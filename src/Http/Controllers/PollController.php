<?php
namespace OrangeShadow\Polls\Http\Controllers;


use DateTime;
use Illuminate\Http\Request;
use OrangeShadow\Polls\Poll;


class PollController extends Controller
{

    public $validateFields = [
        'active'    => 'numeric',
        'anonymous' => 'numeric',
        'position'  => 'numeric',
        'title'     => 'required',
        'type'      => 'required'
    ];

    public function customAttributes()
    {
        return [
            'active'    => trans('polls.poll.active'),
            'anonymous' => trans('polls.poll.anonymous'),
            'position'  => trans('polls.poll.position'),
            'title'     => trans('polls.poll.title'),
            'type'      => trans('polls.poll.type'),
            'closed_at' => trans('polls.poll.closed_at')
        ];
    }


    /**
     * List of polls
     */
    public function index(Request $request)
    {

        if ($request->has('all')) {
            $data = Poll::search($request)->all();
        } else {
            $data = Poll::search($request)->paginate(config('polls.paginate'));
        }

        return response($data);

    }

    /**
     * Store poll
     */
    public function store(Request $request)
    {

        $this->validate($request, $this->validateFields, [], $this->customAttributes());

        $poll = Poll::create($request->all());

        return response($poll);

    }

    /**
     * Show poll
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function show(Poll $poll)
    {
        return response($poll);
    }

    /**
     * Update poll
     */
    public function update(Poll $poll, Request $request)
    {;
        $this->validate($request, $this->validateFields, [], $this->customAttributes());

        $poll->fill($request->all());
        $poll->save();

        return response($poll);
    }


    /**
     * Delete poll
     */
    public function delete($id)
    {

        $result = Poll::destroy($id);

        return response([
            'success' => $result
        ]);
    }

    /**
     * Close poll
     */
    public function close(Poll $poll)
    {
        $dt = new DateTime();
        $poll->closed_at = $dt->format('Y-m-d H:i:s');
        $poll->save();

        return response($poll);
    }

}