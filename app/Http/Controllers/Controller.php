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
                $electionName = $decode_response["primaryTopic"]["label"];
                $data = $decode_response["primaryTopic"]["@attributes"]["href"];

                //get the election number from the link and create a new link for constituencies
                $electionResourceNumber = substr($data, strpos($data, "resources/") + 10); 

                $constituenciesLink = "http://lda.data.parliament.uk/electionresults.xml?_pageSize=650&electionId=" . $electionResourceNumber . "&_page=0";


                // dd($constituenciesLink);

                // return $decode_response["primaryTopic"];
    
            // default: // Response json
            //     $encode_response = json_encode($response);   
    
            //     $decode_response = json_decode($encode_response, TRUE);
            //     return json_decode($decode_response, TRUE);   
            }


            //constituencies

            // dd($constituenciesLink);

            $response = $client->get($constituenciesLink, $param_data);
            $response = $response->getBody()->getContents();
    
            switch ($provider_type) {
                case 'application/xml':
                    $encode_response = json_encode(simplexml_load_string($response));   
        
                    $decode_response = json_decode($encode_response, TRUE);

                    foreach ($decode_response["items"]["item"] as $constituency){

                        $constituencyName = $constituency["constituency"]["label"];
                        $constituencyCandidatesLink = $constituency["@attributes"]["href"];
                        $constituencyElectorate = $constituency["electorate"];
                        $constituencyMajority = $constituency["majority"];
                        $constituencyResult = $constituency["resultOfElection"];
                        $constituencyTurnout = $constituency["turnout"];

                        // dd($constituency);

                        $constituencyResourceNumber = substr($constituencyCandidatesLink, strpos($constituencyCandidatesLink, "resources/") + 10); 

                        $candiatesLink = "http://lda.data.parliament.uk/resources/" . $constituencyResourceNumber . ".xml";

                       


                           $response = $client->get($candiatesLink, $param_data);
                           $response = $response->getBody()->getContents();
                   
                           switch ($provider_type) {
                               case 'application/xml':
                                   $encode_response = json_encode(simplexml_load_string($response));   
                       
                                   $decode_response = json_decode($encode_response, TRUE);

                                //    dd($decode_response["primaryTopic"]["candidate"]["item"]);
               
                                   foreach ($decode_response["primaryTopic"]["candidate"]["item"] as $candidate){

                                    $candidateLink = $candidate["@attributes"]["href"];
               
                                    //    $constituencyName = $constituency["constituency"]["label"];
                                    //    $constituencyCandidatesLink = $constituency["constituency"]["@attributes"]["href"];
                                    //    $constituencyElectorate = $constituency["electorate"];
                                    //    $constituencyMajority = $constituency["majority"];
                                    //    $constituencyResult = $constituency["resultOfElection"];
                                    //    $constituencyTurnout = $constituency["turnout"];
                                    $candidateResourceNumber = substr($candidateLink, strpos($candidateLink, "candidates/") + 11); 
                                    // dd($candidateResourceNumber);
                                    $candidateLink = "http://lda.data.parliament.uk/resources/" . $constituencyResourceNumber . "/" . "candidates/" . $candidateResourceNumber . ".xml";

                                       $response = $client->get($candidateLink, $param_data);
                                       $response = $response->getBody()->getContents();
                               
                                       switch ($provider_type) {
                                           case 'application/xml':
                                               $encode_response = json_encode(simplexml_load_string($response));   
                                               $decode_response = json_decode($encode_response, TRUE);
            
                                               dd($decode_response["primaryTopic"]);

                                               $candidateFullName = $decode_response["primaryTopic"]["fullName"];
                                               $candidateNumberOfVotes = $decode_response["primaryTopic"]["numberOfVotes"];
                                               $candidateParty = $decode_response["primaryTopic"]["party"];
                                               $candidateVoteChangePercentage = $decode_response["primaryTopic"]["voteChangePercentage"];
                                               $candidateOrder = $decode_response["primaryTopic"]["order"];
                                               
                                   
                                        //    default: // Response json
                                        //        $encode_response = json_encode($response);   
                                   
                                        //        $decode_response = json_decode($encode_response, TRUE);
                                        //        return json_decode($decode_response, TRUE);   
                                           }


               
                                   }
                       
                            //    default: // Response json
                            //        $encode_response = json_encode($response);   
                       
                            //        $decode_response = json_decode($encode_response, TRUE);
                            //        return json_decode($decode_response, TRUE);   
                               }

                    }
        
                // default: // Response json
                //     $encode_response = json_encode($response);   
        
                //     $decode_response = json_decode($encode_response, TRUE);
                //     return json_decode($decode_response, TRUE);   
                }






        
        return $response;
        // return view('products/show',compact('response'));
    }
}
