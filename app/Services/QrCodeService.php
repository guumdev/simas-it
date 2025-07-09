<?php

namespace App\Services;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\Label\Font\OpenSans;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;

class QrCodeService
{
  public function generateQRCodeImage($content, $qrCodeId)
  {
    try {
      $uploadPath = ROOTPATH . 'public/uploads/qr_codes/';
      if (!is_dir($uploadPath)) {
        mkdir($uploadPath, 0755, true);
      }

      $filename = 'qr_' . $qrCodeId . '_' . time() . '.png';
      $filePath = $uploadPath . $filename;

      // Generate QR code using endroid/qr-code Builder
      $builder = new Builder(
        writer: new PngWriter(),
        writerOptions: [],
        validateResult: false,
        data: $content,
        encoding: new Encoding('UTF-8'),
        errorCorrectionLevel: ErrorCorrectionLevel::High,
        size: 300,
        margin: 10,
        roundBlockSizeMode: RoundBlockSizeMode::Margin,
      );

      $result = $builder->build();

      file_put_contents($filePath, $result->getString());

      // Return relative path for database storage
      return $filename;
    } catch (\Exception $e) {
      // Log error and return null if QR generation fails
      log_message('error', 'Failed to generate QR code: ' . $e->getMessage());
      return null;
    }
  }
}
