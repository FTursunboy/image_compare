<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Jenssegers\ImageHash\ImageHash;
use Jenssegers\ImageHash\Implementations\DifferenceHash;


class FileUploader extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function upload(Request $request)
    {
        $files = [];

        if ($request->file('files')) {
            foreach ($request->file('files') as $key => $file) {
                $file_name = time() . rand(1, 99) . '.' . $file->extension();
                $file->move(public_path('uploads'), $file_name);
                $path = 'uploads/' . $file_name;
                $files[] = ['path' => $path];
            }
        }

        $hasher = new ImageHash(new DifferenceHash());

        foreach ($files as $key => $file) {
            $hash = $hasher->hash(public_path($file['path']));

            \App\Models\Image::create([
                'img_path' => $file['path'],
                'hash' =>  $hash,
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

                $threshold = Setting::first()->percent;
                $similarImages = [];

                $databaseHashes = \App\Models\Image::get()->toArray();


                foreach ($databaseHashes as $databaseHash) {

                    $distance = $hasher->distance($hashToCompare, $databaseHash['hash']);
                    $percentSimilarity = (1 - $distance / 35) * 100;

                    if ($percentSimilarity > $threshold) {
                        $similarImages[] = [
                            'img' => $databaseHash['img_path'],
                            'percent' => $percentSimilarity
                        ];
                    }
                }

                usort($similarImages, function ($a, $b) {
                    return $b['percent'] <=> $a['percent'];
                });
                return view('welcome', ['images' => $similarImages]);
            }

            return view('welcome');
        }


    public function setting(Request $request) {
        $s = Setting::first();

        $s->percent = $request->percent;
        $s->save();

        return redirect()->route('welcome');
    }


}
