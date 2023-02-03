<?php

require_once 'modules/Telcom/integration/AbstractNotification.php';
require_once 'modules/Telcom/ProvidersErrors.php';
require_once 'modules/Telcom/ProvidersEnum.php';


abstract class AbstractTelcomNotification extends AbstractNotification {
    
    const SOURCE_ID_PREFIX = "telcom_";
    
    abstract public function process();
    
    abstract public function validateNotification();
    
    protected function getType() {
        return $this->get('type');
    }
    
    /**
     * 
     * @return ?Users_Record_Model
     */
    protected function getAssignedUser() {
        $db = \PearDatabase::getInstance();
        $result = $db->pquery("SELECT id FROM vtiger_users WHERE telcom_id=?", array(
            $this->getSipLogin()
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
