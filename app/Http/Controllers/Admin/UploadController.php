<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;

class UploadController extends Controller
{
    public function ckeditor(Request $request)
    {
        $request->validate([
            'upload' => 'required|file|image|max:4096', // <= 4MB
        ]);

        $path = $request->file('upload')->store('ckeditor', 'public');

        return response()->json([
            'url' => Storage::url($path),
        ]);
    }
}
