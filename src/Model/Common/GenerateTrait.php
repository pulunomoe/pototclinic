<?php

namespace App\Model\Common;

use GdImage;
use SimpleSoftwareIO\QrCode\Generator;

trait GenerateTrait
{
    protected int $w;
    protected int $h;
    protected GdImage $image;

    protected string $font = __DIR__ . '/../../../public/static/comic.ttf';
    protected GdImage $logo;
    protected int $white;
    protected int $dark;
    protected int $black;
    protected int $pink;

    protected function setUp(int $w, int $h): void
    {
        $this->w = $w;
        $this->h = $h;
        $this->image = imagecreatetruecolor(
            $this->w,
            $this->h,
        );

        $this->logo = imagecreatefrompng(__DIR__ . '/../../../public/static/logo.png');
        $this->white = imagecolorallocate($this->image, 255, 255, 255);
        $this->dark = imagecolorallocate($this->image, 64, 64, 64);
        $this->black = imagecolorallocate($this->image, 0, 0, 0);
        $this->pink = imagecolorallocate($this->image, 255, 226, 226);

        imagefill($this->image, 0, 0, $this->white);
    }

    protected function save(string $filename): void
    {
        imagepng($this->image, $filename);
        imagedestroy($this->image);
    }

    protected function generateQr(
        string $text,
        int $size,
    ): GdImage {
        $filename = __DIR__ . '/../../../public/var/tmp/' . $text . '_qr.png';
        $generator = new Generator();
        $generator->size($size)->margin(2)->format('png')->generate($text, $filename);
        $qrImage = imagecreatefrompng($filename);
        unlink($filename);
        return $qrImage;
    }

    protected function drawRectangle(
        int $color,
        int $x1,
        int $y1,
        int $x2,
        int $y2,
    ): void {
        imagefilledrectangle($this->image, $x1, $y1, $x2, $y2, $color);
    }

    protected function writeText(
        int $fontSize,
        int $color,
        string $text,
        int $x,
        int $y,
    ): void {
        imagettftext($this->image, $fontSize, 0, $x, $y, $color, $this->font, $text);
    }

    protected function writeTextCentered(
        int $fontSize,
        int $color,
        string $text,
        int $y,
    ): void {
        $bbox = imagettfbbox($fontSize, 0, $this->font, $text);
        $textWidth = $bbox[2] - $bbox[0];
        $x = (1200 - $textWidth) / 2;
        imagettftext($this->image, $fontSize, 0, $x, $y, $color, $this->font, $text);
    }

    protected function drawImage(
        GdImage $image,
        int $x,
        int $y,
        int $w,
        int $h,
    ): void {
        imagecopyresampled(
            $this->image,
            $image,
            $x,
            $y,
            0,
            0,
            $w,
            $h,
            imagesx($image),
            imagesy($image),
        );
    }

    protected function drawImageCentered(
        GdImage $image,
        int $y,
        int $h,
    ): void {
        $srcW = imagesx($image);
        $srcH = imagesy($image);

        $scale = $h / $srcH;
        $w = $srcW * $scale;

        $destWidth = imagesx($this->image);
        $x = ($destWidth - $w) / 2;

        imagecopyresampled($this->image, $image, $x, $y, 0, 0, $w, $h, $srcW, $srcH);
    }
}