<?php

namespace OrangeShadow\Polls\Test;

use OrangeShadow\Polls\Test\Models\User;
use OrangeShadow\Polls\Poll;
use OrangeShadow\Polls\Vote;

class PollTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->seedPolls();
    }

    public function testCountOptions()
    {
        $poll =$this->pollSingle;
        $this->assertEquals($poll->getOptionsCount(),3);
    }

    public function testGetVotesCountForSingle()
    {
        $poll =$this->pollSingle;
        $this->assertEquals($poll->getVoterCount(), 0);

        $pollProxy = $this->app->make('PollProxy', ['poll' => $poll]);

        $user1 = $this->user;
        $user2 = User::find(2);
        $user3 = User::find(3);

        $pollProxy->voting($user1->id, [$poll->options[0]->id]);
        $pollProxy->voting($user2->id, [$poll->options[1]->id]);
        $pollProxy->voting($user3->id, [$poll->options[2]->id]);

        $this->assertEquals($poll->getVoterCount(), 3);
    
    }

    public function testGetVotesCountForMulty()
    {
        $poll =$this->pollMulti;
        $this->assertEquals($poll->getVoterCount(), 0);

        $user1 = $this->user;
        $user2 = User::find(2);
        $user3 = User::find(3);

        $optionList = $poll->options;
        $options = $poll->options->pluck('id')->toArray();


        $pollProxy = $this->app->make('PollProxy', ['poll' => $this->pollMulti]);

        $pollProxy->voting($user1->id, $options);
        $pollProxy->voting($user2->id, $options);
        unset($options[0]);
        $pollProxy->voting($user3->id, $options);


        $this->assertEquals($poll->getVoterCount(), 3);
    
    }
    
    public function testGetVotesForSingle()
    {
        $poll =$this->pollSingle;
    
        $pollProxy = $this->app->make('PollProxy', ['poll' => $poll]);

        $user1 = $this->user;
        $user2 = User::find(2);
        $user3 = User::find(3);

        $pollProxy->voting($user1->id, [$poll->options[0]->id]);
        $pollProxy->voting($user2->id, [$poll->options[1]->id]);
        $pollProxy->voting($user3->id, [$poll->options[2]->id]);

        $votes = Vote::join('options', 'options.id', '=', 'votes.option_id')
                     ->where('options.poll_id', '=', $poll->id)
                     ->select('option_id', 'user_id', 'weight')
                     ->get();

        $this->assertEquals($poll->getVotes(), $votes->toArray());
    
    }


    public function testHasVoted()
    {
        $poll =$this->pollSingle;
    
        $pollProxy = $this->app->make('PollProxy', ['poll' => $poll]);

        $user1 = $this->user;
        $user2 = User::find(2);
        $user3 = User::find(3);

        $pollProxy->voting($user1->id, [$poll->options[0]->id]);
        $pollProxy->voting($user2->id, [$poll->options[1]->id]);

    
        $this->assertEquals($poll->hasVoted($user1), true);
        $this->assertEquals($poll->hasVoted($user2), true);
        $this->assertEquals($poll->hasVoted($user3), false);
    
    }

}