<?php

namespace OrangeShadow\Polls\Test\Urls;


use OrangeShadow\Polls\Test\Models\User;
use OrangeShadow\Polls\Poll;
use OrangeShadow\Polls\Test\TestCase;

class PollUrlTest extends TestCase
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


    public function testPollStoreUrl()
    {
        $data = [
            'title'     => 'Who is the best football player in the world?',
            'active'    => 1,
            'anonymous' => 0,
            'position'  => 1,
            'type'      => 'OrangeShadow\\Polls\\Types\\SingleVote',
        ];


        $user = User::find(1);

        $this->actingAs($user)->post('/'.config('polls.admin_route_prefix').'/poll', $data, [
            'Accept'        => 'application/json'
        ])->assertStatus(200);

        $this->assertDatabaseHas('polls', $data);
    }


    public function testPollShowUrl()
    {
    
        $poll = $this->pollSingle;
        $user = User::find(1);
        $this->actingAs($user)
            ->get('/'.config('polls.admin_route_prefix').'/poll/'.$poll->id, [ 'Accept' => 'application/json' ])
            ->assertStatus(200)
            ->assertJsonStructure(['id', 'options','results'])
            ->assertJson(['id'=>$poll->id]);
    }

    public function testPollShowPublicUrl()
    {
        $poll = $this->pollSingle;

        $options = $poll->options->shuffle()->pluck('id')->toArray();

        $results = $this->actingAs($this->user)->post("/poll/{$this->pollVariable->id}/vote", [
            'options' => $options,
            'Accept' => 'application/json'
        ]);

        $user = User::find(1);

        $this->actingAs($user)
            ->get('/'.config('polls.public_route_prefix').'/poll/'.$poll->id, [ 'Accept' => 'application/json' ])
            ->assertStatus(200)
            ->assertJsonStructure(['poll', 'options']);
    }


    public function testPollUpdateUrl()
    {
        $poll = $this->pollSingle;
        $user = User::find(1);

        $data=$poll->getAttributes();
        $data['title'] = 'Who is the best football player in the world?';

        $this->actingAs($user)->put('/'.config('polls.admin_route_prefix').'/poll/'.$poll->id,$data, [
            'Accept'        => 'application/json'
        ])->assertStatus(200)->assertJson($data);


        $this->assertDatabaseHas('polls', ['id'=>$poll->id,'title'=>$data['title']]);
    }

    public function testPollDeleteUrl()
    {
        $poll = $this->pollSingle;

        $user = User::find(1);


        $this->actingAs($user)
            ->delete('/'.config('polls.admin_route_prefix').'/poll/'.$poll->id, [
                'Accept'        => 'application/json'
            ])
            ->assertStatus(200)->assertJson(['success'=>true]);


        $this->assertDatabaseMissing('polls', ['id'=>$poll->id]);
    }


    public function testPollWithVoteDelete()
    {
        $poll = $this->pollSingle;

        $user = User::find(1);
        $user2 = User::find(2);

        $option = $poll->options->random();

        $this->actingAs($user)->post("/poll/{$poll->id}/vote", [
            'options' => [$option->id],
            'Accept' => 'application/json'
        ]);

        $this->actingAs($user2)->post("/poll/{$poll->id}/vote", [
            'options' => [$poll->options->random()->id],
            'Accept' => 'application/json'
        ]);


        $this->actingAs($user)
            ->delete('/'.config('polls.admin_route_prefix').'/poll/'.$poll->id, [
                'Accept'        => 'application/json'
            ])
            ->assertStatus(200)
            ->assertJson(['success'=>true]);


        $this->assertDatabaseMissing('polls', ['id'=>$poll->id]);
    }


    public function testPollIndexUrl()
    {
        $data = [
            'title'     => 'Who is the best football player in the world?',
            'active'    => 1,
            'anonymous' => 0,
            'position'  => 1,
            'type'      => 'OrangeShadow\\Polls\\Types\\SingleVote',
        ];
        Poll::create ($data);

        $data1 = [
            'title'     => 'Who is the best basketball player in the world?',
            'active'    => 1,
            'anonymous' => 0,
            'position'  => 1,
            'type'      => 'OrangeShadow\\Polls\\Types\\SingleVote',
        ];
        Poll::create ($data1);


        $user = User::find(1);


        $result = $this->actingAs($user)->get('/'.config('polls.admin_route_prefix').'/poll',[
            'Accept'        => 'application/json'
        ])->assertStatus(200);

        $result->assertJsonFragment($data);
        $result->assertJsonFragment($data1);

    }

}