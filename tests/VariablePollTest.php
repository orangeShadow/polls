<?php

namespace OrangeShadow\Polls\Test;

use OrangeShadow\Polls\Test\Models\User;


class VariablePollTest extends TestCase
{



    public function setUp()
    {
        parent::setUp();
        $this->seedPolls();
    }

    public function testClosePollByActive()
    {
        $this->pollVariable->active = 0;
        $this->pollVariable->save();

        $this->expectException(\OrangeShadow\Polls\Exceptions\PollIsClosedException::class);

        $this->app->make('PollProxy', ['poll' => $this->pollVariable]);
    }


    public function testClosePollByClosedAt()
    {
        $this->pollVariable->closed_at = '2012-01-01';
        $this->pollVariable->save();

        $this->expectException(\OrangeShadow\Polls\Exceptions\PollIsClosedException::class);

        $this->app->make('PollProxy', ['poll' => $this->pollVariable]);
    }

    public function testVariableVoteOptionNotFound()
    {
        $optionsCount = $this->pollVariable->options->count();

        $options = \OrangeShadow\Polls\Option::limit($optionsCount)->pluck('id')->toArray();

        $pollProxy = $this->app->make('PollProxy', ['poll' => $this->pollVariable]);

        $this->expectException(\OrangeShadow\Polls\Exceptions\OptionIsNotFoundException::class);

        $pollProxy->voting($this->user->id,$options);
    }

    public function testVariableVoteOptionTypeException()
    {
        $option = $this->pollVariable->options()->first()->id;

        $pollProxy = $this->app->make('PollProxy', ['poll' => $this->pollVariable]);

        $this->expectException(\OrangeShadow\Polls\Exceptions\OptionTypeException::class);

        $pollProxy->voting($this->user->id,$option);
    }


    public function testVariableVoteWrongOptionsCountSpecifiedException()
    {
        $options = $this->pollVariable->options()->where('id','>=',12)->pluck('id')->toArray();

        $pollProxy = $this->app->make('PollProxy', ['poll' => $this->pollVariable]);

        $this->expectException(\OrangeShadow\Polls\Exceptions\WrongOptionsCountSpecifiedException::class);

        $pollProxy->voting($this->user->id,$options);
    }

    public function testResultsSameOrderLikeInBD()
    {
        $user1  = $this->user;
        $user2  = User::find(2);
        $user3  = User::find(3);

        $optionList = $this->pollVariable->options;
        $options = $this->pollVariable->options->pluck('id')->toArray();

        $pollProxy = $this->app->make('PollProxy', ['poll' => $this->pollVariable]);

        $pollProxy->voting($user1->id, $options);
        $pollProxy->voting($user2->id, $options);
        $pollProxy->voting($user3->id, $options);

        $results = $pollProxy->getResult();

        $this->assertContains(
            [
                'title'       => $optionList[0]->title,
                'voter_count' => 3,
                'total_weight'=> $optionList->count()*3,
                'percent'     => 100,
            ], $results);

    }
}