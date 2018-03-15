<?php
namespace OrangeShadow\Polls;

use Illuminate\Database\Eloquent\Model;
use OrangeShadow\Polls\Option;

class Poll extends Model
{

    public $fillable = [
        'active',
        'anonymous',
        'position',
        'title',
        'type',
        'closed_at'
    ];


    public $casts = [
        'id'        => 'integer',
        'active'    => 'integer',
        'anonymous' => 'integer',
        'position'  => 'integer',
        'type'      => 'string',
        'closed_at' => 'datetime'
    ];




    public function scopeSearch($query, $request)
    {
        if ($request->has('id')) {
            $query->where('id', $request->get('id'));
        }

        if ($request->has('active')) {
            $query->where('active', $request->get('active'));
        }

        if ($request->has('anonymous')) {
            $query->where('anonymous', $request->get('anonymous'));
        }

        if ($request->has('position')) {
            $query->where('position', $request->get('position'));
        }

        if ($request->has('type')) {
            $query->where('type', $request->get('type'));
        }

        if ($request->has('title')) {
            $query->where('title', 'like', "{$request->get('title')}");
        }

        if ($request->has('closed_at')) {
            $query->where('closed_at', "{$request->get('closed_at')}");
        }

        if ($request->has('closed_at_from') && $request->has('closed_at_to')) {
            $query->whereBetween('closed_at', [$request->get('closed_at_from'), $request->get('closed_at_to')]);
        }

    }

    public function options()
    {
        return $this->hasMany(Option::class);
    }


    /**
     * Check poll for closure
     * @return bool
     */
    public function isClosed()
    {
        if (!$this->attributes['active']) return true;

        $now = time();

        if( !empty($this->closed_at) && $now - strtotime($this->closed_at) > 0)
        {
            return true;
        }

        return false;

    }


    /**
     * Get options count
     * @return int
     */
    public function getOptionsCount()
    {
        return $this->options()->count();
    }

    /**
     * Get poll votes
     * @return mixed
     */
    public function getVotes()
    {
        return $this->join('options','options.poll_id','=','polls.id')
                        ->join('votes','votes.option_id','=','options.id')
                        ->select(['votes.option_id','votes.user_id','weight'])
                        ->get()->toArray();
    }

    /**
     * Get Voter Count
     * @return mixed
     */
    public function getVoterCount()
    {
        return $this->join('options','options.poll_id','=','polls.id')
            ->join('votes','votes.option_id','=','options.id')
            ->select(\DB::raw('count(DISTINCT votes.user_id) as cnt'))
            ->get()[0]->cnt;
    }


}
