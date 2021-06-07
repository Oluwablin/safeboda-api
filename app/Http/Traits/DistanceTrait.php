<?php

namespace App\Http\Traits;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Client\ConnectionException;

trait DistanceTrait
{

    // Using Haversine formula to calculate the shortest distance between both coordinates.

    protected function get_distance(array $point1, array $point2)
    {

        // earth radius in km
        $earth_radius = 6371;
        $point1_lat = $point1["latitude"];
        $point2_lat = $point2["latitude"];

        $lat_diff = deg2rad($point2_lat - $point1_lat);
        $point1_long = $point1["longitude"];
        $point2_long = $point2["longitude"];

        $long_diff = deg2rad($point2_long - $point1_long);

        $a = sin($lat_diff / 2) * sin($lat_diff / 2) + cos(deg2rad($point1_lat)) * cos(deg2rad($point2_lat)) * sin($long_diff / 2) * sin($long_diff / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        $distance = $earth_radius * $c;
        $distance = round($distance, 2);

        // in km
        return $distance;
    }

    // Fetch the latitude and longitude of input address
    protected function get_latitude_and_longitude($address)
    {
        try {
            $client = new Client();

            $res = $client->request('GET', 'https://maps.googleapis.com/maps/api/geocode/json', [
                'query' =>  [
                    'address' => urlencode($address),
                    'key' => config('services.google-map.apikey')
                ]
            ]);

            $response = json_decode(file_get_contents($res->getBody()));

            if ($response->status == 'ZERO_RESULTS') {
                return null;
            }

            $latitude = $response->results[0]->geometry->location->lat;
            $longitude = $response->results[0]->geometry->location->lng;

            $coordinates = [
                "latitude" => $latitude,
                "longitude" => $longitude
            ];

            return $coordinates;
        } catch (RequestException $e) {
            $response = $e->getResponse();
            $result =  $response->getBody();

            $data = [
                'status' => 400,
                'response' => $response,
                'result' => $result
            ];

            return $data;
        } catch (ClientException $e) {
            $response = $e->getResponse();
            $result =  $response->getBody();

            $data = [
                'status' => 400,
                'response' => $response,
                'result' => $result
            ];

            return $data;
        } catch (ConnectionException $e) {
            $result =  $response->getBody();

            $data = [
                'status' => 400,
                'result' => $result
            ];

            return $data;
        }
    }
}
