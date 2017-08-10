<?php

use App\Reply;
use App\Thread;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    private $tables = [
        'threads',
        'replies',
        'channels',
        'users',
        'activities',
        'favorites',
        'password_resets'

    ];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->cleanDataBase();

        factory(Thread::class, 10)->create()->each(function($thread){
            factory(Reply::class, rand(1,5))->create([
                'thread_id' => $thread->id
            ]);
        });

        $user = \App\User::find(1);
        $user->email = 'mail@mail.com';
        $user->save();

    }

    public function cleanDataBase()
    {
        //disable foreign key check for this connection before truncating tables
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        foreach ($this->tables as $tableName){
            DB::table($tableName)->truncate();
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
