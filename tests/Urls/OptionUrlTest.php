<?php

namespace OrangeShadow\Polls\Test\Urls;

use OrangeShadow\Polls\Poll;
use OrangeShadow\Polls\Option;
use OrangeShadow\Polls\Test\Models\User;
use OrangeShadow\Polls\Test\TestCase;

class OptionUrl extends TestCase
{

    protected $poll;
    protected $user;

    public function setUp()
    {
        parent::setUp();

        $this->poll = Poll::create ([
            'title'     => 'Who is the best football player in the world?',
            'active'    => 1,
            'anonymous' => 0,
            'position'  => 1,
            'type'      => 'OrangeShadow\\Polls\\Types\\SingleVote',
        ]);

        $this->user = User::find(1);
    }

    public function testOptionStoreUrl()
    {
        $data = [
            'title'     => 'Messi',
            'poll_id'   => $this->poll->id,
            'position'  => 1
        ];

        $this->actingAs($this->user)->post('/option', $data, [
            'Accept'        => 'application/json'
        ])->assertStatus(200);

        $this->assertDatabaseHas('options', $data);
    }


    public function testOptionShowUrl()
    {

        $data = [
            'title'     => 'Messi',
            'poll_id'   => $this->poll->id,
            'position'  => 1
        ];

        $option =  Option::create($data);


        $this->actingAs($this->user)->get('/option/'.$option->id, [
            'Accept'        => 'application/json'
        ])->assertStatus(200)->assertJson($data);
    }


    public function testOptionUpdateUrl()
    {
        $data = [
            'title'     => 'Messi',
            'poll_id'   => $this->poll->id,
            'position'  => 1
        ];
        $option = Option::create ($data);

        $data['title'] = 'Ronaldo';

        $this->actingAs($this->user)->put('/option/'.$option->id,$data, [
            'Accept'        => 'application/json'
        ])->assertStatus(200)->assertJson($data);


        $this->assertDatabaseHas('options', $data);
    }

    public function testOptionDeleteUrl()
    {
        $data = [
            'title'     => 'Messi',
            'poll_id'   => $this->poll->id,
            'position'  => 1
        ];
        $option = Option::create ($data);


        $this->actingAs($this->user)->delete('/option/'.$option->id,$data, [
            'Accept'        => 'application/json'
        ])->assertStatus(200)->assertJson(['success'=>true]);


        $this->assertDatabaseMissing('options', ['id'=>$option->id]);
    }


    public function testOptionIndexUrl()
    {
        $data = [
            'title'     => 'Messi',
            'poll_id'   => $this->poll->id,
            'position'  => 1
        ];
        Option::create ($data);


        $data1 = [
            'title'     => 'Ronaldo',
            'poll_id'   => $this->poll->id,
            'position'  => 2
        ];

        Option::create ($data1);


        $result = $this->actingAs($this->user)->get('/option',[
            'Accept'        => 'application/json'
        ])->assertStatus(200);

        $result->assertJsonFragment($data);
        $result->assertJsonFragment($data1);

    }


    public function testOptionFilterByPollUrl()
    {
        $data = [
            'title'     => 'Messi',
            'poll_id'   => $this->poll->id,
            'position'  => 1
        ];
        Option::create ($data);


        $data1 = [
            'title'     => 'Ronaldo',
            'poll_id'   => $this->poll->id,
            'position'  => 2
        ];

        Option::create ($data1);


        $poll2 = Poll::create ([
            'title'     => 'What programming language do you use in your job?',
            'active'    => 1,
            'anonymous' => 0,
            'position'  => 2,
            'type'      => 'OrangeShadow\\Polls\\Types\\MultiVote',
        ]);

        $wData = [
            'title'     => 'PHP',
            'poll_id'   => $poll2->id,
            'position'  => 1
        ];
        Option::create ($wData);


        $wData1 = [
            'title'     => 'Node.js',
            'poll_id'   => $poll2->id,
            'position'  => 2
        ];
        Option::create ($wData1);

        $wData2 = [
            'title'     => 'Python',
            'poll_id'   => $poll2->id,
            'position'  => 3
        ];
        Option::create ($wData2);

        $wData3 = [
            'title'     => 'Java',
            'poll_id'   => $poll2->id,
            'position'  => 4
        ];
        Option::create ($wData3);


        $result = $this->actingAs($this->user)->get('/option?poll_id='.$this->poll->id,[
            'Accept'        => 'application/json'
        ])->assertStatus(200);

        $result->assertJsonFragment($data);
        $result->assertJsonFragment($data1);

        $result->assertJsonMissingExact($wData);
        $result->assertJsonMissingExact($wData1);
        $result->assertJsonMissingExact($wData2);
        $result->assertJsonMissingExact($wData3);

    }


}