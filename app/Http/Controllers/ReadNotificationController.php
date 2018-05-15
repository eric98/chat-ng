<?php

namespace App\Http\Controllers;

use App\Notifications\ChatMessage;
use Auth;
use Illuminate\Http\Request;
use Psy\Util\Json;

class ReadNotificationController extends Controller
{
    public function update($notificationId)
    {
        Auth::user()->unreadNotifications()->find($notificationId)->markAsRead();
    }
}
