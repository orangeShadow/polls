<?php
namespace OrangeShadow\Polls\Http\Controllers;

use OrangeShadow\Polls\Exceptions\Exception;
use Illuminate\Http\Request;
use OrangeShadow\Polls\Poll;

class VoteController extends Controller
{

    public $validateFields = [
        'options' => 'required|array',
    ];

    public function customAttributes()
    {
        return [
            'options' => trans('polls.vote.options'),
        ];
    }


    /**
     * Store vote
     * @param Poll $poll
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function vote(Poll $poll,Request $request)
    {
        try {
            $this->validate($request, $this->validateFields, [], $this->customAttributes());

            $pollWriter = app()->make('PollProxy', ['poll' => $poll]);

            $pollWriter->voting($request->user()->id, $request->get('options'));

        } catch (Exception $e) {
            return response(['error'=>true,'message'=>$e->getMessage()],500);
        }

        return response(['success' => true,'results'=>$pollWriter->getResult()]);

    }

}