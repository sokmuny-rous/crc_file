<x-layouts.app>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Edit File</h3>
        <a href="{{ route('files.index') }}" class="btn btn-outline-secondary">Back</a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('files.update', $sharedFile) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title', $sharedFile->title) }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Category</label>
                    <select name="category_id" class="form-select">
                        <option value="">No category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected((int) old('category_id', $sharedFile->category_id) === $category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="4">{{ old('description', $sharedFile->description) }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Replace File (optional)</label>
                    <input type="file" name="file" class="form-control">
                    <div class="form-text">Current: {{ $sharedFile->original_name }}</div>
                </div>
                <button class="btn btn-primary" type="submit">Save Changes</button>
            </form>
        </div>
    </div>
</x-layouts.app>
