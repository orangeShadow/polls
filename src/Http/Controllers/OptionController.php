<?php
namespace OrangeShadow\Polls\Http\Controllers;


use OrangeShadow\Polls\Http\Controllers\Controller;
use Illuminate\Http\Request;
use OrangeShadow\Polls\Option;


class OptionController extends Controller
{

    public $validateAttribute = [
        'poll_id'  => 'required',
        'title'    => 'required',
        'position' => 'nullable | integer',
    ];

    /**
     * Set translate for attributes
     *
     * @return array
     */
    public function customAttributes()
    {
        return [
            'poll_id'  => trans('polls.option.poll_id'),
            'title'    => trans('polls.option.title'),
            'position' => trans('polls.option.position'),
        ];
    }

    /**
     * List of option
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request)
    {

        if ($request->has('all')) {
            $data = Option::search($request)->get();
        } else {
            $data = Option::search($request)->paginate(config('polls.paginate'));
        }

        return response($data);
    }


    /**
     * Store option
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function store(Request $request)
    {
        $option = Option::create($request->all());

        return response($option);
    }


    /**
     * Get option
     *
     * @param Option $option
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function show(Option $option)
    {
        return response($option);
    }


    /**
     * Update option
     *
     * @param Option  $option
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function update(Option $option, Request $request)
    {
        $option->fill($request->all());
        $option->save();

        return response($option);
    }

    /**
     * Delete option
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function delete($id)
    {
        $result = Option::destroy($id);

        return response(['success' => $result]);
    }
}