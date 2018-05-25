<?php

namespace App\Http\Controllers;

use App\Chat;
use App\Events\newChatMessage;
use App\Notifications\ChatMessage;
use App\User;
use Auth;
use Carbon\Carbon;
use Gravatar;
use Illuminate\Http\Request;
use Notification;

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
        $participants = $request->participants;
        unset($participants[array_search($user['name'], $participants)]);

        foreach ($participants as $participant){
            if ($participant['id'] != $user['id']) {
                $userNotify = User::findOrFail($participant['id']);
                $userNotify->notify(new ChatMessage($user, $chat, $message, Carbon::now()));
            }
        }

        event((new newChatMessage($chat,$message,$user))->dontBroadcastToCurrentUser());

        $chat->addMessage($message);
    }
}
