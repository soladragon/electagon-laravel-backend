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
use App\Models\election;
use App\Models\constituency;
use App\Models\candidate;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function importElection($electionID = 1167964, $provider_type = 'application/xml')
    {

        $param_data = [
            'headers' => [
               'Accept' => $provider_type,
            //    'ACCEPT' => 'application/json'
            ]
            ];

        $client = new Client();
        $response = $client->get('http://eldaddp.azurewebsites.net/resources/' . $electionID . '.xml', $param_data);
        $response = $response->getBody()->getContents();

        switch ($provider_type) {
            case 'application/xml':
                $encode_response = json_encode(simplexml_load_string($response));   
                $decode_response = json_decode($encode_response, TRUE);

                $electionName = $decode_response["primaryTopic"]["label"];
                $electionDate = $decode_response["primaryTopic"]["date"];
                $electionLink = $decode_response["primaryTopic"]["@attributes"]["href"];

                // $inputs = ['election' => $electionName, 'date' => $electionDate];
              
                // Election::create($inputs);
                // dd($decode_response);

                $electionClass = new Election;
                $electionClass->election = $electionName;
                $electionClass->date = $electionDate;
            
                $electionClass->save();
                $electionDatabaseID = $electionClass->id;

                //get the election number from the link and create a new link for constituencies
                $electionResourceNumber = substr($electionLink, strpos($electionLink, "resources/") + 10); 

                $constituenciesLink = "http://lda.data.parliament.uk/electionresults.xml?_pageSize=650&electionId=" . $electionResourceNumber . "&_page=0";
    
            // default: // Response json
            //     $encode_response = json_encode($response);   
    
            //     $decode_response = json_decode($encode_response, TRUE);
            //     return json_decode($decode_response, TRUE);   
            }

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

                        $constituencyClass = new Constituency;
                        $constituencyClass->election_id = $electionDatabaseID;
                        $constituencyClass->constituency = $constituencyName;
                        $constituencyClass->electorate = $constituencyElectorate;
                        $constituencyClass->majority = $constituencyMajority;
                        $constituencyClass->result = $constituencyResult;
                        $constituencyClass->turnout = $constituencyTurnout;

                        $constituencyClass->save();
                        $constituencyDatabaseID = $constituencyClass->id;

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
                                    $candidateResourceNumber = substr($candidateLink, strpos($candidateLink, "candidates/") + 11); 
                                    // dd($candidateResourceNumber);

                                    $candidateLink = "http://lda.data.parliament.uk/resources/" . $constituencyResourceNumber . "/" . "candidates/" . $candidateResourceNumber . ".xml";

                                    // dd($candidateLink);

                                       $response = $client->get($candidateLink, $param_data);
                                       $response = $response->getBody()->getContents();
                               
                                       switch ($provider_type) {
                                           case 'application/xml':
                                               $encode_response = json_encode(simplexml_load_string($response));   
                                               $decode_response = json_decode($encode_response, TRUE);
            
                                            //    dd($decode_response["primaryTopic"]);

                                               $candidateFullName = $decode_response["primaryTopic"]["fullName"];
                                               $candidateNumberOfVotes = $decode_response["primaryTopic"]["numberOfVotes"];
                                               $candidateParty = $decode_response["primaryTopic"]["party"];
                                               $candidateVoteChangePercentage = $decode_response["primaryTopic"]["voteChangePercentage"];
                                               $candidateOrder = $decode_response["primaryTopic"]["order"];

                                               $inputs = [
                                                   'constituency_id' => $constituencyDatabaseID, 
                                                   'full_name' => $candidateFullName, 
                                                   'votes' => $candidateNumberOfVotes, 
                                                   'party' => $candidateParty, 
                                                   'position' => $candidateOrder, 
                                                   'vote_change_percentage' => is_array($candidateVoteChangePercentage) === 1 ? $candidateVoteChangePercentage : 0,
                                                ];
                                                Candidate::create($inputs);
                                               
                                   
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

    public function getElection(){
        

    }

    public function apiGet($electionID = 1167964, $provider_type = 'application/xml'){
        {
    
            $param_data = [
                'headers' => [
                   'ACCEPT' => $provider_type,
                //    'ACCEPT' => 'application/json'
                ]
                ];
    
            $client = new Client();
            $response = $client->get('http://eldaddp.azurewebsites.net/resources/' . $electionID . '.xml', $param_data);
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
        
                default: // Response json
                    $encode_response = json_encode($response);   
        
                    $decode_response = json_decode($encode_response, TRUE);
                    return json_decode($decode_response, TRUE);   
                }
    
                $response = $client->get($constituenciesLink, $param_data);
                $response = $response->getBody()->getContents();
        
                switch ($provider_type) {
                    case 'application/xml':
                        $encode_response = json_encode(simplexml_load_string($response));   
                        $decode_response = json_decode($encode_response, TRUE);

                    default: // Response json
                        $encode_response = json_encode($response);   
            
                        $decode_response = json_decode($encode_response, TRUE);
                        return json_decode($decode_response, TRUE);   
                    }
    
    
    
    
    
    
            
            return $response;
            // return view('products/show',compact('response'));
        }
    }
}
