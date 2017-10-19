<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (App::environment() === 'production') exit();

        $this->cleanDataBase();

        $this->call(ThreadsTableSeeder::class);

        factory(\App\User::class)->states('test')->create();
    }

    public function cleanDataBase()
    {
        //disable foreign key check for this connection before truncating tables
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $database = DB::select('SELECT DATABASE() AS name');

        $col = 'Tables_in_' . $database[0]->name;

        $tables = array_except(DB::select('SHOW TABLES'), ['migrations']);

        foreach ($tables as $table) {
            DB::table($table->$col)->truncate();
        }
    }
}
