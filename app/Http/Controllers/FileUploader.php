<?php

namespace App\Http\Controllers;

use App\Jobs\JobCommand;
use App\Models\Setting;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Http;
use Jenssegers\ImageHash\ImageHash;
use Jenssegers\ImageHash\Implementations\DifferenceHash;


class FileUploader extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function upload(Request $request)
    {
        if ($request->file('files')) {
            foreach ($request->file('files') as $key => $file) {
                $file_name = time() . rand(1, 99) . '.' . $file->extension();
                $original_name = $file->getClientOriginalName();
                $file->move(public_path('uploads'), $file_name);
                $path = 'uploads/' . $file_name;
                $files[] = ['path' => $path, 'file_name' => $original_name];
            }
        }

        $hasher = new ImageHash(new DifferenceHash());

        foreach ($files as $key => $file) {
            $hash = $hasher->hash(public_path($file['path']));

            \App\Models\Image::create([
                'img_path' => $file['path'],
                'file_name' => $file['file_name'],
                "category_id" => $request->category_id,
                'hash' => $hash,
                'unique_number' => rand(10000000, 99999999) . round(microtime(true) * 1000) . '.jpg'
            ]);

        }

        return back()->with('success', 'Файлы успешны загружены');
    }


    public function compare(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $hasher = new ImageHash(new DifferenceHash());
            $hashToCompare = $hasher->hash($file->get());

            $file_name = time() . rand(1, 99);
            $file->move(public_path('uploads'), $file_name);
            $path = 'uploads/' . $file_name;


            $similarImages = [];

            $databaseHashes = \App\Models\Image::where([
                ['category_id', $request->category_id],
                ['hash', '!=', '']
            ])->get()->toArray();

            foreach ($databaseHashes as $databaseHash) {
                $distance = $hasher->distance($hashToCompare, $databaseHash['hash']);
                $digit = 0.1;

                $percentSimilarity = ((1 - $distance / 35) * 100) - $digit;

                if ($percentSimilarity > 10) {
                    $similarImages[] = [
                        'img' => $databaseHash['img_path'],
                        'file_name' => $databaseHash['file_name'],
                        'percent' => $percentSimilarity
                    ];
                }
            }

            usort($similarImages, function ($a, $b) {
                return $b['percent'] <=> $a['percent'];
            });


            return view('welcome', ['images' => $similarImages, 'image' => $path]);
        }

        return view('welcome');
    }



    public function change()
    {
        JobCommand::dispatch();
    }
}

