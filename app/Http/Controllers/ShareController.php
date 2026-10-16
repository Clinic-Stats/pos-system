<?php

namespace App\Http\Controllers;

use App\Support\WhatsAppReceipt;
use Illuminate\Http\Request;

/**
 * لینکە کورتە پارێزراوەکانی واتساپ (بێ لۆگین).
 * کۆدەکە (ID + HMAC) لە WhatsAppReceipt پشکنین دەکرێت؛ ئەگەر هەڵە بێت 404 دەگەڕێتەوە.
 */
class ShareController extends Controller
{
    public function sale(Request $request, string $code)
    {
        $id = WhatsAppReceipt::verify('sale', $code);
        abort_if($id === null, 404);

        $request->merge(['type' => 'a4']);
        return app(SaleController::class)->print($request, $id);
    }

    public function statement(string $code)
    {
        $id = WhatsAppReceipt::verify('statement', $code);
        abort_if($id === null, 404);

        return app(CustomerController::class)->publicStatement($id);
    }
}