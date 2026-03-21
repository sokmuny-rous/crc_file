<x-layouts.app>
    <style>
        .hero-slide { height: 320px; border-radius: 18px; overflow: hidden; }
        .hero-slide .overlay { background: linear-gradient(90deg, rgba(0, 0, 0, .70), rgba(0, 0, 0, .15)); }
        .section-title i { color: #ef4444; }
        .doc-card { border: 1px solid #e5e7eb; border-radius: 14px; overflow: hidden; background: #fff; }
        .doc-cover { height: 210px; background-size: cover; background-position: center; position: relative; }
        .doc-cover::after { content: ''; position: absolute; inset: 0; background: rgba(255, 255, 255, .68); }
        .doc-tag { position: absolute; left: 14px; bottom: 12px; z-index: 2; }
        .doc-ext { position: absolute; right: 12px; top: 12px; z-index: 2; }
        .doc-main { padding: 14px; }
        .meta { color: #6b7280; font-size: .86rem; }
    </style>

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <h4 class="mb-0 fw-bold">Documents Dashboard</h4>
        @if(in_array(auth()->user()->role, ['editor', 'admin'], true))
            <a href="{{ route('files.create') }}" class="btn btn-danger">
                <i class="bi bi-cloud-upload me-1"></i> Upload Document
            </a>
        @endif
    </div>

    <form method="GET" class="surface p-3 mb-4">
        <div class="row g-2 align-items-center">
            <div class="col-lg-8">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search documents...">
                </div>
            </div>
            <div class="col-lg-3">
                <select name="category" class="form-select">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected((int) request('category') === $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-1 d-grid">
                <button class="btn btn-outline-secondary">Go</button>
            </div>
        </div>
    </form>

    @if($featuredFiles->isNotEmpty())
        <div id="featureCarousel" class="carousel slide mb-4" data-bs-ride="carousel">
            <div class="carousel-inner hero-slide surface">
                @foreach($featuredFiles as $index => $file)
                    <div class="carousel-item h-100 @if($index === 0) active @endif">
                        <div class="h-100 position-relative" style="background-image:url('{{ route('files.thumbnail', $file) }}'); background-size:cover; background-position:center;">
                            <div class="overlay position-absolute w-100 h-100 text-white d-flex flex-column justify-content-end p-4">
                                <span class="badge rounded-pill text-bg-danger mb-2" style="width: fit-content;">{{ $file->created_at?->format('F Y') }}</span>
                                <h2 class="h3 fw-bold">{{ $file->title }}</h2>
                                <p class="mb-2">{{ \Illuminate\Support\Str::limit($file->description ?? $file->original_name, 100) }}</p>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('files.preview', $file) }}" class="btn btn-light btn-sm">View</a>
                                    <a href="{{ route('files.download', $file) }}" class="btn btn-danger btn-sm">Download</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#featureCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#featureCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>
    @endif

    <section class="mb-5">
        <h4 class="section-title fw-bold mb-3"><i class="bi bi-calendar2-event me-2"></i>Recently Uploaded Documents</h4>
        <div class="row g-3">
            @forelse($recentFiles as $file)
                <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                    <div class="doc-card h-100">
                        <div class="doc-cover" style="background-image: url('{{ route('files.thumbnail', $file) }}')">
                            <span class="badge bg-danger doc-tag">{{ $file->category?->name ?? 'General' }}</span>
                            <span class="badge text-bg-light doc-ext">{{ strtoupper(pathinfo($file->original_name, PATHINFO_EXTENSION) ?: 'FILE') }}</span>
                        </div>
                        <div class="doc-main">
                            <div class="fw-semibold mb-2">{{ \Illuminate\Support\Str::limit($file->title, 45) }}</div>
                            <div class="meta mb-2"><i class="bi bi-buildings me-1"></i>{{ $file->category?->name ?? 'Uncategorized' }}</div>
                            <div class="meta mb-2"><i class="bi bi-calendar3 me-1"></i>{{ $file->created_at?->format('M j, Y') }}</div>
                            <div class="meta mb-3"><i class="bi bi-person me-1"></i>{{ $file->uploader?->name ?? '-' }}</div>

                            <div class="d-flex gap-2">
                                <a href="{{ route('files.preview', $file) }}" class="btn btn-light btn-sm flex-fill"><i class="bi bi-eye me-1"></i>View</a>
                                <a href="{{ route('files.download', $file) }}" class="btn btn-danger btn-sm flex-fill"><i class="bi bi-download me-1"></i>Download</a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="surface p-4 text-center text-secondary">No uploaded documents yet.</div>
                </div>
            @endforelse
        </div>
    </section>

    @foreach($departmentSections as $category)
        <section class="mb-5">
            <h3 class="section-title fw-bold mb-3"><i class="bi bi-building me-2"></i>{{ $category->name }} Department</h3>
            <div class="row g-3">
                @foreach($category->files as $file)
                    <div class="col-xl-3 col-lg-4 col-sm-6">
                        <div class="doc-card h-100">
                            <div class="doc-cover" style="background-image: url('{{ route('files.thumbnail', $file) }}')">
                                <span class="badge bg-danger doc-tag">{{ $file->category?->name ?? 'General' }}</span>
                                <span class="badge text-bg-light doc-ext">{{ strtoupper(pathinfo($file->original_name, PATHINFO_EXTENSION) ?: 'FILE') }}</span>
                            </div>
                            <div class="doc-main">
                                <div class="fw-semibold mb-2">{{ \Illuminate\Support\Str::limit($file->title, 50) }}</div>
                                <div class="meta mb-1"><i class="bi bi-calendar3 me-1"></i>{{ $file->created_at?->format('M j, Y') }}</div>
                                <div class="meta mb-3"><i class="bi bi-download me-1"></i>{{ $file->file_size ? number_format($file->file_size / 1024, 0).' KB' : '-' }}</div>

                                <div class="d-flex gap-2">
                                    <a href="{{ route('files.preview', $file) }}" class="btn btn-light btn-sm flex-fill"><i class="bi bi-eye me-1"></i>View</a>
                                    <a href="{{ route('files.download', $file) }}" class="btn btn-danger btn-sm flex-fill"><i class="bi bi-download me-1"></i>Download</a>
                                </div>

                                @if(in_array(auth()->user()->role, ['editor', 'admin'], true))
                                    <div class="d-flex gap-2 mt-2">
                                        <a href="{{ route('files.edit', $file) }}" class="btn btn-outline-secondary btn-sm flex-fill">Edit</a>
                                        <form action="{{ route('files.destroy', $file) }}" method="POST" class="flex-fill" onsubmit="return confirm('Delete this file?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-outline-danger btn-sm w-100" type="submit">Delete</button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endforeach

    <section>
        <h4 class="fw-bold mb-3">All Documents</h4>
        <div class="surface p-3">
            <div class="row g-3">
                @forelse($files as $file)
                    <div class="col-lg-4 col-md-6">
                        <div class="border rounded-3 p-3 h-100 bg-white">
                            <div class="d-flex justify-content-between align-items-start gap-2">
                                <div>
                                    <div class="fw-semibold">{{ $file->title }}</div>
                                    <div class="small text-secondary">{{ $file->original_name }}</div>
                                </div>
                                <span class="badge text-bg-light">{{ strtoupper(pathinfo($file->original_name, PATHINFO_EXTENSION) ?: 'FILE') }}</span>
                            </div>
                            <div class="small text-secondary mt-2">{{ $file->category?->name ?? 'Uncategorized' }} • {{ $file->created_at?->format('M j, Y') }}</div>
                            <div class="d-flex gap-2 mt-3">
                                <a href="{{ route('files.preview', $file) }}" class="btn btn-light btn-sm flex-fill">View</a>
                                <a href="{{ route('files.download', $file) }}" class="btn btn-danger btn-sm flex-fill">Download</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center text-secondary py-4">No files found.</div>
                @endforelse
            </div>

            <div class="mt-3">{{ $files->links() }}</div>
        </div>
    </section>
</x-layouts.app>
