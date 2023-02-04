<?php

require_once 'modules/Telcom/integration/AbstractNotification.php';
require_once 'modules/Telcom/ProvidersErrors.php';
require_once 'modules/Telcom/ProvidersEnum.php';


abstract class AbstractTelcomNotification extends AbstractNotification {
    
    const SOURCE_ID_PREFIX = "telcom_";
    
    abstract public function process();
    
    abstract public function validateNotification();
    
    protected function getType() {
        return $this->getDirection();
    }
    
    /**
     * 
     * @return ?Users_Record_Model
     */
    protected function getAssignedUser() {
        $userId = $this->get('user');

        return Vtiger_Record_Model::getInstanceById($userId, 'Users');
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
        if ($this->getDirection() == TelcomEventType::OUTGOING) {
            return $this->getSource();
        }
        return $this->getDestination();
    }
    
    protected function getTelcomUserId() {
        return $this->get('sipLogin');
    }
    
    protected function getCustomerPhoneNumber() {
        if ($this->getDirection() == TelcomEventType::OUTGOING) {
            return $this->getDestination();
        }
        return $this->getSource();
    }
    
    public function getStaffId() {
        return \Settings_Telcom_Record_Model::getTelcomSipStaffId();
    }

    /**
     * @return string
     */
    public function getSourceUUId() {
        return 'telcom_' . $this->get('protocolConfID') . '_' . $this->get('user');
    }

    public function getProtocolId()
    {
        return $this->get('protocolConfID');
    }

    public function getSource()
    {
        return $this->get('source');
    }

    public function getDestination()
    {
        return $this->get('destination');
    }

    public function getDirection()
    {
        return $this->get('direction');
    }
    
    protected function getProviderName() {
        return ProvidersEnum::TELCOM;
    }
}
