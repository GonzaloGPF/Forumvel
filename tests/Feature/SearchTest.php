<?php

namespace Tests\Feature;

use App\Thread;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function a_user_can_search_threads()
    {
        config(['scout.driver' => 'algolia']);

        $search = 'foobar';

        create(Thread::class, [], 2);
        create(Thread::class, ['body' => "A thread with ${search} tern"], 2);

        // Because here we are using Algolia's driver, each time a Thread is created, it will hit Algolia's server
        // So, there will be a little delay, so when requesting 'threads/search' uri, we can get wrong results and test fails
        do {
            sleep(.25);

            $result = $this->getJson("threads/search?q=${search}")->json()['data'];
        } while(empty($result));

        $this->assertCount(2, $result);

        Thread::latest()->take(4)->unsearchable(); // This method will hit Algolia's Server requesting to delete this test's threads
    }
}
