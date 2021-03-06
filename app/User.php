<?php

namespace App;

use Gravatar;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use NotificationChannels\WebPush\HasPushSubscriptions;

class User extends Authenticatable
{
    use Notifiable, HasPushSubscriptions;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'avatar'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function toArray()
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'email'        => $this->email,
            'avatar'        => $this->avatar
        ];
    }

    /**
     * formatted_created_at_date attribute.
     *
     * @return mixed
     */
    public function getFormattedCreatedAtDateAttribute()
    {
        return $this->created_at->format('h:i:sA d-m-Y');
    }

    public function formatted_notifications()
    {
        return $this->notifications->map(function ($notification) {
        {
            $data = [
                'user' => $notification->data['user'],
                'text' => $notification->data['text'],
                'created_at' => $notification->data['created_at'],
            ];
            return $data;
        }
    });
    }
}
