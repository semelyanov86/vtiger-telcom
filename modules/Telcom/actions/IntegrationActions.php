<?php
include_once 'modules/Telcom/vendor/autoload.php';
require_once 'modules/Telcom/integration/AbstractCallManagerFactory.php';

class Telcom_IntegrationActions_Action extends Vtiger_Action_Controller {    
    
    function __construct() {
		parent::__construct();
		$this->exposeMethod('startOutgoingCall');
        $this->exposeMethod('getOutgoingPermissions');
        $this->exposeMethod("checkClickToCall");
        $this->exposeMethod("syncUserWithProvider");
	}
    
    function checkPermission(Vtiger_Request $request) {
		return;
	}
    
    public function checkClickToCall(Vtiger_Request $request) {
        $response = new Vtiger_Response();
        $response->setResult(array(
            'enabled' => Settings_Telcom_Record_Model::isClickToCallEnabled()
        ));
        $response->emit();
    }
    
    public function startOutgoingCall(Vtiger_Request $request) {
        $response = new Vtiger_Response();
        try {
            $factory = AbstractCallManagerFactory::getDefaultFactory();
            $callApiManager = $factory->getCallApiManager();        
            $callApiManager->doOutgoingCall($request->get('number'));
            $response->setResult(array('success'=>true));
        } catch (Exception $ex) {
            $response->setError($ex->getMessage());
        }
        $response->emit();
        
    }
    
    public function getOutgoingPermissions(Vtiger_Request $request) {
        $response = new Vtiger_Response();
        Users_Privileges_Model::getCurrentUserPrivilegesModel();
        $permission = Users_Privileges_Model::isPermitted('PBXManager', 'MakeOutgoingCalls');

        $response->setResult(array('success' => true, 'permission' => $permission));
        $response->emit();
    }

    public function syncUserWithProvider(\Vtiger_Request $request)
    {
        $recordId = $request->get('record');
        $userModel = Users_Record_Model::getInstanceById($recordId, 'Users');
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        if (!$userModel->get('telcom_id') || !$userModel->get('telcom_password')) {
            $response->setError(422, vtranslate('LBL_USER_VALIDATION_MESSAGE', 'Telcom'), vtranslate('LBL_USER_VALIDATION_TITLE', 'Telcom'));
        } else {
            try {
                $factory = AbstractCallManagerFactory::getDefaultFactory();
                $factory->syncUser($userModel);
                $response->setResult(array('success' => true, 'message' => vtranslate('LBL_USER_SYNCED', 'Telcom')));
            } catch (Exception $ex) {
                $response->setError($ex->getMessage());
            }

        }
        $response->emit();
    }

    public function process(\Vtiger_Request $request) {
        $mode = $request->getMode();
        try {
            if(!empty($mode)) {
                $this->invokeExposedMethod($mode, $request);
                return;
            }
        } catch (Exception $ex) {
            $response = new Vtiger_Response();
            $response->setError(vtranslate($ex->getMessage(), $request->getModule()));
            $response->emit();
            return;
        }
        
    }

}