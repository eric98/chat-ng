<?php

namespace App\Http\Controllers;

use App\Chat;
use PDF;
use Illuminate\Http\Request;

class DownloadChatAsPDFController extends Controller
{
    public function index(Request $request, Chat $chat)
    {
//        dd($chat->messages);
        $pdf = PDF::loadView('pdf.chat',compact('chat'));

        return $pdf->download("$chat->name.pdf");
    }
}
