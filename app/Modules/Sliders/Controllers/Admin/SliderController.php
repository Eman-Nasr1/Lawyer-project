<?php

namespace App\Modules\Sliders\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use App\Modules\Sliders\Requests\SliderRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SliderController extends Controller
{
    public function index(Request $request)
    {
        $query = Slider::query()->orderBy('sort_order')->orderBy('id', 'desc');

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $sliders = $query->paginate((int)$request->get('per_page', 12));

        return view('admin.sliders.index', [
            'sliders' => $sliders,
            'search' => $request->get('search'),
        ]);
    }

    public function create()
    {
        return view('admin.sliders.create');
    }

    public function store(SliderRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            
            // Store the file using the 'public' disk explicitly
            Storage::disk('public')->putFileAs('sliders', $file, $filename);
            $data['image'] = $filename;
        }

        Slider::create($data);

        return redirect()->route('admin.sliders.index')->with('success', 'Slider created successfully');
    }

    public function edit(Slider $slider)
    {
        return view('admin.sliders.edit', compact('slider'));
    }

    public function update(SliderRequest $request, Slider $slider)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            // Delete old image
            if ($slider->image) {
                Storage::disk('public')->delete('sliders/' . $slider->image);
            }
            
            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            
            // Store the file using the 'public' disk explicitly
            Storage::disk('public')->putFileAs('sliders', $file, $filename);
            $data['image'] = $filename;
        }

        $slider->update($data);

        return redirect()->route('admin.sliders.index')->with('success', 'Slider updated successfully');
    }

    public function destroy(Slider $slider)
    {
        if ($slider->image) {
            Storage::disk('public')->delete('sliders/' . $slider->image);
        }
        $slider->delete();

        return back()->with('success', 'Slider deleted successfully');
    }
}

