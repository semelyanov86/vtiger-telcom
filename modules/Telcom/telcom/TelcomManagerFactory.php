<?php
namespace Telcom\telcom;

use Telcom\integration\AbstractCallManagerFactory;
use Telcom\telcom\notifications\TelcomContactNotification;
use Telcom\telcom\notifications\TelcomEventNotification;
use Telcom\telcom\notifications\TelcomHistoryNotification;
use Telcom\apiManagers\TelcomApiManager;

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