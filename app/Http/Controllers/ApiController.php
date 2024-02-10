<?php

namespace App\Http\Controllers;

use App\Jobs\JobCommand;
use App\Models\Setting;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\File;
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

            $image =\App\Models\Image::create([
                'img_path' => $file['path'],
                'file_name' => $file['file_name'],
                'category_id' => $request->category_id,
                'unique_number' => rand(10000000, 99999999) . round(microtime(true) * 1000) . '.jpg'
            ]);

            $imagePath = public_path($image->img_path);

            $file = \Illuminate\Support\Facades\File::get($imagePath);

            $response = Http::withHeaders([
                'accept' => 'application/json',
                'authorization' => 'Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyX2lkIjoiYTJjMDJkNGEtZjgwNC00M2UxLThhNzQtMjAzZGViNWVlYTk0IiwidHlwZSI6ImFwaV90b2tlbiJ9.iP-Ga-VPn1TmjfiA0qLAO_4Y5lgJ4-ZppRkw7uDsWGI',
            ])
                ->attach('file', $file, 'test.jpg', ['Content-Type' => 'multipart/form-data'])
                ->timeout(657384573485730)
                ->post('https://api.edenai.run/v2/image/search/upload_image', [
                    'providers' => 'sentisight',
                    'image_name' => $image->unique_number
                ]);
            if ($response->successful()) {
                $image->sent = true;
                $image->save();

            }
            else {
                return back()->with('error', 'Превышен лимит запросов');
            }

        }
        return back()->with('success', 'Файлы успешны загружены');


    }



    public function compare(Request $request)
    {
        if ($request->hasFile('file')) {
//            $file = $request->file('file');
//            $file_name = time() . rand(1, 99);
//            $file->move(public_path('uploads'), $file_name);
//            $path = 'uploads/' . $file_name;
//
//            $imagePath = public_path($path);
//
//            $file1 = \Illuminate\Support\Facades\File::get($imagePath);
//            $similarImages = [];


            $response = Http::withHeaders([
                'accept' => 'application/json',
                'authorization' => 'Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyX2lkIjoiYTJjMDJkNGEtZjgwNC00M2UxLThhNzQtMjAzZGViNWVlYTk0IiwidHlwZSI6ImFwaV90b2tlbiJ9.iP-Ga-VPn1TmjfiA0qLAO_4Y5lgJ4-ZppRkw7uDsWGI',
            ])
                ->attach('file', $request->file('file'), 'test.jpg', ['Content-Type' => 'multipart/form-data'])
                ->timeout(657384573485730)
                ->post('https://api.edenai.run/v2/image/search/launch_similarity', [
                    'providers' => 'sentisight',
                ]);

            if ($response->successful()) {
                dump($response);
                $result = $response['sentisight']['items'];
                dump($response->json()['sentisight']['items']);
                dd($result);

                foreach ($result as $res) {
                    $image = \App\Models\Image::where([
                        ['unique_number', $res['image_name']],
                        ['category_id' => $request->category_id]
                            ])->first();

                    if ($image) {
                        $similarImages[] = [
                            'img' => $image->img_path,
                            'file_name' => $image->unque_number,
                            'percent' => $res['score']
                        ];
                    }
                }
                return view('welcome', ['images' => $similarImages]);
            }
            else {
                return redirect()->back()->with('error', 'Превышен лимит запросов');
            }

        }

        return view('welcome');
    }

}

