<?php

namespace Tests\Feature;

use App\Chat;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use View;

class DownloadChatAsPDFControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function can_download_chat_as_pdf()
    {
//        $this->withoutExceptionHandling();
        $user = factory(User::class)->create();
        View::share('user', $user);

        $chat = Chat::forceCreate(['name' => 'Chat1' ]);

        //TODO -> Afegir missatges al chat

        $response = $this->actingAs($user)->get('/chat/'.$chat->id.'/pdf');

        $response->assertSuccessful();
    }
}
