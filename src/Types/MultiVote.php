<?php

namespace OrangeShadow\Polls\Types;

use OrangeShadow\Polls\Exceptions\AlreadyCastYourVoteException;
use OrangeShadow\Polls\Exceptions\OptionTypeException;
use OrangeShadow\Polls\Exceptions\OptionIsNotFoundException;
use OrangeShadow\Polls\Vote;
use OrangeShadow\Polls\PollTypeAbstract;
use Illuminate\Support\Facades\DB;


class MultiVote extends PollTypeAbstract
{

    public function voting(int $user_id, $options)
    {
        if(!is_array($options))
            throw new OptionTypeException();

        if (!$this->poll->options()->pluck('id')->contains(function($item) use ($options){
           return in_array($item,$options);
        }))
            throw new OptionIsNotFoundException();


        $votes = Vote::leftJoin('options', 'options.id', '=', 'votes.option_id')->where('options.poll_id', '=', $this->poll->id)->where('user_id', $user_id)->get();

        if (!$votes->isEmpty())
            throw new AlreadyCastYourVoteException();

        $weight = 1;

        foreach ($options as $option) {
            Vote::create([
                'option_id' => $option,
                'user_id'   => $user_id,
                'weight'    => $weight
            ]);
        }


        return true;
    }

    /**
     * Get poll results
     * @return array
     */
    public function getResult()
    {
        $results = $this->poll->options->mapWithKeys(function ($item) {
            return [$item['id'] => [ 'title'=>$item['title'], 'voter_count'=>0,'total_weight'=>0,'percent'=>null]];
        })->toArray();

        $votes = $this->poll->getVotes();

        foreach ($votes as $vote) {
            $results[$vote['option_id']]['voter_count']  += 1;
            $results[$vote['option_id']]['total_weight'] += $vote['weight'];
        }

        $votersCount = $this->poll->getVoterCount();

        $results = array_map(function($item) use ($votersCount){

            if($item['total_weight']>0) {
                $item['percent'] = round($item['total_weight']/$votersCount,4)*100;
            } else{
                $item['percent'] = 0;
            }
            return $item;
        },$results);

        uasort($results,function($a,$b){
            return $a['percent']<=$b['percent'];
        });

        return $results;
    }
}