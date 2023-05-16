<?php

require_once 'modules/Telcom/integration/AbstractCallManagerFactory.php';
require_once 'modules/Telcom/apiManagers/TelcomApiManager.php';
require_once 'modules/Telcom/telcom/notifications/TelcomUserNotification.php';
require_once 'modules/Telcom/telcom/notifications/TelcomEventNotification.php';
require_once 'modules/Telcom/telcom/notifications/TelcomHistoryNotification.php';
require_once 'modules/Telcom/telcom/notifications/TelcomFirstLaunchNotification.php';

class TelcomManagerFactory extends AbstractCallManagerFactory {
    /**
     * @return TelcomApiManager
     */
    public function getCallApiManager() {
        return new TelcomApiManager();
    }

    /**
     * @param  array{data: array, method: string}  $request
     * @return AbstractTelcomNotification
     * @throws Exception
     */
    public function getNotificationModel(array $request) {
        if ($request['method'] == 'POST' && isset($request['data']['email'])) {
            throw new DomainException('Unsupported incoming data');
        } elseif ($request['method'] == 'PUT' && isset($request['data']['email'])) {
            return new TelcomUserNotification($request['data']);
        } elseif ($request['method'] == 'GET') {
            return new TelcomFirstLaunchNotification($request['data']);
        } elseif ($request['method'] == 'POST' && isset($request['data']['protocolConfId'])) {
            $request['data']['is_completed'] = false;
            return new TelcomEventNotification($request['data']);
        } elseif ($request['method'] == 'PUT' && isset($request['data']['protocolConfId'])) {
            $request['data']['is_completed'] = true;
            return new TelcomEventNotification($request['data']);
        }
        throw new DomainException('Unsupported incoming data');
    }

    public function syncUser(Users_Record_Model $recordModel)
    {
        $this->getCallApiManager()->syncUser($recordModel);
    }
}