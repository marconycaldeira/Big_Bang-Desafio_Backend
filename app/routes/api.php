<?php

use Illuminate\Http\Request;

Route::get('/getplaylist', 'Api\\PlaylistController@getPlaylistInJSONFile');