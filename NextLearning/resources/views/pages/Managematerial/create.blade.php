<form action="{{ route('materials-store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <input type="hidden" name="subject_id" value="{{ $subject->id }}">

    <div class="mb-3">
        <label for="materials_name">Material Name*</label>
        <input type="text" id="materials_name" name="materials_name" value="{{ old('materials_name') }}"
               class="form-control @error('materials_name') is-invalid @enderror" required>
        @error('materials_name')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>

    <div class="mb-3">
        <label for="file">Upload Material (PDF, Doc, etc.)*</label>
        <input type="file" id="file" name="file" class="form-control @error('file') is-invalid @enderror" required>
        @error('file')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>

    <div class="mb-3">
        <label for="materials_notes">Material Notes</label>
        <textarea id="materials_notes" name="materials_notes"
                  class="form-control @error('materials_notes') is-invalid @enderror">{{ old('materials_notes') }}</textarea>
        @error('materials_notes')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>

    <button class="btn btn-primary" type="submit">Create Material</button>
</form>
