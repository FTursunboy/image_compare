<?php

namespace App\Http\Controllers;

use App\Jobs\ResizeImagesJob;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use \Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Jenssegers\ImageHash\ImageHash;
use Jenssegers\ImageHash\Implementations\DifferenceHash;
use \Intervention\Image\Facades\Image as InterventionImage;
use App\Models\Image;


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
        $hasher = new ImageHash(new DifferenceHash());

        foreach ($files as $key => $file) {

            $hash = $hasher->hash(public_path($file['path']));

            $image =\App\Models\Image::create([
                'img_path' => $file['path'],
                'file_name' => $file['file_name'],
                'hash' => $hash,
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
            $file = $request->file('file');

            $file_name_of_original_image = time() . rand(1, 99) . '.' . $file->extension();

            $file->move(public_path('uploads'), $file_name_of_original_image);
            $path_of_original_image = 'uploads/' . $file_name_of_original_image;


            $resized_image = InterventionImage::make(File::get(public_path($path_of_original_image)));

            $resized_image->brightness(22);

            $resized_image->resize(400, 400);


            $file_name_of_resized_image = time();

            $resized_image->save(public_path('uploads/' . $file_name_of_resized_image . '.jpg'));

            $path_of_resized_image = 'uploads/' . $file_name_of_resized_image . '.jpg';

            $file_for_ai = File::get(public_path($path_of_resized_image));
            $similarImages = [];


            $response = Http::withHeaders([
                'accept' => 'application/json',
                'authorization' => 'Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyX2lkIjoiYTJjMDJkNGEtZjgwNC00M2UxLThhNzQtMjAzZGViNWVlYTk0IiwidHlwZSI6ImFwaV90b2tlbiJ9.iP-Ga-VPn1TmjfiA0qLAO_4Y5lgJ4-ZppRkw7uDsWGI',
            ])
                ->attach('file', $file_for_ai, 'test.jpg', ['Content-Type' => 'multipart/form-data'])
                ->timeout(657384573485730)
                ->post('https://api.edenai.run/v2/image/search/launch_similarity', [
                    'providers' => 'sentisight',
                ]);

            if ($response->successful()) {

                $result = $response->json()['sentisight']['items'];


                foreach ($result as $res) {
                    $image = Image::where('unique_number', $res['image_name'])->first();

                    if ($image) {
                        $similarImages[] = [
                            'img' => $image->img_path,
                            'file_name' => $image->file_name,
                            'percent' => $res['score']
                        ];
                    }
                }
                return view('welcome', ['images' => $similarImages, 'image' => $path_of_resized_image, 'name' => "f"]);
            }
            else {
                return redirect()->back()->with('error', 'Превышен лимит запросов');
            }

     }

        return view('welcome');
    }


    public function resize_image() :void
    {
        ResizeImagesJob::dispatch();
    }

}

