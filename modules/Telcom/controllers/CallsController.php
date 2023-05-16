<?php

register_shutdown_function(function() {
    $error = error_get_last();
    error_log(print_r($error, true));
});

$headers = getallheaders();

chdir(__DIR__. '/../../../');
include_once 'include/Webservices/Relation.php';
include_once 'vtlib/Vtiger/Module.php';
include_once 'includes/main/WebUI.php';
include_once 'libraries/htmlpurifier/library/HTMLPurifier.auto.php';
vimport('includes.http.Request');
require_once 'modules/Telcom/ProvidersEnum.php';
require_once 'modules/Telcom/integration/AbstractCallManagerFactory.php';
require_once 'modules/Telcom/loggers/TelcomLogger.php';
if (file_exists('vendor/autoload.php')) {
    include_once 'vendor/autoload.php';
}

global $current_user;

class CallsController {

    public function process(Vtiger_Request $request) {
        try {
            debugData($request, $request->getAll());
            $voipManagerName = $this->parseRequest($request);
            $factory = AbstractCallManagerFactory::getEventsFacory($voipManagerName);
            $notificationModel = $factory->getNotificationModel($request->getAll());
            $notificationModel->validateNotification();
            http_response_code(202);
            $notificationModel->process();
        } catch (DomainException $exception) {
            TelcomLogger::log('Domain Exception', $exception);
            http_response_code(422);
            echo json_encode([
                'success' => false,
                'error' => [
                    'code' => $exception->getCode(),
                    'message' => $exception->getMessage()
                ]
            ], JSON_THROW_ON_ERROR);
            debugData($exception);
        } catch (\Exception $ex) {
            http_response_code(500);
            TelcomLogger::log('Error on process notification', $ex);
            debugData($ex);
        }
    }

    private function parseRequest(\Vtiger_Request $request) {
        return ProvidersEnum::TELCOM;
    }

}

$current_user = Users::getActiveAdminUser();
$callController = new CallsController();
$request = new Vtiger_Request($_REQUEST);
$request->set('data', json_decode(file_get_contents('php://input'), true));
if (!$request->get('data')) {
    $request->set('data', []);
}
$request->setGlobal('method', $_SERVER['REQUEST_METHOD']);

$callController->process($request);

function debugData(...$data) {
    if (function_exists('ray')) {
        ray(...$data);
    }
}
