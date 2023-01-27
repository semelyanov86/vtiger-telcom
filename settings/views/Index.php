<?php

class Settings_Telcom_Index_View extends Settings_Vtiger_Index_View
{

    public function process(Vtiger_Request $request)
    {
        $recordModel = Settings_Telcom_Record_Model::getInstance();
        $moduleModel = Settings_Telcom_Module_Model::getCleanInstance();
        $viewer = $this->getViewer($request);

        $fieldsInfo = $recordModel->getSettingsFieldsInfo();

        $viewer->assign('MODULE_MODEL', $moduleModel);
        $viewer->assign('MODULE', $request->getModule(false));
        $viewer->assign('QUALIFIED_MODULE', $request->getModule(false));
        $viewer->assign('RECORD_MODEL', $recordModel);
        $viewer->assign('FIELDS_INFO', $fieldsInfo);
        $viewer->view('Index.tpl', $request->getModule(false));
    }

}
