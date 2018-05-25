<?php

use App\MonthlyStatistic;
use Illuminate\Database\Seeder;

/**
 * Class DatabaseSeeder
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        inititalize_test_database();

        create_test_database();
        generate_statistics_chat();
    }
}
