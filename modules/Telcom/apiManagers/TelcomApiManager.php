<?php

namespace Telcom\apiManagers;

class TelcomApiManager
{

    public function doOutgoingCall($number)
    {
        $currentUser = \Users_Record_Model::getCurrentUserModel();
        $telcomUserId = $currentUser->get('telcom_id');
        if (empty($telcomUserId)) {
            throw new \Exception('No Telcom id in profile');
        }

        $response = $this->sendRequest(
            \Settings_Telcom_Record_Model::getTelcomSipRealm(),
            $telcomUserId,
            \Settings_Telcom_Record_Model::getTelcomSipPassword(),
            array('destination' => $number, 'user' => $currentUser->get('id'))
        );

        if (empty($response)) {
            throw new \Exception('No communication with provider');
        }

        $decodedResponse = json_decode($response);
        if ($decodedResponse != null && !empty($decodedResponse->error)) {
            throw new \Exception('Invalid provider parameters');
        }
    }

    public function syncUser(\Users_Record_Model $userModel)
    {
        $telcomUserId = $userModel->get('telcom_id');
        if (empty($telcomUserId)) {
            throw new \Exception('No Telcom id in profile');
        }

        $response = $this->sendRequest(
            \Settings_Telcom_Record_Model::getTelcomSipRealm(),
            $telcomUserId,
            \Settings_Telcom_Record_Model::getTelcomSipPassword(),
            array(
                'id' => $userModel->get('id'),
                'phoneNumber' => $userModel->get('telcom_id'),
                'email' => $userModel->get('email1'),
                'name' => $userModel->get('user_name'),
                'sipLogin' => $userModel->get('telcom_id'),
                'sipPassword' => $userModel->get('telcom_password')
            ),
        );

        if (empty($response)) {
            throw new \Exception('No communication with provider');
        }

        $decodedResponse = json_decode($response);
        if ($decodedResponse != null && !empty($decodedResponse->error)) {
            throw new \Exception('Invalid provider parameters');
        }
    }

    protected function sendRequest(string $apiUrl, string $user, string $token, array $data)
    {
        $options = array(
            CURLOPT_URL => $apiUrl,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPHEADER => [
                'Authorization: Basic '.base64_encode($user.":".$token),
                "Accept: application/json",
                "Content-Type: application/json"
            ],
            CURLOPT_POSTFIELDS => json_encode($data)
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