<?php
namespace Telcom\integration;

use Telcom\ProvidersEnum;
use Telcom\telcom\TelcomFactory;

abstract class AbstractCallManagerFactory {

    public abstract function getNotificationModel($requestData);
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
    
    public static function getEventsFacory($providerName) {
        return new TelcomFactory();
    }
}