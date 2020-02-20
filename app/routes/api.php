<?php

use Illuminate\Http\Request;

Route::get('/getplaylist/city/{city?}', 'Api\\PlaylistController@getPlaylistByCityName');
Route::get('/getplaylist/coordinates/{latitude?}/{longitude?}', 'Api\\PlaylistController@getPlaylistByCoordinates');