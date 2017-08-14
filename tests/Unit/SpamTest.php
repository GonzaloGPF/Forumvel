<?php

namespace Tests\Feature;

use App\Spam;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class SpamTest extends TestCase
{

    use DatabaseMigrations;

    /** @test */
    function it_validates_spam()
    {
        $spam = new Spam();

        $this->assertFalse($spam->detect('Innocent reply here'));

        $this->expectException(\Exception::class);
        $this->assertTrue($spam->detect('Yahoo comment'));
    }
}