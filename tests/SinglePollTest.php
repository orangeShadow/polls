<?php

namespace OrangeShadow\Polls\Test;

use OrangeShadow\Polls\Test\Models\User;

class SinglePollTest extends TestCase
{

    public function setUp()
    {
        parent::setUp();
        $this->seedPolls();
    }

    public function testClosePollByActive()
    {
        $this->pollSingle->active = 0;
        $this->pollSingle->save();

        $this->expectException(\OrangeShadow\Polls\Exceptions\PollIsClosedException::class);

        $this->app->make('PollProxy', ['poll' => $this->pollSingle]);
    }

    public function testClosePollByClosedAt()
    {
        $this->pollSingle->closed_at = '2012-01-01';
        $this->pollSingle->save();

        $this->expectException(\OrangeShadow\Polls\Exceptions\PollIsClosedException::class);

        $this->app->make('PollProxy', ['poll' => $this->pollSingle]);
    }

    public function testSingleVoteOptionNotFound()
    {
        $option = $this->pollMulti->options()->first()->id;

        $pollProxy = $this->app->make('PollProxy', ['poll' => $this->pollSingle]);

        $this->expectException(\OrangeShadow\Polls\Exceptions\OptionIsNotFoundException::class);

        $pollProxy->voting($this->user->id, [$option]);
    }

    public function testSingleVoteOptionTypeException()
    {
        $option = $this->pollSingle->options()->pluck('id')[0];

        $pollProxy = $this->app->make('PollProxy', ['poll' => $this->pollSingle]);

        $this->expectException(\OrangeShadow\Polls\Exceptions\OptionTypeException::class);

        $pollProxy->voting($this->user->id, $option);
    }

    public function testSingleVoteAlreadyCastYourVoteException()
    {
        $option = $this->pollSingle->options()->first()->id;

        $pollProxy = $this->app->make('PollProxy', ['poll' => $this->pollSingle]);

        $pollProxy->voting($this->user->id, [$option]);

        $this->expectException(\OrangeShadow\Polls\Exceptions\AlreadyCastYourVoteException::class);

        $pollProxy->voting($this->user->id, [$option]);
    }

    public function testResultsEqualVote()
    {
        $user1 = $this->user;
        $user2 = User::find(2);
        $user3 = User::find(3);

        $options = $this->pollSingle->options;

        $pollProxy = $this->app->make('PollProxy', ['poll' => $this->pollSingle]);

        $pollProxy->voting($user1->id, [$options[0]->id]);
        $pollProxy->voting($user2->id, [$options[1]->id]);
        $pollProxy->voting($user3->id, [$options[2]->id]);

        $results = $pollProxy->getResult();

        $this->assertContains(
            [
                'title'        => $options[0]->title,
                'voter_count'  => 1,
                'total_weight' => 1,
                'percent'      => 33.33,
            ], $results);

        $this->assertContains(
            [
                'title'        => $options[1]->title,
                'voter_count'  => 1,
                'total_weight' => 1,
                'percent'      => 33.33,
            ], $results);

        $this->assertContains(
            [
                'title'        => $options[2]->title,
                'voter_count'  => 1,
                'total_weight' => 1,
                'percent'      => 33.33,
            ], $results);


        $this->assertEquals(100.0, round(array_sum(array_map(function ($item) {
            return $item['percent'];
        }, $results))));
    }

    public function testResultsNotEqualVote()
    {
        $user1 = $this->user;
        $user2 = User::find(2);
        $user3 = User::find(3);

        $options = $this->pollSingle->options;

        $pollProxy = $this->app->make('PollProxy', ['poll' => $this->pollSingle]);

        $pollProxy->voting($user1->id, [$options[0]->id]);
        $pollProxy->voting($user2->id, [$options[0]->id]);
        $pollProxy->voting($user3->id, [$options[2]->id]);

        $results = $pollProxy->getResult();

        $this->assertContains(
            [
                'title'        => $options[0]->title,
                'voter_count'  => 2,
                'total_weight' => 2,
                'percent'      => 66.67,
            ], $results);

        $this->assertContains(
            [
                'title'        => $options[1]->title,
                'voter_count'  => 0,
                'total_weight' => 0,
                'percent'      => 0,
            ], $results);

        $this->assertContains(
            [
                'title'        => $options[2]->title,
                'voter_count'  => 1,
                'total_weight' => 1,
                'percent'      => 33.33,
            ], $results);


        $this->assertEquals(100.0, round(array_sum(array_map(function ($item) {
            return $item['percent'];
        }, $results))));
    }
}