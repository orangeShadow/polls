<?php

$admin_prefix = config('polls.admin_route_prefix');

if (is_array(config('polls.admin_route_middleware'))) {
    $admin_middleware = array_merge(config('polls.admin_route_middleware'),['bindings']);
} else {
    $admin_middleware = ['bindings'];
}


$public_prefix = config('polls.admin_route_prefix');

if (is_array(config('polls.admin_route_middleware'))) {
    $public_middleware = array_merge(config('polls.public_route_middleware'),['auth','bindings']);
} else {
    $public_middleware = ['bindings'];
}


Route::group([
    'namespace' => 'OrangeShadow\Polls\Http\Controllers',
    'prefix' => $admin_prefix,
    'middleware' => $admin_middleware
], function() {
    Route::post('poll/{poll}/close','PollController@close');

    Route::get('poll','PollController@index')->name('poll.index');
    Route::post('poll','PollController@store')->name('poll.store');
    Route::get('poll/{poll}','PollController@show')->name('poll.show');
    Route::put('poll/{poll}','PollController@update')->name('poll.update');
    Route::delete('poll/{poll}','PollController@delete')->name('poll.update');

    Route::get('option','OptionController@index')->name('option.index');
    Route::post('option','OptionController@store')->name('option.store');
    Route::get('option/{option}','OptionController@show')->name('option.show');
    Route::put('option/{option}','OptionController@update')->name('option.update');
    Route::delete('option/{option}','OptionController@delete')->name('option.update');
});

Route::group([
    'namespace' => 'OrangeShadow\Polls\Http\Controllers',
    'prefix' => $public_prefix,
    'middleware' => $public_middleware
], function() use ($admin_prefix, $public_prefix){
    if($admin_prefix != $public_prefix) {
        Route::get('poll/{poll}','PollController@show');            
    }
    Route::post('poll/{poll}/vote','VoteController@vote');
});