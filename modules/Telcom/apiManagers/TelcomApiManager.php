<?php

namespace Telcom\apiManagers;

class TelcomApiManager
{
    
    public function doOutgoingCall($number) {
        $currentUser = \Users_Record_Model::getCurrentUserModel();
        $telcomUserId = $currentUser->get('telcom_id');
        if(empty($telcomUserId)) {
            throw new \Exception('No Telcom id in profile');
        }
        
        $response = $this->sendRequest(
            \Settings_Telcom_Record_Model::getTelcomSipRealm(), 
            $number, 
            $telcomUserId, 
            \Settings_Telcom_Record_Model::getTelcomSipPassword()
        );
        
        if(empty($response)) {
            throw new \Exception('No communication with provider');
        }
        
        $decodedResponse = json_decode($response);
        if($decodedResponse != null && !empty($decodedResponse->error)) {
            throw new \Exception('Invalid provider parameters');
        }
    }

       protected function sendRequest($apiUrl, $number, $user, $token) {
        $options = array(
            CURLOPT_URL            => $apiUrl,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Basic '. base64_encode($user . ":" . $token)
            ],
            CURLOPT_POSTFIELDS => array(
                'cmd' => 'makeCall',
                'phone' => $number,
            )
        );
        
        $ch = curl_init();
        curl_setopt_array($ch, $options);
        $response = curl_exec($ch);
        $error = curl_error($ch);
        $this->_httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($error) {
            throw new \Exception($error);
        }
        
        return $response;
    }
}