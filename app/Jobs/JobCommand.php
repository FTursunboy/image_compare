<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
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

        $images = \App\Models\Image::get();

        foreach ($images as $image) {


            $hasher = new ImageHash(new DifferenceHash(32));
                dump($image->category_id);

              $i =  \App\Models\RecalculatedImages::create([
                    'img_path' => $image->img_path,
                    'file_name' => $image->file_name,
                    "category_id" => $image->category_id,
                    'hash' => 123,
                ]);
                dump($i);
                dd(1);

        }
    }
}
