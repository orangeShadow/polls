<?php

namespace OrangeShadow\Polls\Test\Urls;

use OrangeShadow\Polls\Test\TestCase;
use OrangeShadow\Polls\Test\Models\User;
use OrangeShadow\Polls\Poll;
use OrangeShadow\Polls\Option;


class VoteUrl extends TestCase
{

    protected $pollSingle;
    protected $pollMulti;
    protected $pollVariable;
    protected $user;

    public function setUp()
    {
        parent::setUp();

        $this->seedPolls();
    }

    public function testSingleVoteUrl()
    {

        $options = $this->pollSingle->options;

        $option = $options->random();

        $this->actingAs($this->user)->post("/poll/{$this->pollSingle->id}/vote", [
            'options' => [$option->id],
            'Accept' => 'application/json'
        ])->assertJson(['success' => true])->assertStatus(200);

        $this->assertDatabaseHas('votes', [
            'user_id'   => $this->user->id,
            'option_id' => $option->id,
            'weight'    => 1
        ]);
    }

    public function testMultiVoteUrl()
    {

        $options = $this->pollMulti->options;

        $option = [];
        array_push($option, $options[0]->id);
        array_push($option, $options[2]->id);

        $this->actingAs($this->user)->post("/poll/{$this->pollMulti->id}/vote", [
            'options' => $option,
            'Accept' => 'application/json'
        ])->assertJson(['success' => true])->assertStatus(200);

        $this->assertDatabaseHas('votes', [
            'user_id'   => $this->user->id,
            'option_id' => $options[0]->id,
            'weight'    => 1
        ]);

        $this->assertDatabaseHas('votes', [
            'user_id'   => $this->user->id,
            'option_id' => $options[2]->id,
            'weight'    => 1
        ]);
    }


    public function testVariableVoteUrl()
    {

        $options = $this->pollVariable->options;

        $options = $options->shuffle()->pluck('id')->toArray();

        $results = $this->actingAs($this->user)->post("/poll/{$this->pollVariable->id}/vote", [
            'options' => $options,
            'Accept' => 'application/json'
        ])->assertJson(['success' => true])->assertStatus(200);


        foreach ($options as $key => $option) {
            $this->assertDatabaseHas('votes', [
                'user_id'   => $this->user->id,
                'option_id' => $option,
                'weight'    => count($options) - $key
            ]);
        }

    }

}