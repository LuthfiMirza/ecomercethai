<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->query('q', ''));

        $banners = Banner::when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('title', 'like', "%$q%")
                        ->orWhere('placement', 'like', "%$q%");
                });
            })
            ->orderByDesc('priority')
            ->orderByDesc('created_at')
            ->paginate(12)
            ->appends(['q' => $q]);

        return view('admin.banners.index', compact('banners', 'q'));
    }

    public function create()
    {
        $placements = ['homepage_top','homepage_sidebar','homepage_bottom'];

        return view('admin.banners.create', compact('placements'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'image_path' => 'required|string|max:1024',
            'link_url' => 'nullable|url',
            'placement' => 'required|string',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'is_active' => 'sometimes|boolean',
            'priority' => 'nullable|integer',
        ]);

        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        Banner::create($data);

        return redirect()->route('admin.banners.index')->with('status', 'Banner created');
    }

    public function show(string $id)
    {
        return redirect()->route('admin.banners.edit', $id);
    }

    public function edit(string $id)
    {
        $banner = Banner::findOrFail($id);
        $placements = ['homepage_top','homepage_sidebar','homepage_bottom'];

        return view('admin.banners.edit', compact('banner', 'placements'));
    }

    public function update(Request $request, string $id)
    {
        $banner = Banner::findOrFail($id);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'image_path' => 'required|string|max:1024',
            'link_url' => 'nullable|url',
            'placement' => 'required|string',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'is_active' => 'sometimes|boolean',
            'priority' => 'nullable|integer',
        ]);

        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        $banner->update($data);

        return redirect()->route('admin.banners.index')->with('status', 'Banner updated');
    }

    public function destroy(string $id)
    {
        $banner = Banner::findOrFail($id);
        $banner->delete();

        return back()->with('status', 'Banner deleted');
    }
}
