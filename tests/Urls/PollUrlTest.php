<?php

namespace OrangeShadow\Polls\Test\Urls;


use OrangeShadow\Polls\Test\Models\User;
use OrangeShadow\Polls\Poll;
use OrangeShadow\Polls\Test\TestCase;

class PollUrlTest extends TestCase
{

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

        $this->actingAs($user)->post('/poll', $data, [
            'Accept'        => 'application/json'
        ])->assertStatus(200);

        $this->assertDatabaseHas('polls', $data);
    }


    public function testPollShowUrl()
    {
        $data = [
            'title'     => 'Who is the best football player in the world?',
            'active'    => 1,
            'anonymous' => 0,
            'position'  => 1,
            'type'      => 'OrangeShadow\\Polls\\Types\\SingleVote',
        ];
        $poll = Poll::create ($data);


        $user = User::find(1);

        $this->actingAs($user)->get('/poll/'.$poll->id, [
            'Accept'        => 'application/json'
        ])->assertStatus(200)->assertJson($data);
    }


    public function testPollUpdateUrl()
    {
        $data = [
            'title'     => 'Who is the best soccer player in the world?',
            'active'    => 1,
            'anonymous' => 0,
            'position'  => 1,
            'type'      => 'OrangeShadow\\Polls\\Types\\SingleVote',
        ];
        $poll = Poll::create ($data);


        $user = User::find(1);

        $data['title'] = 'Who is the best football player in the world?';

        $this->actingAs($user)->put('/poll/'.$poll->id,$data, [
            'Accept'        => 'application/json'
        ])->assertStatus(200)->assertJson($data);


        $this->assertDatabaseHas('polls', $data);
    }

    public function testPollDeleteUrl()
    {
        $data = [
            'title'     => 'Who is the best soccer player in the world?',
            'active'    => 1,
            'anonymous' => 0,
            'position'  => 1,
            'type'      => 'OrangeShadow\\Polls\\Types\\SingleVote',
        ];
        $poll = Poll::create ($data);


        $user = User::find(1);


        $this->actingAs($user)->delete('/poll/'.$poll->id,$data, [
            'Accept'        => 'application/json'
        ])->assertStatus(200)->assertJson(['success'=>true]);


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


        $result = $this->actingAs($user)->get('/poll',[
            'Accept'        => 'application/json'
        ])->assertStatus(200);

        print_r($result->getContent());

        $result->assertJsonFragment($data);
        $result->assertJsonFragment($data1);

    }

}