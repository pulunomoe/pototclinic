<?php

namespace App\Model\Common;

use GdImage;
use SimpleSoftwareIO\QrCode\Generator;
use Ulid\Ulid;

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
        $this->pink = imagecolorallocate($this->image, 255, 162, 162);

        imagefill($this->image, 0, 0, $this->white);
    }

    protected function save(string $filename): void
    {
        imagepng($this->image, $filename);
        imagedestroy($this->image);
    }

    protected function registerColor(string $hex): int
    {
        $hex = str_replace('#', '', $hex);
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        return imagecolorallocate($this->image, $r, $g, $b);
    }

    protected function generateQr(
        string $text,
        int $size,
    ): GdImage {
        $filename = __DIR__ . '/../../../public/var/tmp/' . Ulid::generate() . '_qr.png';
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

    protected function writeTextRight(
        int $fontSize,
        int $color,
        string $text,
        int $x,
        int $y,
    ): void {
        $bbox = imagettfbbox($fontSize, 0, $this->font, $text);
        $textWidth = $bbox[2] - $bbox[0];
        $x = $x - $textWidth;
        imagettftext($this->image, $fontSize, 0, $x, $y, $color, $this->font, $text);
    }

    protected function writeTextLine(
        int $fontSize,
        int $leftColor,
        int $rightColor,
        string $leftText,
        string $rightText,
        int $y,
        int $margin,
    ): void {
        $bboxL = imagettfbbox($fontSize, 0, $this->font, $leftText);
        $bboxR = imagettfbbox($fontSize, 0, $this->font, $rightText);

        $leftTextW = $bboxL[2] - $bboxL[0];
        $rightTextW = $bboxR[2] - $bboxR[0];
        $xL = $margin;
        $xR = $this->w - $margin - $rightTextW;
        $lineStartX = $xL + $leftTextW + 20;
        $lineEndX = $xR - 20;

        imagettftext($this->image, $fontSize, 0, $xL, $y, $leftColor, $this->font, $leftText);
        imagefilledrectangle($this->image, $lineStartX, $y - 7, $lineEndX, $y - 5, $leftColor);
        imagettftext($this->image, $fontSize, 0, $xR, $y, $rightColor, $this->font, $rightText);
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