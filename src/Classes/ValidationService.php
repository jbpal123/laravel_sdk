<?php 

namespace Mahindra\Cc_auth\Classes;

class ValidationService
{
    public function verifyJWT($jwtToken, $env)
    {        
        $validateURL = "https://ccservice.mahindra.com";
        if ($env== 'DEV') {
            $validateURL = "https://ccservice-dev.m-devsecops.com";
        } elseif ($env == 'UAT') {
            $validateURL = "https://ccservice-dev.m-devsecops.com";
        }
        $validateURL = $validateURL."/authservice/api/v1/auth/validate";

        $curl = curl_init();
        
        curl_setopt_array($curl, array(
        CURLOPT_URL => $validateURL,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => array('jwt_token' => $jwtToken),));
        
        return $curl;

        

    }
}