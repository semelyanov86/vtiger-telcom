<?php

require_once 'modules/Telcom/integration/AbstractNotification.php';
require_once 'modules/Telcom/ProvidersErrors.php';
require_once 'modules/Telcom/ProvidersEnum.php';


abstract class AbstractTelcomNotification extends AbstractNotification {
    
    const SOURCE_ID_PREFIX = "telcom_";
    
    public function process() {
        $voipModel = $this->getVoipRecordModelFromNotificationModel();
        $voipModel->save();
    }
    
    public function validateNotification() {
        $requestToken = $this->getRequestToken();
        if(empty($requestToken)) {
            throw new \Exception(ProvidersErrors::VALIDATE_REQUEST_ERROR);
        }
        
        $crmToken = $this->getCrmSavedToken();
        if(empty($crmToken)) {
            throw new \Exception(ProvidersErrors::VALIDATE_REQUEST_ERROR);
        }
        
        if($requestToken != $crmToken) {
            throw new \Exception(ProvidersErrors::VALIDATE_REQUEST_ERROR);
        }
        
        $callId = $this->get('callid');
        if(empty($callId)) {
            throw new \Exception("Invalid data", ProvidersErrors::WRONG_PROVIDER_DATA);
        }
        
        $phone = $this->getCustomerPhoneNumber('phone');
        if(empty($phone)) {
            throw new \Exception("Invalid data", ProvidersErrors::WRONG_PROVIDER_DATA);
        }
        
        $gravitelUserId = $this->getTelcomUserId();
        if(empty($gravitelUserId)) {
            throw new \Exception("Invalid data", ProvidersErrors::WRONG_PROVIDER_DATA);
        }
        
        $userModel = $this->getAssignedUser();
        if (empty($userModel)) {
            throw new \Exception("Unknown user", ProvidersErrors::WRONG_PROVIDER_DATA);
        }
    }
    
    protected function getType() {
        return $this->get('type');
    }
    
    /**
     * 
     * @return Users_Record_Model
     */
    protected function getAssignedUser() {
        $db = \PearDatabase::getInstance();
        $result = $db->pquery("SELECT id FROM vtiger_users WHERE sp_domru_id=?", array(
            $this->getGravitelUserId()
        ));
        
        if($result && $resultRow = $db->fetchByAssoc($result)) {
            return \Vtiger_Record_Model::getInstanceById($resultRow['id'], 'Users');
        }
        
        return null;
    }

    /**
     * @return string
     */
    protected function getId()
    {
        return $this->get('id');
    }

    /**
     * @return string
     */
    protected function getPhoneNumber()
    {
        return $this->get('phoneNumber');
    }

    /**
     * @return string
     */
    protected function getEmail()
    {
        return $this->get('email');
    }

    /**
     * @return string
     */
    protected function getName()
    {
        return $this->get('name');
    }

    /**
     * @return string
     */
    protected function getSipLogin()
    {
        return $this->get('sipLogin');
    }

    /**
     * @return string
     */
    protected function getSipPassword()
    {
        return $this->get('sipPassword');
    }
    
    protected function getUserPhoneNumber() {
        return $this->get('ext');
    }
    
    protected function getTelcomUserId() {
        return $this->get('sipLogin');
    }
    
    protected function getCustomerPhoneNumber() {
        return $this->get('phone');
    }
    
    protected function getRequestToken() {
        return $this->get('crm_token');
    }
    
    public function getStaffId() {
        return \Settings_Telcom_Record_Model::getTelcomSipStaffId();
    }
    
    public function getSourceUUId() {
        return 'telcom_' . $this->get('callid') . '_' . $this->get('user');
    }
    
    protected function getProviderName() {
        return ProvidersEnum::TELCOM;
    }
}
