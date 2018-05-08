<?php

namespace App\Http\Controllers;

use App\Chat;
use App\Events\newChatMessage;
use Gravatar;
use Illuminate\Http\Request;

/**
 * Class ChatMessageController
 * @package App\Http\Controllers
 */
class ChatMessageController extends Controller
{
    /**
     * Create chat message
     */
    public function create(Request $request, Chat $chat)
    {
        $message = $request->body;
        $user = $request->user;
        $user['avatar'] = Gravatar::get($user['email']);

        event((new newChatMessage($chat,$message,$user))->dontBroadcastToCurrentUser());

        $chat->addMessage($message);

//        Notification::send(Auth::user(),new ChatMessage($request->body));
    }
}
