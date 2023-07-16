<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class Controller extends BaseController
{
    public function jsonResult(Request $request)
    {
        $buyer = json_decode(file_get_contents(storage_path('app/json1.json')), true);
        $dealer = json_decode(file_get_contents(storage_path('app/json2.json')), true);
        // echo '<pre>' . print_r($buyer['data'][0]['name'], true) . '</pre>';
        // exit;
        $arr = [];

        for ($i = 0; $i < sizeof($buyer['data']); $i++) {
            $json_tampung = new \stdClass();
            $json_tampung->name = $buyer['data'][$i]['name'] ?: '';
            $json_tampung->email = $buyer['data'][$i]['email'] ?: '';
            $json_tampung->booking_number = $buyer['data'][$i]['booking']['booking_number'] ?: '';
            $json_tampung->book_date = $buyer['data'][$i]['booking']['book_date'] ?: '';
            $json_tampung->ahass_code = $buyer['data'][$i]['booking']['workshop']['code'] ?: '';
            $json_tampung->ahass_name = $buyer['data'][$i]['booking']['workshop']['name'] ?: '';


            //nilai default jika data dealer tdk ditemukan
            $json_tampung->ahass_address = '';
            $json_tampung->ahass_contact = '';
            $json_tampung->ahass_distance = 0;

            for ($j = 0; $j < sizeof($dealer['data']); $j++) {
                if ($dealer['data'][$j]['code'] == $json_tampung->ahass_code) {
                    $json_tampung->ahass_address = $dealer['data'][$j]['address'];
                    $json_tampung->ahass_contact = $dealer['data'][$j]['phone_number'];
                    $json_tampung->ahass_distance = $dealer['data'][$j]['distance'];
                    break;
                }
            }

            $json_tampung->motorcycle_ut_code = $buyer['data'][$i]['booking']['motorcycle']['ut_code'];
            $json_tampung->motorcycle = $buyer['data'][$i]['booking']['motorcycle']['name'];
            $arr[$i] =  $json_tampung;
        }

        usort($arr, function ($a, $b) {
            return $a->ahass_distance < $b->ahass_distance ? -1 : 1;
        });

        $arr_final = [
            'status' => 1,
            'message' => "Data Successfully Retrieved.",
            'data' => $arr,
        ];

        echo '<pre>' . json_encode($arr_final) . '</pre>';
    }
}
