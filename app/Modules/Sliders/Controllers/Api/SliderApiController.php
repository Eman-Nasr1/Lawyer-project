<?php

namespace App\Modules\Sliders\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Slider;

class SliderApiController extends Controller
{
    public function index()
    {
        $sliders = Slider::active()
            ->ordered()
            ->select('title', 'description', 'image', 'link_url')
            ->get()
            ->map(function ($slider) {
                return [
                    'title' => $slider->title,
                    'description' => $slider->description,
                    'image_url' => $slider->image_url,
                    'link_url' => $slider->link_url,
                ];
            });

        return response()->json([
            'status' => true,
            'data' => $sliders
        ]);
    }
}

