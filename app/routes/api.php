<?php

use Illuminate\Http\Request;

Route::get('/playlist/city/{city?}', 'Api\\PlaylistController@getPlaylistByCityName');
Route::get('/playlist/coordinates/{latitude?}/{longitude?}', 'Api\\PlaylistController@getPlaylistByCoordinates');