<?php

namespace App\Http\Controllers\Api;

use SpotifyWebAPI\Session;
use Illuminate\Http\Request;
use SpotifyWebAPI\SpotifyWebAPI;
use App\Http\Controllers\Controller;

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
        
        if($data == false || $data == null){
            return response()->json(['status' => false, 'message' => 'Empty results for this parameters.'], 422);
        }
        
        $tempInKelvin = (float) $data->main->temp;
        $tempInCeucius = $this->kelvinToCeucius($tempInKelvin);
        $getGenderByTemp = $this->getGenderByTemp($tempInCeucius);
        $tracks = $this->getTracksFromSpotifyByGender($getGenderByTemp);
        
        return response()->json($tracks, 200);
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
        if($data == false || $data == null){
            return response()->json(['status' => false, 'message' => 'Empty results for this parameters.'], 422);
        }
        $tempInKelvin = (float) $data->main->temp;
        $tempInCeucius = $this->kelvinToCeucius($tempInKelvin);
        $getGenderByTemp = $this->getGenderByTemp($tempInCeucius);
        $tracks = $this->getTracksFromSpotifyByGender($getGenderByTemp);
        
        return response()->json($tracks, 200);
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
    /**  
     * Se a temperatura (Celsius) estiver acima de 30 graus, sugerir músicas para festa
     * Se a temperatura está entre 15 e 30 graus, sugerir músicas do gênero Pop.
     * Entre 10 e 14 graus, sugerir músicas do gênero Rock
     * Abaixo de 10 graus, segerir músicas clássicas.
     */
    function getGenderByTemp($temp){
        $temp = (int) $temp;
        if($temp > 30){
            return ['party'];
        }
        if($temp >= 15 && $temp <= 30){
            return ['pop'];
        }
        if($temp >= 10 && $temp <= 14){
            return ['rock'];
        }
        if($temp < 10){
            return ['classic'];
        }
    }
    function getTracksFromSpotifyByGender($gender = ['party']){
        $session = new Session(
            env('CLIENT_ID_SPOTIFY'),
            env('CLIENT_SECRET_SPOTIFY')
        );
        
        $session->requestCredentialsToken();
        $accessToken = $session->getAccessToken();

        $api = new SpotifyWebAPI();
        $api->setAccessToken($accessToken);
        
        $recommendations = $api->getRecommendations([
            'seed_genres' => $gender,
            'limit'       => 50,
            'market'          => 'BR'
        ]);

        return $this->formatSpotifyResponse($recommendations);
    }
    function formatSpotifyResponse($recommendations){
        $tracks = [];

        foreach($recommendations->tracks as $index => $row){
            $tracks[$index] = $row->name;
        }
        return $tracks;
    }
}
