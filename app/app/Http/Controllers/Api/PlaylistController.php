<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PlaylistController extends Controller
{
    protected $urlBaseApiOpenWeatherMap;
    protected $urlBaseApiOpenWeatherMapWithParams;
    protected $apiKey;
    protected $params;
    
    function __construct()
    {
        $this->urlBaseApiOpenWeatherMap = 'http://api.openweathermap.org/data/2.5/weather';
        $this->apiKey = env('API_KEY_OPENWHEATER');

        $this->addParamToUrlBaseApiOpenWeatherMap('appid', $this->apiKey);
    }
    
    public function getPlaylistByCityName($city = null){
        if($city == null){
            return response()->json(['status' => false, 'message' => 'The city name is required.'], 422);
        }
        $this->addParamToUrlBaseApiOpenWeatherMap('q', $city);

        $response = file_get_contents($this->urlBaseApiOpenWeatherMapWithParams);
        $data = json_decode($response);

        return response()->json($data, 200);
    }
    public function getPlaylistByCoordinates($latitude = null, $longitude = null){
        if($latitude == null || $longitude == null){
            return response()->json(['status' => false, 'message' => 'The latitude and the longitute are required.'], 422);
        }else{
            if(!$this->validateLatitude($latitude)){
                return response()->json(['status' => false, 'message' => 'The latitude is invalid.'], 422);
            }else if(!$this->validateLongitude($longitude)){
                return response()->json(['status' => false, 'message' => 'The longitude is invalid.'], 422);
            }
        }
        $this->addParamToUrlBaseApiOpenWeatherMap('lat', $latitude)
             ->addParamToUrlBaseApiOpenWeatherMap('lon', $longitude);
             
        $response = file_get_contents($this->urlBaseApiOpenWeatherMapWithParams);
        $data = json_decode($response);
        return response()->json(['latitude' => $latitude, 'longitude' => $longitude, 'data' => $data], 200);
    }
    function validateLatitude($lat) {
        return preg_match('/^(\+|-)?(?:90(?:(?:\.0{1,6})?)|(?:[0-9]|[1-8][0-9])(?:(?:\.[0-9]{1,6})?))$/', $lat);
    }
    function validateLongitude($long) {
        return preg_match('/^(\+|-)?(?:180(?:(?:\.0{1,6})?)|(?:[0-9]|[1-9][0-9]|1[0-7][0-9])(?:(?:\.[0-9]{1,6})?))$/', $long);
    }
    function kelvinToCeucius($value){
        return $value - 273.15;
    }
    function addParamToUrlBaseApiOpenWeatherMap($key, $value){
        $this->params[$key] = $value;

        $this->urlBaseApiOpenWeatherMapWithParams = $this->urlBaseApiOpenWeatherMap.'?';
        foreach($this->params as $index => $row){
            $this->urlBaseApiOpenWeatherMapWithParams .= '&'.$index.'='.urlencode($row);
        }
        return $this;
    }
}
