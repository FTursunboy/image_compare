<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Jenssegers\ImageHash\ImageHash;
use Jenssegers\ImageHash\Implementations\DifferenceHash;
use SapientPro\ImageComparator\ImageComparator;
use SebastianBergmann\Comparator\Comparator;
use Intervention\Image\ImageManagerStatic as Image;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function index() {


        $f = public_path('img_2.png');
        $s = public_path('321.png');



        $hasher = new ImageHash(new DifferenceHash());
        $hash = $hasher->hash($f);
        $hash2 = $hasher->hash($s);

        $distance = $hasher->distance($hash, $hash2);


        $maxDistance = 35;
        $percentSimilarity = (1 - $distance / $maxDistance) * 100;

        $percentSimilarity = round($percentSimilarity, 2);


        echo "Процент схожести: $percentSimilarity%";

    }


}
