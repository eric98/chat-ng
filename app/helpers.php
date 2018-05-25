<?php

use Acacha\User\GuestUser;
use App\Chat;
use App\Http\Resources\UserResource;
use App\MonthlyStatistic;
use App\User;
use Carbon\Carbon;

if (!function_exists('randomDate')) {
    /**
     * Method to generate random date between two dates.
     *
     * @param $sStartDate
     * @param $sEndDate
     * @param string $sFormat
     * @return bool|string
     */
    function randomDate($sStartDate, $sEndDate, $sFormat = 'Y-m-d H:i:s')
    {
        // Convert the supplied date to timestamp
        $fMin = strtotime($sStartDate);
        $fMax = strtotime($sEndDate);
        // Generate a random number from the start and end dates
        $fVal = mt_rand($fMin, $fMax);
        // Convert back to the specified date format
        return date($sFormat, $fVal);
    }
}

if (!function_exists('create_test_database')) {
    function create_test_database()
    {
        $now = Carbon::now();

        $chat = Chat::forceCreate(['name' => 'Chat001' ]);

        foreach (range(1, $now->month) as $month) {
            dump('Month: ' . $month);
            foreach (range(1, rand(10, 100)) as $user) {
                dump('Creating user number ' , $user);
                dump($randomDate = randomDate(
                    Carbon::createFromDate(null, $month, 1)->startOfMonth(),
                    Carbon::createFromDate(null, $month, 1)->endOfMonth()));
                $user = factory(User::class)->create([
                    'created_at' => $randomDate,
                    'updated_at' => $randomDate
                ]);
                $chat->addMessage('Hola que tal cracks!',$user);
                dump('Chat001.missatge: Hola que tal cracks! -> ' . $user->name);
            }
        }

        $chat = Chat::forceCreate(['name' => 'Chat002' ]);

        foreach (range(1, $now->month) as $month) {
            dump('Month: ' . $month);
            foreach (range(1, rand(10, 100)) as $user) {
                dump('Creating user number ' , $user);
                dump($randomDate = randomDate(
                    Carbon::createFromDate(null, $month, 1)->startOfMonth(),
                    Carbon::createFromDate(null, $month, 1)->endOfMonth()));
                $user = factory(User::class)->create([
                    'created_at' => $randomDate,
                    'updated_at' => $randomDate
                ]);
                $chat->addMessage('Adeu màquina!',$user);
                dump('Chat002.missatge: Adeu màquina! -> ' . $user->name);

            }
        }

        $chat = Chat::forceCreate(['name' => 'Chat003' ]);

        foreach (range(1, $now->month) as $month) {
            dump('Month: ' . $month);
            foreach (range(1, rand(10, 100)) as $user) {
                dump('Creating user number ' , $user);
                dump($randomDate = randomDate(
                    Carbon::createFromDate(null, $month, 1)->startOfMonth(),
                    Carbon::createFromDate(null, $month, 1)->endOfMonth()));
                $user = factory(User::class)->create([
                    'created_at' => $randomDate,
                    'updated_at' => $randomDate
                ]);
                $chat->addMessage('Jeje',$user);
                dump('Chat003.missatge: Jeje! -> ' . $user->name);

            }
        }
    }
}

if (!function_exists('inititalize_test_database')) {
    function inititalize_test_database() {
        $chat1 = Chat::forceCreate(['name' => 'Chat1' ]);
        Chat::forceCreate(['name' => 'Chat2' ]);
        Chat::forceCreate(['name' => 'Chat3' ]);

        // Add some messages to Chat 1
        $user = factory(User::class)->create();
        $chat1->addMessage('Hello world!',$user);
        $chat1->addMessage('Hello foo!',$user);
        $user2 = factory(User::class)->create();
        $chat1->addMessage('Hello bar!',$user2);
    }
}

if (!function_exists('random_avatar_path')) {
    function random_avatar_path()
    {
        $avatars = [
            '/img/avatar.png',
            '/img/avatar2.png',
            '/img/avatar3.png',
            '/img/avatar04.png',
            '/img/avatar5.png',
            '/img/user1-128x128.jpg',
            '/img/user2-160x160.jpg',
            '/img/user3-128x128.jpg',
            '/img/user4-128x128.jpg',
            '/img/user5-128x128.jpg',
            '/img/user6-128x128.jpg',
            '/img/user7-128x128.jpg',
            '/img/user8-128x128.jpg'
        ];
        return $avatars[array_rand($avatars)];
    }
}

if (!function_exists('formatted_logged_user')) {
    function formatted_logged_user()
    {
        if(!Auth::user()) return new GuestUser();
        return json_encode((new UserResource(Auth::user()))->resolve());
    }
}

if (!function_exists('generate_statistics_chat')) {
    function generate_statistics_chat_at_month($year, $month)
    {
        Artisan::call('cache:clear');

        if ($month<10){
            $month = "0".$month;
        }
        $month=(string)$month;
        $year=(string)$year;

        MonthlyStatistic::forceCreate([
            'year' => intval($year),
            'month' => intval($month),
            'new_users' =>  DB::table('users')->whereYear('created_at', $year)->whereMonth('created_at', $month)->get()->count(),
            'total_users' => DB::table('users')
                ->whereYear('created_at', '<=',$year)
                ->whereMonth('created_at','<=',$month)
                ->get()->count(),
            'chat_messages' => DB::table('chat_messages')
                ->whereYear('created_at',$year)
                ->whereMonth('created_at',$month)
                ->get()->count(),
        ]);
    }
}

if (!function_exists('generate_statistics_chat')) {
    function generate_statistics_chat() {
        for ($i = 1; $i < Carbon::now()->month; $i++) {
            $year = Carbon::now()->year;
            generate_statistics_chat_at_month($year, $i);
        }
    }
}