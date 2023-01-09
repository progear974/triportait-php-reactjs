<?php

namespace App\Services;

use thiagoalessio\TesseractOCR\TesseractOCR;

class OCR
{
    private TesseractOCR $tesseractOCR;

    public function __construct()
    {
        $this->tesseractOCR = new TesseractOCR();
    }

    public function cutImage($path)
    {
        // Create an image from given image
        $im = imagecreatefromjpeg($path);

        $size = min(imagesx($im), imagesy($im));
        $width = imagesx($im);
        $height = imagesy($im);
        $im2 = imagecrop($im, ['x' => 25, 'y' => $height - 90, 'width' => 500, 'height' => 100]);
        //$im2 = imagerotate($im2, 90, 0);
        imagejpeg($im2, "test.jpg");
    }

    public function readCode($path)
    {
        $this->tesseractOCR->image($path);
        $code = $this->tesseractOCR->lang('eng')
            ->allowlist(range('a', 'z'), range('A', 'Z'), range('0', '9'), ["_", "-"])
            ->run();
        return $code;
    }
}