<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Image;

class ImageController extends Controller
{
    public function index()
    {
        return view('images.index');
    }

    public function show()
    {
        // return all images
        return Image::latest()->pluck('name')->toArray();
    }

    public function store(Request $request)
    {
        // validate the incoming file
        if (!$request->hasFile('image')) {
            return response()->json(['error' => 'There is no image present'], 400);
        }

        $request->validate([
            'image' => 'required|file|image|mimes:jpg,jpeg,png'
        ]);

        // save the file in storage
        $path = $request->file('image')->store('public/images');

        if (!$path) {
            return response()->json(['error', 'The file could not be saved.'], 500);
        }

        $uploadedFile = $request->file('image');

        // create image model
        $image = Image::create([
            'name' => $uploadedFile->hashName(),
            'extension' => $uploadedFile->extension(),
            'size' => $uploadedFile->getSize(),
        ]);

        // return that image model back to the front end
        return $image->name;
    }
}
