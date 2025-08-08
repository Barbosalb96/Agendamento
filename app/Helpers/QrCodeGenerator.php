<?php

declare(strict_types=1);

namespace App\Helpers;

use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Storage;

class QrCodeGenerator
{
    /**
     * Gera e salva o QR code, retornando a URL pública.
     */
    public static function generateAndSave(string $content): string
    {
        $writer = new PngWriter;

        $qrCode = new QrCode(
            data: $content,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::Low,
            size: 300,
            margin: 10,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
            foregroundColor: new Color(0, 0, 0),
            backgroundColor: new Color(255, 255, 255)
        );

        $result = $writer->write($qrCode);

        $fileName = 'qrcodes/'.md5($content).'.png';

        Storage::disk('public')->put($fileName, $result->getString());

        return asset('storage/'.$fileName);
    }
}
