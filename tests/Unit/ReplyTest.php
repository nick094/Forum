<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;


class ReplyTest extends TestCase
{
    /** @test **/
    public function a_reply_has_a_owner()
    {
    	$reply = factory('App\Reply')->create();

        $this->assertInstanceOf('App\User', $reply->owner);
    }

    /** @test **/
    public function it_knows_if_it_was_just_published() 
    {
        $reply = create('App\Reply');

        $this->assertTrue($reply->wasJustPublished());

        $reply->created_at = Carbon::now()->subMonth();

        $this->assertFalse($reply->wasJustPublished());
    }

    /** @test **/
    public function it_can_detect_all_mentioned_users_in_the_body() 
    {
        $reply = create('App\Reply', [
            'body' => '@JaneDoe wants to talk to @JohnDoe'
        ]);

        $this->assertEquals(['JaneDoe','JohnDoe'], $reply->mentionedUsers());
    }

    /** @test **/
    public function it_wraps_mentioned_usernames_in_the_body_with_anchor_tags() 
    {
        $reply = create('App\Reply', [
            'body' => 'Hello @JaneDoe'
        ]);

        $this->assertEquals('Hello <a href="/profiles/JaneDoe">@JaneDoe</a>'),
        $reply->body
        );
    }

    /** @test **/
    public function it_knows_if_it_is_the_best_reply() 
    {
        $reply = create('App\Reply');

        $this->assertFalse($reply->isBest());

        $reply->thread->update(['best_reply_id' => $reply->id ]);

        $this->assertTrue($reply->fresh()->isBest());
    }
}
