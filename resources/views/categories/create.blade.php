<x-layouts.app>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Create Category</h3>
        <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">Back</a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form action="{{ route('categories.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Create</button>
            </form>
        </div>
    </div>
</x-layouts.app>
