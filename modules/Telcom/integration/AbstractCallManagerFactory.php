<?php

require_once 'modules/Telcom/apiManagers/TelcomApiManager.php';
require_once 'modules/Telcom/ProvidersEnum.php';
require_once 'modules/Telcom/telcom/TelcomManagerFactory.php';

abstract class AbstractCallManagerFactory {

    public abstract function getNotificationModel($requestData);

    /**
     * @return TelcomApiManager
     */
    public abstract function getCallApiManager();
    
    /**
     * 
     * @return AbstractCallManagerFactory
     * @throws \Exception
     */
    public static function getDefaultFactory() {
        $defaultProvider = \Settings_Telcom_Record_Model::getDefaultProvider();
        return self::getEventsFacory($defaultProvider);
    }

    /**
     * @param $providerName
     * @return TelcomManagerFactory
     */
    public static function getEventsFacory($providerName) {
        return new TelcomManagerFactory();
    }
}