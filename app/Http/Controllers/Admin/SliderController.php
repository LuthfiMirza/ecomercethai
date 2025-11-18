<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SliderController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $sliders = Slider::when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('title', 'like', "%{$q}%")
                        ->orWhere('subtitle', 'like', "%{$q}%");
                });
            })
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->paginate(12)
            ->appends(['q' => $q]);

        return view('admin.sliders.index', compact('sliders', 'q'));
    }

    public function create()
    {
        $slider = new Slider([
            'sort_order' => 0,
            'is_active' => true,
        ]);

        return view('admin.sliders.create', compact('slider'));
    }

    public function store(Request $request, string $locale)
    {
        $data = $this->validatedData($request, true);
        $data['image_path'] = $request->file('image')->store('sliders', 'public');

        Slider::create($data);

        return redirect()
            ->route('admin.sliders.index', ['locale' => $locale])
            ->with('status', __('Slider created successfully.'));
    }

    public function edit(string $locale, Slider $slider)
    {
        return view('admin.sliders.edit', compact('slider'));
    }

    public function update(Request $request, string $locale, Slider $slider)
    {
        $data = $this->validatedData($request, false);

        if ($request->hasFile('image')) {
            $newPath = $request->file('image')->store('sliders', 'public');
            $this->deleteImage($slider->image_path);
            $data['image_path'] = $newPath;
        }

        $slider->update($data);

        return redirect()
            ->route('admin.sliders.index', ['locale' => $locale])
            ->with('status', __('Slider updated successfully.'));
    }

    public function destroy(string $locale, Slider $slider)
    {
        $this->deleteImage($slider->image_path);
        $slider->delete();

        return redirect()
            ->route('admin.sliders.index', ['locale' => $locale])
            ->with('status', __('Slider deleted successfully.'));
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
