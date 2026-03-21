<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\SharedFile;
use App\Services\FileThumbnailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileController extends Controller
{
    public function index(Request $request): View
    {
        $query = SharedFile::query()->with(['category', 'uploader']);

        if ($request->filled('q')) {
            $query->where(function ($inner) use ($request): void {
                $inner->where('title', 'like', '%'.$request->string('q').'%')
                    ->orWhere('description', 'like', '%'.$request->string('q').'%')
                    ->orWhere('original_name', 'like', '%'.$request->string('q').'%');
            });
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->integer('category'));
        }

        $files = $query->latest()->paginate(12)->withQueryString();
        $categories = Category::query()->orderBy('name')->get();
        $featuredFiles = SharedFile::query()
            ->with(['category', 'uploader'])
            ->latest()
            ->take(3)
            ->get();

        $recentFiles = SharedFile::query()
            ->with(['category', 'uploader'])
            ->latest()
            ->take(6)
            ->get();

        $departmentSections = Category::query()
            ->with([
                'files' => fn ($q) => $q->with(['category', 'uploader'])->latest()->take(4),
            ])
            ->orderBy('name')
            ->get()
            ->filter(fn (Category $category) => $category->files->isNotEmpty())
            ->values();

        return view('files.index', compact('files', 'categories', 'featuredFiles', 'recentFiles', 'departmentSections'));
    }

    public function create(): View
    {
        $categories = Category::query()->orderBy('name')->get();

        return view('files.create', compact('categories'));
    }

    public function store(Request $request, FileThumbnailService $thumbnailService): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'file' => ['required', 'file', 'max:20480'],
        ]);

        $uploaded = $request->file('file');
        $path = $uploaded->store('uploads', 'local');
        $thumbnailPath = $thumbnailService->generateFromFirstPage($path, $uploaded->getClientMimeType());

        SharedFile::query()->create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'category_id' => $data['category_id'] ?? null,
            'file_path' => $path,
            'original_name' => $uploaded->getClientOriginalName(),
            'mime_type' => $uploaded->getClientMimeType(),
            'file_size' => $uploaded->getSize(),
            'thumbnail_path' => $thumbnailPath,
            'uploaded_by' => $request->user()->id,
        ]);

        return redirect()->route('files.index')->with('success', 'File uploaded successfully.');
    }

    public function edit(SharedFile $sharedFile): View
    {
        $categories = Category::query()->orderBy('name')->get();

        return view('files.edit', compact('sharedFile', 'categories'));
    }

    public function update(Request $request, SharedFile $sharedFile, FileThumbnailService $thumbnailService): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'file' => ['nullable', 'file', 'max:20480'],
        ]);

        $payload = [
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'category_id' => $data['category_id'] ?? null,
        ];

        if ($request->hasFile('file')) {
            Storage::disk('local')->delete($sharedFile->file_path);

            if ($sharedFile->thumbnail_path) {
                Storage::disk('local')->delete($sharedFile->thumbnail_path);
            }

            $uploaded = $request->file('file');
            $payload['file_path'] = $uploaded->store('uploads', 'local');
            $payload['original_name'] = $uploaded->getClientOriginalName();
            $payload['mime_type'] = $uploaded->getClientMimeType();
            $payload['file_size'] = $uploaded->getSize();
            $payload['thumbnail_path'] = $thumbnailService->generateFromFirstPage($payload['file_path'], $payload['mime_type']);
        }

        $sharedFile->update($payload);

        return redirect()->route('files.index')->with('success', 'File updated successfully.');
    }

    public function destroy(SharedFile $sharedFile): RedirectResponse
    {
        Storage::disk('local')->delete($sharedFile->file_path);

        if ($sharedFile->thumbnail_path) {
            Storage::disk('local')->delete($sharedFile->thumbnail_path);
        }

        $sharedFile->delete();

        return redirect()->route('files.index')->with('success', 'File deleted successfully.');
    }

    public function download(SharedFile $sharedFile): StreamedResponse
    {
        return Storage::disk('local')->download($sharedFile->file_path, $sharedFile->original_name);
    }

    public function preview(SharedFile $sharedFile): BinaryFileResponse
    {
        if (! Storage::disk('local')->exists($sharedFile->file_path)) {
            abort(404, 'File not found.');
        }

        return response()->file(Storage::disk('local')->path($sharedFile->file_path), [
            'Content-Type' => $sharedFile->mime_type ?? 'application/octet-stream',
            'Content-Disposition' => 'inline; filename="'.$sharedFile->original_name.'"',
        ]);
    }

    public function thumbnail(SharedFile $sharedFile): BinaryFileResponse|Response
    {
        if ($sharedFile->thumbnail_path && Storage::disk('local')->exists($sharedFile->thumbnail_path)) {
            return response()->file(Storage::disk('local')->path($sharedFile->thumbnail_path), [
                'Content-Type' => 'image/jpeg',
                'Content-Disposition' => 'inline; filename="thumb-'.$sharedFile->id.'.jpg"',
            ]);
        }

        $extension = strtoupper(pathinfo($sharedFile->original_name, PATHINFO_EXTENSION) ?: 'FILE');
        $title = e($sharedFile->title);

        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="900" height="540" viewBox="0 0 900 540">
  <defs>
    <linearGradient id="bg" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="#f3f4f6"/>
      <stop offset="100%" stop-color="#e5e7eb"/>
    </linearGradient>
  </defs>
  <rect width="100%" height="100%" fill="url(#bg)"/>
  <rect x="65" y="50" width="770" height="440" rx="18" fill="#ffffff" stroke="#d1d5db"/>
  <rect x="95" y="86" width="124" height="40" rx="20" fill="#ef4444"/>
  <text x="157" y="112" text-anchor="middle" fill="#fff" font-family="Arial, sans-serif" font-size="18" font-weight="700">{$extension}</text>
  <text x="95" y="188" fill="#111827" font-family="Arial, sans-serif" font-size="34" font-weight="700">{$title}</text>
  <text x="95" y="230" fill="#6b7280" font-family="Arial, sans-serif" font-size="20">Preview unavailable for this file type</text>
</svg>
SVG;

        return response($svg, 200, [
            'Content-Type' => 'image/svg+xml',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}
