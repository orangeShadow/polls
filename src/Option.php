<?php
namespace OrangeShadow\Polls;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Option extends Model
{
    protected $fillable = [
        'poll_id',
        'title',
        'position'
    ];

    public $casts = [
        'poll_id'   => 'integer',
        'position'  => 'integer'
    ];

    public $timestamps = false;

    /**
     * @param $query
     * @param Request $request
     */
    public function scopeSearch($query,Request $request)
    {
        if ( $request->has('id') ) {
            $query->where('id',$request->get('id'));
        }

        if ( $request->has('poll_id') ) {
            $query->where('poll_id',$request->get('poll_id'));
        }

        if ( $request->has('position') ) {
            $query->where('position',$request->get('position'));
        }

        if ( $request->has('title') ) {
            $query->where('title','like',"%{$request->get('title')}%");
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function poll()
    {
        return $this->belongsTo(Poll::class);
    }
}
