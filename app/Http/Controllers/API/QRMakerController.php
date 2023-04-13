<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\HtmlString;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QRMakerController extends Controller
{

    /**
     * @param string $text
     * @return void|HtmlString|string
     * */
    public static function make($text)
    {
        $qr = QrCode::size(TELEGRAM_LINK_QR_SIZE);
        $qr->style('round');
        $qr->eye('circle');
        $qr->margin(1);
        $qr->backgroundColor(255,255,255);
        $qr->errorCorrection('H');
        $qr->format(TELEGRAM_LINK_QR_FORMAT);
        $qr->merge(WATERMARK_QR_LOGO_PATH, 0.35);
        $qr->gradient(71, 126, 175, 12, 126, 126, 'vertical');
        return $qr->generate($text);
    }

}
