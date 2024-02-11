<?php

namespace App\Jobs;

use App\Models\Image;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Intervention\Image\Facades\Image as InterventionImage;
use Jenssegers\ImageHash\ImageHash;
use Jenssegers\ImageHash\Implementations\DifferenceHash;

class ResizeImagesJob implements ShouldQueue
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

        $images_to_resize = Image::get();

        foreach ($images_to_resize as $image) {

            $resized_image = InterventionImage::make(File::get(public_path($image->img_path)));

            $resized_image->resize(400, 400);

            $file_name_of_resized_image = 'uploads/' . time() . rand(123434, 999999) . rand(100, 999) . '.jpg';

            $resized_image->save(public_path( $file_name_of_resized_image));

            $image->new_file_path = $file_name_of_resized_image;
            $image->save();
        }

    }
}
