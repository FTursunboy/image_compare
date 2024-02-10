<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Jenssegers\ImageHash\ImageHash;
use Jenssegers\ImageHash\Implementations\DifferenceHash;

class JobCommand implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        $images = \App\Models\Image::where('id', '>', 469)->get();

        foreach ($images as $image) {

            $imagePath = public_path($image->img_path);

            $file = \Illuminate\Support\Facades\File::get($imagePath);

            $originalFilename = $image->file_name;


            $cleanedFilename = str_replace('.', '', $image->file_name);
            $cleanedFilename = strtolower($cleanedFilename);


            $extension = pathinfo($originalFilename, PATHINFO_EXTENSION);

            if (!empty($extension)) {
                $newFilename = substr_replace($cleanedFilename, '.', -strlen($extension), 0);
            } else {
                $newFilename = $cleanedFilename;
            }


            $response = Http::withHeaders([
                'accept' => 'application/json',
                'authorization' => 'Bearer твой_токен',
            ])
                ->attach('file', $file, 'test.jpg', ['Content-Type' => 'multipart/form-data'])
                ->timeout(657384573485730)
                ->post('https://api.edenai.run/v2/image/search/upload_image', [
                    'providers' => 'sentisight',
                    'image_name' => $newFilename
                ]);

            dump($response->json());

            if ($response->successful()) {
                $image->sent = true;
                $image->save();
            }



        }

    }
}
