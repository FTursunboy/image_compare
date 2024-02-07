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

            $similarImages = [];

            $databaseHashes = \App\Models\Image::where('category_id', $request->category_id)->get()->toArray();


            foreach ($databaseHashes as $databaseHash) {
                $distance = $hasher->distance($hashToCompare, $databaseHash['hash']);
                $digit = mt_rand(1, 2) / 100;

                $percentSimilarity = ((1 - $distance / 35) * 100) - $digit;



                $similarImages[] = [
                    'img' => $databaseHash['img_path'],
                    'file_name' => $databaseHash['file_name'],
                    'percent' => $percentSimilarity
                ];
            }

            usort($similarImages, function ($a, $b) {
                return $b['percent'] <=> $a['percent'];
            });


            return view('welcome', ['images' => $similarImages, 'hash' => $hashToCompare]);
        }

        return view('welcome');
    }


    public function setting(Request $request)
    {
        $s = Setting::first();

        $s->percent = $request->percent;
        $s->save();

        return redirect()->route('welcome');
    }


    public function cutImageArray($hash, $count)
    {
        $databaseHashes = \App\Models\Image::where('category_id', 1)->get()->toArray();
        $hasher = new ImageHash(new DifferenceHash());

        foreach ($databaseHashes as $databaseHash) {
            $distance = $hasher->distance($hash, $databaseHash['hash']);
            $digit = mt_rand(1, 2) / 100;



            $percentSimilarity = ((1 - $distance / 35) * 100) - $digit;



            $similarImages[] = [
                'img' => $databaseHash['img_path'],
                'file_name' => $databaseHash['file_name'],
                'percent' => $percentSimilarity
            ];
        }

        usort($similarImages, function ($a, $b) {
            return $b['percent'] <=> $a['percent'];
        });


    }


}
