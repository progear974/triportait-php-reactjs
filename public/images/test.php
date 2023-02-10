<?php
function resizeSingle($pathImage, $new_width=1748, $new_height=2402)
{
    $img = imagecreatefromjpeg($pathImage);
    if (!$img)
        return;
    $width_img = imagesx($img);
    $height_img = imagesy($img);
    $cropped_img = imagecrop($img, ['x' => ($width_img / 2) - ($new_width / 2), 'y' => ($height_img / 2) - ($new_height / 2), 'width' => $new_width, 'height' => $new_height]);
    if ($cropped_img)
        imagejpeg($cropped_img, $pathImage);
    imagedestroy($img);
    imagedestroy($cropped_img);
}

$singles_filenames = ["20230210_005120_756.jpg", "20230210_005128_272.jpg", "20230210_005135_710.jpg",
    "20230210_005143_211.jpg", "20230210_005148_118.jpg"];
array_map('resizeSingle', $singles_filenames, array_fill(0, sizeof($singles_filenames), 1748), array_fill(0, sizeof($singles_filenames), 2402));
