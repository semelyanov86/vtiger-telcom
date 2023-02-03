<?php

class TelcomFirstLaunchNotification extends AbstractTelcomNotification
{

    protected function getNotificationDataMapping()
    {
        return [];
    }

    protected function prepareNotificationModel()
    {
        return true;
    }

    public function process()
    {
        global $API_VERSION;
        echo json_encode(array(
            'status' => 'OK',
            'version' => $API_VERSION,
        ), JSON_THROW_ON_ERROR);
    }

    public function validateNotification()
    {
        return true;
    }
}