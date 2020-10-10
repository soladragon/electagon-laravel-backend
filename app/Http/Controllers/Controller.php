<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Http\Requests;
use GuzzleHttp\Client;
use GuzzleHttp\Message\Request;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Exception\GuzzleException;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function importElection()
    {

        $electionID = 1;

        $provider_type = 'application/xml';

        $param_data = [
            'headers' => [
               'Accept' => $provider_type,
               'ACCEPT' => 'application/json'
            ]
            ];

        $client = new Client();
        $response = $client->get('http://eldaddp.azurewebsites.net/resources/1167964.xml', $param_data);
        $response = $response->getBody()->getContents();

        switch ($provider_type) {
            case 'application/xml':
                $encode_response = json_encode(simplexml_load_string($response));   
    
                $decode_response = json_decode($encode_response, TRUE);
                // dd($decode_response["@attributes"]);
                return $decode_response;
    
            default: // Response json
                $encode_response = json_encode($response);   
    
                $decode_response = json_decode($encode_response, TRUE);
                return json_decode($decode_response, TRUE);   
            }
        
        return $response;
        // return view('products/show',compact('response'));
    }
}
