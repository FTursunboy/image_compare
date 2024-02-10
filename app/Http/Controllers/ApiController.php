<?php

namespace App\Http\Controllers;

use App\Jobs\JobCommand;
use App\Models\Setting;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Jenssegers\ImageHash\ImageHash;
use Jenssegers\ImageHash\Implementations\DifferenceHash;


class ApiController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function index()
    {
        JobCommand::dispatch();
    }


    public function upload(Request $request) {
        if ($request->file('files')) {
            foreach ($request->file('files') as $key => $file) {

                $file_name = time() . rand(1, 99) . '.' . $file->extension();
                $original_name = $file->getClientOriginalName();
                $file->move(public_path('uploads'), $file_name);
                $path = 'uploads/' . $file_name;
                $files[] = ['path' => $path, 'file_name' => $original_name];
            }
        }

        foreach ($files as $key => $file) {

            \App\Models\Image::create([
                'img_path' => $file['path'],
                'file_name' => $file['file_name'],
                'category_id' => $request->category_id,
                'sent' => true
            ]);
        }


    }

}

