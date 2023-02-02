<?php

require_once 'modules/Telcom/ProvidersErrors.php';
require_once 'modules/Telcom/telcom/notifications/AbstractTelcomNotification.php';

class TelcomUserNotification extends AbstractTelcomNotification {
    public function validateNotification() {
        $userId = $this->getId();
        if(empty($userId)) {
            throw new DomainException(ProvidersErrors::VALIDATE_REQUEST_ERROR);
        }

        $email = $this->getEmail();
        if(empty($email)) {
            throw new DomainException(ProvidersErrors::VALIDATE_REQUEST_ERROR);
        }

        $sipLogin = $this->getSipLogin();
        if(empty($sipLogin)) {
            throw new DomainException(ProvidersErrors::VALIDATE_REQUEST_ERROR);
        }

        $sipPassword = $this->getSipPassword();
        if (empty($sipPassword)) {
            throw new DomainException(ProvidersErrors::VALIDATE_REQUEST_ERROR);
        }
    }
    
    public function process() {
        global $current_user;
        $current_user->id = 1;
        $userId = $this->getId();
        $userModel = Users_Record_Model::getInstanceById($userId, 'Users');
        $responseData = array();
        if($userModel != null) {
            $userModel->set('telcom_id', $this->getTelcomUserId());
            $userModel->set('telcom_password', $this->getSipPassword());
            $userModel->set('phone_crm_extension', $this->getPhoneNumber());
            $userModel->set('mode', 'edit');
            $userModel->save();
            $responseData['first_name'] = $userModel->get('first_name');
            $responseData['last_name'] = $userModel->get('last_name');
            $responseData['email'] = $userModel->get('email');
            $responseData['telcom_id'] = $this->getTelcomUserId();
            $responseData['telcom_password'] = $this->getSipPassword();
        }
        
        echo json_encode($responseData);
    }
    
    protected function getNotificationDataMapping() {
        return array();
    }


    protected function prepareNotificationModel() {
        /* No need */
    }
}
