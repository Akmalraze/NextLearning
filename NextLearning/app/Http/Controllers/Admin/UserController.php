<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Classes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    /**
     * Display a listing of users with tabs for Students/Teachers/Admins
     */
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'students');
        $search = $request->get('search');
        $status = $request->get('status');

        $query = User::with(['roles']);

        // Filter by role based on tab
        switch ($tab) {
            case 'teachers':
                $query->role('Teacher');
                break;
            case 'admins':
                $query->role('Admin');
                break;
            default:
                $query->role('Student');
                break;
        }

        // Search filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('id_number', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($status !== null && $status !== '') {
            $query->where('status', $status);
        }

        $users = $query->latest()->paginate(15)->withQueryString();
        $classes = Classes::all();

        return view('admin.users.index', compact('users', 'tab', 'search', 'status', 'classes'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        abort_if(Gate::denies('create users'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $roles = Role::all();
        $classes = Classes::all();

        return view('admin.users.create', compact('roles', 'classes'));
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        abort_if(Gate::denies('create users'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'id_number' => 'nullable|string|unique:users,id_number|max:50',
            'password' => 'required|string|min:8',
            'role' => 'required|exists:roles,name',
            'class_id' => 'nullable|exists:classes,id',
        ]);

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'id_number' => $validatedData['id_number'],
            'password' => Hash::make($validatedData['password']),
            'status' => 1,
        ]);

        $user->assignRole($validatedData['role']);

        // If student and class selected, enroll them
        if ($validatedData['role'] === 'Student' && !empty($validatedData['class_id'])) {
            ClassStudent::create([
                'class_id' => $validatedData['class_id'],
                'student_id' => $user->id,
                'enrollment_date' => now(),
                'status' => 'active',
            ]);
        }

        flash()->addSuccess('User created successfully.');
        return redirect()->route('admin.users.index');
    }

    /**
     * Show the form for editing a user
     */
    public function edit($id)
    {
        abort_if(Gate::denies('edit users'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user = User::with(['roles', 'enrollments'])->findOrFail($id);
        $roles = Role::all();
        $classes = Classes::all();
        $currentClass = $user->activeClass()->first();

        return view('admin.users.edit', compact('user', 'roles', 'classes', 'currentClass'));
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, $id)
    {
        abort_if(Gate::denies('edit users'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user = User::findOrFail($id);

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'id_number' => 'nullable|string|max:50|unique:users,id_number,' . $id,
            'password' => 'nullable|string|min:8',
            'role' => 'required|exists:roles,name',
            'class_id' => 'nullable|exists:classes,id',
        ]);

        $user->update([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'id_number' => $validatedData['id_number'],
        ]);

        if (!empty($validatedData['password'])) {
            $user->update(['password' => Hash::make($validatedData['password'])]);
        }

        // Sync role
        $user->syncRoles([$validatedData['role']]);

        // Handle class enrollment for students
        if ($validatedData['role'] === 'Student' && !empty($validatedData['class_id'])) {
            // Mark old enrollment as transferred
            ClassStudent::where('student_id', $user->id)
                ->where('status', 'active')
                ->update(['status' => 'transferred']);

            // Create new enrollment
            ClassStudent::create([
                'class_id' => $validatedData['class_id'],
                'student_id' => $user->id,
                'enrollment_date' => now(),
                'status' => 'active',
            ]);
        }

        flash()->addSuccess('User updated successfully.');
        return redirect()->route('admin.users.index');
    }

    /**
     * Deactivate/Activate user
     */
    public function destroy($id)
    {
        abort_if(Gate::denies('delete users'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user = User::findOrFail($id);
        $user->update(['status' => 0]);

        flash()->addSuccess('User deactivated successfully.');
        return redirect()->route('admin.users.index');
    }

    /**
     * Toggle user status (ban/unban)
     */
    public function toggleStatus($id, $status)
    {
        abort_if(!auth()->user()->hasRole('Admin'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user = User::findOrFail($id);
        $user->status = $status;

        if ($user->save()) {
            flash()->addSuccess('User status updated successfully.');
            return redirect()->back();
        }

        flash()->addError('User status update failed!');
        return redirect()->back();
    }

    /**
     * Show bulk create form
     */
    public function bulkCreate()
    {
        abort_if(Gate::denies('create users'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $roles = Role::all();
        $classes = Classes::all();

        return view('admin.users.bulk-create', compact('roles', 'classes'));
    }

    /**
     * Store bulk created users
     */
    public function bulkStore(Request $request)
    {
        abort_if(Gate::denies('create users'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'role' => 'required|exists:roles,name',
            'users_data' => 'required|string',
        ]);

        $role = $request->role;
        $classId = $request->class_id;
        $usersData = $request->users_data;

        // Parse the text data (one user per line)
        $lines = array_filter(array_map('trim', explode("\n", $usersData)));

        if (empty($lines)) {
            return back()->withErrors(['users_data' => 'No valid user data provided.'])->withInput();
        }

        $createdCount = 0;
        $errors = [];

        foreach ($lines as $index => $line) {
            $parts = array_map('trim', explode(',', $line));

            if (count($parts) < 3) {
                $errors[] = "Line " . ($index + 1) . ": Missing required fields (Full Name, Email, Password)";
                continue;
            }

            $fullName = $parts[0];
            $email = $parts[1];
            $password = $parts[2];
            $idNumber = $parts[3] ?? null;

            // Validate password strength
            if (strlen($password) < 8) {
                $errors[] = "Line " . ($index + 1) . ": Password must be at least 8 characters";
                continue;
            }

            // Validate email format
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Line " . ($index + 1) . ": Invalid email format '{$email}'";
                continue;
            }

            // Check if email already exists
            if (User::where('email', $email)->exists()) {
                $errors[] = "Line " . ($index + 1) . ": Email '{$email}' already exists";
                continue;
            }

            $user = User::create([
                'name' => $fullName,
                'email' => $email,
                'id_number' => $idNumber,
                'password' => Hash::make($password),
                'status' => 1,
            ]);

            $user->assignRole($role);

            if ($role === 'Student' && !empty($classId)) {
                ClassStudent::create([
                    'class_id' => $classId,
                    'student_id' => $user->id,
                    'enrollment_date' => now(),
                    'status' => 'active',
                ]);
            }

            $createdCount++;
        }

        if (!empty($errors)) {
            flash()->addWarning("{$createdCount} users created. Some entries had errors.");
            return back()->withErrors($errors)->withInput();
        }

        flash()->addSuccess("{$createdCount} users created successfully.");
        return redirect()->route('admin.users.index');
    }
}
