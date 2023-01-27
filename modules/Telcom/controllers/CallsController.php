<?php

namespace Telcom;

register_shutdown_function(function() {
    $error = error_get_last();
    error_log(print_r($error, true));
});
if (isset($_GET['zd_echo']))
    exit($_GET['zd_echo']);
$headers = getallheaders();
if (isset($headers['Echo'])) {
    header("Echo: {$headers['Echo']}");
    exit();
}
chdir(dirname(__FILE__) . '/../../../');
include_once 'include/Webservices/Relation.php';
include_once 'vtlib/Vtiger/Module.php';
include_once 'includes/main/WebUI.php';
include_once 'libraries/htmlpurifier/library/HTMLPurifier.auto.php';
vimport('includes.http.Request');
include_once 'modules/Telcom/vendor/autoload.php';

global $current_user;

use Telcom\ProvidersEnum;
use Telcom\integration\AbstractCallManagerFactory;
use Telcom\loggers\Logger;

class CallsController {

    public function process(\Vtiger_Request $request) {
        try {
            $voipManagerName = $this->parseRequest($request);
            $factory = AbstractCallManagerFactory::getEventsFacory($voipManagerName);
            $notificationModel = $factory->getNotificationModel($request->getAll());
            $notificationModel->validateNotification();
            $notificationModel->process();
        } catch (\Exception $ex) {
            Logger::log('Error on process notification', $ex);
        }
    }

    private function parseRequest(\Vtiger_Request $request) {
        return ProvidersEnum::TELCOM;
    }

}

$current_user = \Users::getActiveAdminUser();

$callController = new CallsController();
$callController->process(new \Vtiger_Request($_REQUEST));
