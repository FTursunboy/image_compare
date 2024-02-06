<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Jenssegers\ImageHash\ImageHash;
use Jenssegers\ImageHash\Implementations\DifferenceHash;


class FileUploader extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    // Контроллер для обработки загрузки файлов

    public function upload(Request $request)
    {
        $files = [];

        if ($request->file('files')) {
            foreach ($request->file('files') as $key => $file) {
                $file_name = time() . rand(1, 99) . '.' . $file->extension();
                $file->move(public_path('uploads'), $file_name);
                $path = 'uploads/' . $file_name;
                $files[] = ['name' => $file_name, 'path' => $path];
            }
        }

        $hasher = new ImageHash(new DifferenceHash());

        foreach ($files as $key => $file) {
            \App\Models\Image::create([
                'img_url' => $file['path'], // Fix: Accessing 'path' from array, not object
                'hash' =>  $hasher->hash(public_path($file['path'])), // Fix: Passing correct path
            ]);
        }

        return back()->with('success', 'File uploaded successfully');
    }

}
