<?php

namespace OrangeShadow\Polls\Test;

use OrangeShadow\Polls\Test\Models\User;

class MultiPollTest extends TestCase
{

    public function setUp()
    {
        parent::setUp();
        $this->seedPolls();
    }

    public function testClosePollByActive()
    {
        $this->pollMulti->active = 0;
        $this->pollMulti->save();

        $this->expectException(\OrangeShadow\Polls\Exceptions\PollIsClosedException::class);

        $this->app->make('PollProxy', ['poll' => $this->pollMulti]);
    }


    public function testClosePollByClosedAt()
    {
        $this->pollMulti->closed_at = '2012-01-01';
        $this->pollMulti->save();

        $this->expectException(\OrangeShadow\Polls\Exceptions\PollIsClosedException::class);

        $this->app->make('PollProxy', ['poll' => $this->pollMulti]);
    }

    public function testMultiVoteOptionNotFound()
    {
        $option = $this->pollVariable->options()->where('id', '>', 9)->pluck('id')->toArray();

        $pollProxy = $this->app->make('PollProxy', ['poll' => $this->pollMulti]);

        $this->expectException(\OrangeShadow\Polls\Exceptions\OptionIsNotFoundException::class);

        $pollProxy->voting($this->user->id, $option);
    }

    public function testMultiVoteOptionTypeException()
    {
        $option = $this->pollMulti->options()->first();

        $pollProxy = $this->app->make('PollProxy', ['poll' => $this->pollMulti]);

        $this->expectException(\OrangeShadow\Polls\Exceptions\OptionTypeException::class);

        $pollProxy->voting($this->user->id, $option->id);
    }


    public function testMultiVoteAlreadyCastYourVoteException()
    {
        $options = $this->pollMulti->options()->where('id', '<', 5)->pluck('id')->toArray();

        $pollProxy = $this->app->make('PollProxy', ['poll' => $this->pollMulti]);

        $pollProxy->voting($this->user->id, $options);

        $this->expectException(\OrangeShadow\Polls\Exceptions\AlreadyCastYourVoteException::class);

        $pollProxy->voting($this->user->id, $options);
    }


    public function testResultsEqualVote()
    {
        $user1 = $this->user;
        $user2 = User::find(2);
        $user3 = User::find(3);

        $optionList = $this->pollMulti->options;
        $options = $this->pollMulti->options->pluck('id')->toArray();


        $pollProxy = $this->app->make('PollProxy', ['poll' => $this->pollMulti]);

        $pollProxy->voting($user1->id, $options);
        $pollProxy->voting($user2->id, $options);
        $pollProxy->voting($user3->id, $options);

        $results = $pollProxy->getResult();

        $this->assertContains(
            [
                'title'       => $optionList[0]->title,
                'voter_count' => 3,
                'total_weight' => 3,
                'percent'     => 100,
            ], $results);

        $this->assertContains(
            [
                'title'       => $optionList[1]->title,
                'voter_count' => 3,
                'total_weight' => 3,
                'percent'     => 100,
            ], $results);

        $this->assertContains(
            [
                'title'       => $optionList[2]->title,
                'voter_count' => 3,
                'total_weight' => 3,
                'percent'     => 100,
            ], $results);

    }


    public function testResultsNotEqualVote()
    {
        $user1 = $this->user;
        $user2 = User::find(2);
        $user3 = User::find(3);

        $optionList = $this->pollMulti->options;
        $options = $this->pollMulti->options->pluck('id')->toArray();


        $pollProxy = $this->app->make('PollProxy', ['poll' => $this->pollMulti]);

        $pollProxy->voting($user1->id, $options);
        $pollProxy->voting($user2->id, [$options[0]]);
        $pollProxy->voting($user3->id, [$options[0], $options[1], $options[2]]);

        $results = $pollProxy->getResult();


        $this->assertContains(
            [
                'title'       => $optionList[0]->title,
                'voter_count' => 3,
                'total_weight'=> 3,
                'percent'     => 100,
            ], $results);

        $this->assertContains(
            [
                'title'       => $optionList[1]->title,
                'voter_count' => 2,
                'total_weight'=> 2,
                'percent'     => 66.67,
            ], $results);

        $this->assertContains(
            [
                'title'       => $optionList[2]->title,
                'voter_count' => 2,
                'total_weight' => 2,
                'percent'     => 66.67,
            ], $results);
    }
}