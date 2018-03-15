<?php

$prefix = config('polls.route_prefix');
$middleware = array_merge(config('polls.admin_route_middleware'),['bindings']);

Route::group([
    'namespace' => 'OrangeShadow\Polls\Http\Controllers',
    'prefix' => $prefix,
    'middleware' => $middleware
], function() {
    Route::post('poll/{poll}/close','PollController@close');
    Route::post('poll/{poll}/vote','VoteController@vote');

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