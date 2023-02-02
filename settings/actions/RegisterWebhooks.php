<?php

require_once 'modules/Telcom/integration/AbstractCallManagerFactory.php';

class Settings_Telcom_RegisterWebhooks_Action extends Vtiger_SaveAjax_Action {

    public function process(Vtiger_Request $request) {
        $response = new Vtiger_Response();
        try {
            $provider = $request->get('provider');
            $factory = AbstractCallManagerFactory::getEventsFacory($provider);
            $apiManager = $factory->getCallApiManager();
            $response->setResult(true);
        } catch (Exception $ex) {
            $response->setError($ex->getMessage());
        }                        
        $response->emit();
    }        
}


