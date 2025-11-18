<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $banners = Banner::when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('title', 'like', "%{$q}%")
                        ->orWhere('subtitle', 'like', "%{$q}%");
                });
            })
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->paginate(12)
            ->appends(['q' => $q]);

        return view('admin.banners.index', compact('banners', 'q'));
    }

    public function create()
    {
        $banner = new Banner([
            'sort_order' => 0,
            'is_active' => true,
        ]);

        return view('admin.banners.create', compact('banner'));
    }

    public function store(Request $request, string $locale)
    {
        $data = $this->validatedData($request, true);

        $data['image_path'] = $request->file('image')->store('banners', 'public');

        Banner::create($data);

        return redirect()
            ->route('admin.banners.index', ['locale' => $locale])
            ->with('status', __('Banner created successfully.'));
    }

    public function edit(string $locale, Banner $banner)
    {
        return view('admin.banners.edit', compact('banner'));
    }

    public function update(Request $request, string $locale, Banner $banner)
    {
        $data = $this->validatedData($request, false);

        if ($request->hasFile('image')) {
            $newPath = $request->file('image')->store('banners', 'public');
            $this->deleteImage($banner->image_path);
            $data['image_path'] = $newPath;
        }

        $banner->update($data);

        return redirect()
            ->route('admin.banners.index', ['locale' => $locale])
            ->with('status', __('Banner updated successfully.'));
    }

    public function destroy(string $locale, Banner $banner)
    {
        $this->deleteImage($banner->image_path);
        $banner->delete();

        return redirect()
            ->route('admin.banners.index', ['locale' => $locale])
            ->with('status', __('Banner deleted successfully.'));
    }

    protected function validatedData(Request $request, bool $requireImage = false): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string'],
            'image' => [$requireImage ? 'required' : 'nullable', 'image', 'max:2048'],
            'link_url' => ['nullable', 'url', 'max:255'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_active'] = $request->boolean('is_active', true);

        unset($data['image']);

        return $data;
    }

    protected function deleteImage(?string $path): void
    {
        if (! $path) {
            return;
        }

        Storage::disk('public')->delete($path);
    }
}
