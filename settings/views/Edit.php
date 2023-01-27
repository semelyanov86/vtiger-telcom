<?php

class Settings_Telcom_Edit_View extends Settings_Vtiger_IndexAjax_View {
    
    /**
     * Display Ajax edit view to status setting.
     * @param Vtiger_Request $request
     */
    public function process(Vtiger_Request $request) {
        $recordModel = Settings_Telcom_Record_Model::getInstance();        
        $qualifiedModuleName = $request->getModule(false);
        $moduleModel = Settings_Telcom_Module_Model::getCleanInstance();
              
        $fieldsInfo = $recordModel->getSettingsFieldsInfo();
        $providerFieldObject = $recordModel->getProviderFieldObject();
        $viewer = $this->getViewer($request);
        $viewer->assign('MODULE_MODEL', $moduleModel);
        $viewer->assign('QUALIFIED_MODULE', $qualifiedModuleName);
        $viewer->assign('RECORD_MODEL',$recordModel);
        $viewer->assign('FIELDS_INFO', $fieldsInfo);
        $viewer->assign('PROVIDER', $providerFieldObject);
        $viewer->view('Edit.tpl',$qualifiedModuleName);
    }
}