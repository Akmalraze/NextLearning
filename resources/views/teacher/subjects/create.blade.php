@extends('layouts.master')

@section('content')
<div style="max-width: 900px; margin: 0 auto; padding: 0 1rem;">
    <!-- Header Section -->
    <div style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); border-radius: 1rem; padding: 2rem; margin-bottom: 2rem; color: white; box-shadow: 0 10px 25px rgba(99, 102, 241, 0.2);">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h1 style="font-size: 2rem; font-weight: 700; margin: 0 0 0.5rem 0; color: white; display: flex; align-items: center; gap: 0.75rem;">
                    <i data-feather="plus-circle" style="width: 32px; height: 32px;"></i>
                    Create New Course
                </h1>
                <p style="margin: 0; opacity: 0.95; font-size: 1rem;">Fill in the details below to create a new course for learners</p>
            </div>
            <a href="{{ route('teacher.subjects.index') }}" style="background: rgba(255,255,255,0.2); color: white; padding: 0.75rem 1.5rem; border-radius: 0.5rem; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem; backdrop-filter: blur(10px); transition: all 0.3s; border: 1px solid rgba(255,255,255,0.3);">
                <i data-feather="arrow-left" style="width: 18px; height: 18px;"></i>
                Back to Courses
            </a>
        </div>
    </div>

    <!-- Form Card -->
    <div style="background: white; border-radius: 1rem; padding: 2rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <form action="{{ route('teacher.subjects.store') }}" method="POST">
            @csrf
            
            @if ($errors->any())
            <div style="background: #fef2f2; border-left: 4px solid #ef4444; border-radius: 0.5rem; padding: 1rem; margin-bottom: 1.5rem;">
                <div style="display: flex; align-items: center; gap: 0.75rem; color: #dc2626; font-weight: 600; margin-bottom: 0.5rem;">
                    <i data-feather="alert-circle" style="width: 20px; height: 20px;"></i>
                    Please fix the following errors:
                </div>
                <ul style="margin: 0; padding-left: 1.5rem; color: #991b1b;">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Course Name -->
            <div style="margin-bottom: 1.5rem;">
                <label for="name" style="display: block; font-weight: 600; color: #1e293b; margin-bottom: 0.5rem; font-size: 0.95rem;">
                    Course Name <span style="color: #ef4444;">*</span>
                </label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       value="{{ old('name') }}" 
                       required
                       placeholder="e.g., Introduction to Mathematics"
                       style="width: 100%; padding: 0.875rem 1rem; border: 2px solid {{ $errors->has('name') ? '#ef4444' : '#e2e8f0' }}; border-radius: 0.5rem; font-size: 1rem; transition: all 0.3s; background: #f8fafc;"
                       onfocus="this.style.borderColor='#6366f1'; this.style.background='white';"
                       onblur="this.style.borderColor='#e2e8f0'; this.style.background='#f8fafc';">
                @error('name')
                <div style="color: #ef4444; font-size: 0.875rem; margin-top: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i data-feather="alert-circle" style="width: 16px; height: 16px;"></i>
                    {{ $message }}
                </div>
                @enderror
            </div>

            <!-- Level (Year/Form) -->
            <div style="margin-bottom: 1.5rem;">
                <label for="code" style="display: block; font-weight: 600; color: #1e293b; margin-bottom: 0.5rem; font-size: 0.95rem;">
                    Level (Year / Form) <span style="color: #ef4444;">*</span>
                </label>
                <select id="code" 
                        name="code" 
                        required
                        style="width: 100%; padding: 0.875rem 1rem; border: 2px solid {{ $errors->has('code') ? '#ef4444' : '#e2e8f0' }}; border-radius: 0.5rem; font-size: 1rem; transition: all 0.3s; background: #f8fafc; cursor: pointer;"
                        onfocus="this.style.borderColor='#6366f1'; this.style.background='white';"
                        onblur="this.style.borderColor='#e2e8f0'; this.style.background='#f8fafc';">
                    <option value="">Select level</option>
                    <option value="Year 1" {{ old('code') === 'Year 1' ? 'selected' : '' }}>Year 1</option>
                    <option value="Year 2" {{ old('code') === 'Year 2' ? 'selected' : '' }}>Year 2</option>
                    <option value="Year 3" {{ old('code') === 'Year 3' ? 'selected' : '' }}>Year 3</option>
                    <option value="Form 1" {{ old('code') === 'Form 1' ? 'selected' : '' }}>Form 1</option>
                    <option value="Form 2" {{ old('code') === 'Form 2' ? 'selected' : '' }}>Form 2</option>
                    <option value="Form 3" {{ old('code') === 'Form 3' ? 'selected' : '' }}>Form 3</option>
                    <option value="Form 4" {{ old('code') === 'Form 4' ? 'selected' : '' }}>Form 4</option>
                    <option value="Form 5" {{ old('code') === 'Form 5' ? 'selected' : '' }}>Form 5</option>
                </select>
                @error('code')
                <div style="color: #ef4444; font-size: 0.875rem; margin-top: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i data-feather="alert-circle" style="width: 16px; height: 16px;"></i>
                    {{ $message }}
                </div>
                @enderror
            </div>

            <!-- Description -->
            <div style="margin-bottom: 1.5rem;">
                <label for="description" style="display: block; font-weight: 600; color: #1e293b; margin-bottom: 0.5rem; font-size: 0.95rem;">
                    Course Description
                </label>
                <textarea id="description" 
                          name="description"
                          rows="4"
                          placeholder="Describe what learners will learn in this course..."
                          style="width: 100%; padding: 0.875rem 1rem; border: 2px solid {{ $errors->has('description') ? '#ef4444' : '#e2e8f0' }}; border-radius: 0.5rem; font-size: 1rem; transition: all 0.3s; background: #f8fafc; resize: vertical; font-family: inherit;"
                          onfocus="this.style.borderColor='#6366f1'; this.style.background='white';"
                          onblur="this.style.borderColor='#e2e8f0'; this.style.background='#f8fafc';">{{ old('description') }}</textarea>
                @error('description')
                <div style="color: #ef4444; font-size: 0.875rem; margin-top: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i data-feather="alert-circle" style="width: 16px; height: 16px;"></i>
                    {{ $message }}
                </div>
                @enderror
            </div>

            <!-- Options Section -->
            <div style="background: #f8fafc; border-radius: 0.75rem; padding: 1.5rem; margin-bottom: 2rem; border: 1px solid #e2e8f0;">
                <h3 style="font-size: 1.1rem; font-weight: 700; color: #1e293b; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i data-feather="settings" style="width: 20px; height: 20px; color: #6366f1;"></i>
                    Course Settings
                </h3>
                
                <!-- Active Checkbox -->
                <div style="display: flex; align-items: center; gap: 0.75rem; padding: 1rem; background: white; border-radius: 0.5rem; margin-bottom: 1rem; border: 1px solid #e2e8f0; transition: all 0.3s;"
                     onmouseover="this.style.borderColor='#cbd5e1';"
                     onmouseout="this.style.borderColor='#e2e8f0';">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" 
                           class="form-check-input" 
                           id="is_active" 
                           name="is_active" 
                           value="1" 
                           {{ old('is_active', true) ? 'checked' : '' }}
                           style="width: 20px; height: 20px; cursor: pointer; accent-color: #6366f1;">
                    <label for="is_active" style="margin: 0; cursor: pointer; flex: 1; font-weight: 500; color: #1e293b;">
                        <div style="font-weight: 600; margin-bottom: 0.25rem;">Active Course</div>
                        <div style="font-size: 0.875rem; color: #64748b;">Course can be managed and content can be added</div>
                    </label>
                </div>

                <!-- Published Checkbox -->
                <div style="display: flex; align-items: center; gap: 0.75rem; padding: 1rem; background: white; border-radius: 0.5rem; border: 1px solid #e2e8f0; transition: all 0.3s;"
                     onmouseover="this.style.borderColor='#cbd5e1';"
                     onmouseout="this.style.borderColor='#e2e8f0';">
                    <input type="hidden" name="is_published" value="0">
                    <input type="checkbox" 
                           class="form-check-input" 
                           id="is_published" 
                           name="is_published" 
                           value="1" 
                           {{ old('is_published', false) ? 'checked' : '' }}
                           style="width: 20px; height: 20px; cursor: pointer; accent-color: #6366f1;">
                    <label for="is_published" style="margin: 0; cursor: pointer; flex: 1; font-weight: 500; color: #1e293b;">
                        <div style="font-weight: 600; margin-bottom: 0.25rem;">Publish Course</div>
                        <div style="font-size: 0.875rem; color: #64748b;">Make course visible in public course catalog for learners to join</div>
                    </label>
                </div>
            </div>

            <!-- Action Buttons -->
            <div style="display: flex; gap: 1rem; justify-content: flex-end; padding-top: 1.5rem; border-top: 1px solid #e2e8f0;">
                <a href="{{ route('teacher.subjects.index') }}" 
                   style="padding: 0.875rem 2rem; background: #f1f5f9; color: #475569; border-radius: 0.5rem; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem; transition: all 0.3s; border: 1px solid #e2e8f0;"
                   onmouseover="this.style.background='#e2e8f0'; this.style.borderColor='#cbd5e1'"
                   onmouseout="this.style.background='#f1f5f9'; this.style.borderColor='#e2e8f0'">
                    <i data-feather="x" style="width: 18px; height: 18px;"></i>
                    Cancel
                </a>
                <button type="submit" 
                        style="padding: 0.875rem 2rem; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border: none; border-radius: 0.5rem; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem; transition: all 0.3s; box-shadow: 0 4px 6px rgba(16, 185, 129, 0.3);"
                        onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 12px rgba(16, 185, 129, 0.4)'"
                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px rgba(16, 185, 129, 0.3)'">
                    <i data-feather="check-circle" style="width: 18px; height: 18px;"></i>
                    Create Course
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    if (typeof feather !== 'undefined') feather.replace();
</script>
@endsection
