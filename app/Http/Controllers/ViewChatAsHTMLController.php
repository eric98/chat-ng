<?php

namespace App\Http\Controllers;

use App\Chat;
use Illuminate\Http\Request;

class ViewChatAsHTMLController extends Controller
{
    public function index(Request $request, Chat $chat)
    {

        return view('pdf.chat',compact('chat'));
    }
}
