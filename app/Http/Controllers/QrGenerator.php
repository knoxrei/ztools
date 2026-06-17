<?php

namespace App\Http\Controllers;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\SvgWriter;

class QrGenerator extends Controller
{
    /**
     * Generate a QR code and return it as a data URI string.
     *
     * @param string $data         The content to encode
     * @param int    $size         Image size in pixels (default 300)
     * @param int    $margin       Margin around QR code (default 10)
     * @param string $fgColor      Foreground hex color e.g. '#000000'
     * @param string $bgColor      Background hex color e.g. '#ffffff'
     * @param string $errorLevel   ErrorCorrectionLevel value: high|medium|quartile|low
     * @param string $format       Output format: 'png' or 'svg'
     * @return string              Data URI (data:image/png;base64,...)
     */
    public function generateDataUri(
        string $data,
        int $size = 300,
        int $margin = 10,
        string $fgColor = '#000000',
        string $bgColor = '#ffffff',
        string $errorLevel = 'quartile',
        string $format = 'png',
    ): string {
        $foreground = $this->hexToColor($fgColor);
        $background = $this->hexToColor($bgColor);

        $writer = match ($format) {
            'svg'  => new SvgWriter(),
            default => new PngWriter(),
        };

        $builder = new Builder(
            writer: $writer,
            writerOptions: [],
            validateResult: false,
            data: $data,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::from($errorLevel),
            size: $size,
            margin: $margin,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
            foregroundColor: $foreground,
            backgroundColor: $background,
        );

        return $builder->build()->getDataUri();
    }

    /**
     * Parse a hex color string into an Endroid Color object.
     */
    private function hexToColor(string $hex): Color
    {
        $hex = ltrim($hex, '#');

        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        return new Color(
            red: (int) hexdec(substr($hex, 0, 2)),
            green: (int) hexdec(substr($hex, 2, 2)),
            blue: (int) hexdec(substr($hex, 4, 2)),
        );
    }
}
