<?php
namespace OrangeShadow\Polls;

use Illuminate\Database\Eloquent\Model;
use OrangeShadow\Polls\Option;
use App\User;

class Vote extends  Model
{
    protected $fillable = [
        'user_id',
        'option_id',
        'weight'
    ];

    public function option()
    {
        return $this->belongsTo(Option::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}