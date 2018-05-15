<?php

namespace App\Http\Controllers;

use App\Chat;
use PDF;
use Illuminate\Http\Request;

class ViewChatAsHTMLController extends Controller
{
    public function index(Request $request, Chat $chat)
    {
        $pdf = PDF::loadView('pdf.chat',compact('chat'));

        return $pdf->stream("$chat->name.pdf");
    }
}
