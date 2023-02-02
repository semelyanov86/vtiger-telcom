<?php

require_once 'modules/Telcom/integration/AbstractCallManagerFactory.php';
require_once 'modules/Telcom/apiManagers/TelcomApiManager.php';
require_once 'modules/Telcom/telcom/notifications/TelcomContactNotification.php';
require_once 'modules/Telcom/telcom/notifications/TelcomEventNotification.php';
require_once 'modules/Telcom/telcom/notifications/TelcomHistoryNotification.php';

class TelcomManagerFactory extends AbstractCallManagerFactory {
    /**
     * @return TelcomApiManager
     */
    public function getCallApiManager() {
        return new TelcomApiManager();
    }

    public function getNotificationModel($request) {
        $notificationType = $request['cmd'];
        switch($notificationType) {
            
            case 'history':
                return new TelcomHistoryNotification($request);
            
            case 'event':
                return new TelcomEventNotification($request);
            
            case 'contact':
                return new TelcomContactNotification($request);
                
            default:
                throw new \Exception('Unknow type');
        }
    }

    public function syncUser(\Users_Record_Model $recordModel)
    {
        // TODO: Implement syncUser() method.
    }
}