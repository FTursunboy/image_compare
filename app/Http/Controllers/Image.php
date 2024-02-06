<?php

namespace App\Http\Controllers;

use Exception;

class Image
{

    const
        BAD_ARGS = 1,
        UNSUPPORTED_FILETYPE = 2,
        ERROR_OPEN = 3;

    public $Images = array();

    private
        $_types = array('', 'gif', 'jpeg', 'png', '', '', 'wbmp', '', '', '', '', '');

    public $CompareWithFirst = false;

    public function __construct($Image1, $Image2 = null)
    {

        if (func_num_args() > 2)
            $Images = func_get_args();
        else if (is_array($Image1))
            $Images = $Image1;
        else
            $Images = array($Image1, $Image2);


        foreach ($Images as $Image) {
            if (is_string($Image))
                $this->_openImage($Image);
            else if (is_resource($Image))
                $this->Images[] = array($this->_getPixelsDiff($Image), array());
            else
                throw new Exception('Bad arguments.', self::BAD_ARGS);
        }
    }

    private function _getImageType($Image)
    {
        // Очищаем путь от нулевых байтов
        $cleanPath = str_replace("\0", '', $Image);

        $imageType = exif_imagetype($cleanPath);

        if (!$imageType || !isset($this->_types[$imageType]))
            throw new Exception('Image has an unsupported file type.', self::UNSUPPORTED_FILETYPE);

        return 'imagecreatefrom' . $this->_types[$imageType];
    }


    private function _openImage($Image)
    {

        $Type = $this->_getImageType($Image);

        $Image = $Type($Image);
        if (!$Image)
            throw new Exception('Error opening image.', self::ERROR_OPEN);

        $this->Images[] = array($this->_getPixelsDiff($Image), array());
        imagedestroy($Image);
    }

    private function _getPixelsDiff($Image)
    {
        $Sample = imagecreatetruecolor(8, 8);
        imagecopyresampled($Sample, $Image, 0, 0, 0, 0, 8, 8, imagesx($Image), imagesy($Image));

        $Pixels = array();
        $Color = array(0, 0, 0);
        for ($y = 0; $y < 8; $y++) {
            for ($x = 0; $x < 8; $x++) {
                $Color1 = imagecolorat($Sample, $x, $y);
                $Color1 = $this->_scale255To9(array(
                    ($Color1 >> 16) & 0xFF,
                    ($Color1 >> 8) & 0xFF,
                    $Color & 0xFF
                ));

                if ($x != 0 || $y != 0) {
                    $Pixels[] = array(
                        $Color1[0] - $Color[0],
                        $Color1[1] - $Color[1],
                        $Color1[2] - $Color[2]
                    );
                }

                $Color = $Color1;
            }
        }
        imagedestroy($Sample);

        return $Pixels;
    }

    private function _scale255To9($NumArr)
    {
        return array(
            round($NumArr[0] / 28.3),
            round($NumArr[1] / 28.3),
            round($NumArr[2] / 28.3)
        );
    }

    private function _getDiff($Img1, $Img2)
    {
        $Diff = 0;
        for ($i = 0; $i < 63; $i++) {
            $Diff += abs($this->Images[$Img1][0][$i][0] - $this->Images[$Img2][0][$i][0]);
            $Diff += abs($this->Images[$Img1][0][$i][1] - $this->Images[$Img2][0][$i][1]);
            $Diff += abs($this->Images[$Img1][0][$i][2] - $this->Images[$Img2][0][$i][2]);
        }

        return $Diff;
    }

    public function Compare()
    {
        $count = count($this->Images);

        dd($count);
        if ($this->CompareWithFirst) {
            for ($i = 1; $i < $count; $i++) {
                $this->Images[0][1][$i] = $this->_getDiff(0, $i);
            }
        } else {
            for ($i = 0; $i < $count; $i++) {
                for ($k = $i + 1; $k < $count; $k++) {
                    //echo "\r\n<br />" .
                    $this->Images[$k][1][$i] =
                    $this->Images[$i][1][$k] = $this->_getDiff($i, $k);
                }
            }
        }
    }

}
