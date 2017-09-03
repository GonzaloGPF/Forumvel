<?php

namespace Tests\Feature;

use App\Thread;
use App\Trending;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class TrendingThreadsTest extends TestCase
{

    use DatabaseMigrations;

    /**
     * @var Trending
     */
    public $trending;

    protected function setUp()
    {
        parent::setUp();

        $this->trending = new Trending();
        $this->trending->reset();
    }

    /** @test */
    public function increment_threads_score_each_time_its_read()
    {
        $this->assertEmpty($this->trending->get());

        $thread = create(Thread::class);

        $this->call('GET', $thread->path());

        $trending = $this->trending->get();

        $this->assertCount(1, $trending);

        $this->assertEquals($thread->title, $trending[0]->title);
    }
}
