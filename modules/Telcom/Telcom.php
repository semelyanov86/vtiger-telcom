<?php

include_once('vtlib/Vtiger/Module.php');
include_once 'modules/Telcom/ProvidersEnum.php';

use Telcom\ProvidersEnum;

class Telcom extends CRMEntity
{
    function Telcom()
    {
        global $log;
        $this->column_fields = getColumnFields(get_class($this));
        $this->db = PearDatabase::getInstance();
        $this->log = $log;
    }

    function save_module()
    {

    }

    function vtlib_handler($modulename, $event_type)
    {
        if ($event_type == 'module.postinstall') {
            $this->addResources();
            $this->createFields();
            $this->providerInfoInsertion();
            $this->settingsInsertion();
        } else {
            if ($event_type == 'module.disabled') {
                $this->removeResources();
            } else {
                if ($event_type == 'module.enabled') {
                    $this->addResources();
                } else {
                    if ($event_type == 'module.preuninstall') {
                        $this->removeResources();
                    } else {
                        if ($event_type == 'module.preupdate') {

                        } else {
                            if ($event_type == 'module.postupdate') {

                            }
                        }
                    }
                }
            }
        }
    }

    private function settingsInsertion()
    {
        $db = PearDatabase::getInstance();
        $displayLabel = 'Telcom';

        $fieldid = $db->query_result(
            $db->pquery("SELECT fieldid FROM vtiger_settings_field WHERE name=?", array($displayLabel)), 0, 'fieldid');
        if (!$fieldid) {
            $blockid = $db->query_result(
                $db->pquery("SELECT blockid FROM vtiger_settings_blocks WHERE label='LBL_INTEGRATION'", array()), 0,
                'blockid');
            $sequence = (int) $db->query_result(
                    $db->pquery("SELECT max(sequence) as sequence FROM vtiger_settings_field WHERE blockid=?",
                        array($blockid)), 0, 'sequence') + 1;
            $fieldid = $db->getUniqueId('vtiger_settings_field');
            $db->pquery("INSERT INTO vtiger_settings_field (fieldid, blockid, sequence, name, iconpath, linkto)
                        VALUES (?,?,?,?,?,?)", array(
                $fieldid, $blockid, $sequence, $displayLabel, '',
                'index.php?module=Telcom&parent=Settings&view=Index'
            ));
        }
    }

    private function providerInfoInsertion()
    {
        $db = PearDatabase::getInstance();
        $db->query("INSERT INTO ".Settings_Telcom_Record_Model::settingsTable." VALUES (1, '".ProvidersEnum::TELCOM."', 'telcom_sip_username', 'Username', '')");
        $db->query("INSERT INTO ".Settings_Telcom_Record_Model::settingsTable." VALUES (2, '".ProvidersEnum::TELCOM."', 'telcom_sip_password', 'Password', '')");
        $db->query("INSERT INTO ".Settings_Telcom_Record_Model::settingsTable." VALUES (3, '".ProvidersEnum::TELCOM."', 'telcom_sip_authname', 'Authentication name', '')");
        $db->query("INSERT INTO ".Settings_Telcom_Record_Model::settingsTable." VALUES (4, '".ProvidersEnum::TELCOM."', 'telcom_sip_realm', 'Realm', 'cloud.telcom.pro')");
        $db->query("INSERT INTO ".Settings_Telcom_Record_Model::settingsTable." VALUES (5, '".ProvidersEnum::TELCOM."', 'telcom_sip_display_name', 'Display Name', 'TELCOM')");
        $db->query("INSERT INTO ".Settings_Telcom_Record_Model::settingsTable." VALUES (6, '".ProvidersEnum::TELCOM."', 'telcom_sip_ws_address_domain', 'Web Socket Domain', 'cloud.telcom.pro')");
        $db->query("INSERT INTO ".Settings_Telcom_Record_Model::settingsTable." VALUES (7, '".ProvidersEnum::TELCOM."', 'telcom_sip_ws_address_port', 'Web Socket Port Number', '5064')");
        $db->query("INSERT INTO ".Settings_Telcom_Record_Model::settingsTable." VALUES (8, '".ProvidersEnum::TELCOM."', 'telcom_sip_staff_id', 'Staff', '')");

        $db->pquery("INSERT INTO ".Settings_Telcom_Record_Model::defaultProvideTable." values(?)",
            array(ProvidersEnum::TELCOM));
        $db->pquery("INSERT INTO ".Settings_Telcom_Record_Model::optionsTable." VALUES('use_click_to_call', '0')",
            array());
    }

    private function createFields()
    {
        $moduleInstance = Vtiger_Module_Model::getInstance('PBXManager');
        $blockInstance = Vtiger_Block_Model::getInstance('LBL_PBXMANAGER_INFORMATION', $moduleInstance);

        if (!Vtiger_Field_Model::getInstance('telcom_is_local_cached', $moduleInstance)) {
            $fieldInstance = new Vtiger_Field();
            $fieldInstance->name = 'telcom_is_local_cached';
            $fieldInstance->table = 'vtiger_pbxmanager';
            $fieldInstance->label = 'Is local recorded';
            $fieldInstance->column = 'telcom_is_local_cached';
            $fieldInstance->columntype = 'tinyint';
            $fieldInstance->uitype = 1;
            $fieldInstance->defaultvalue = 0;
            $fieldInstance->displaytype = 3;
            $fieldInstance->typeofdata = 'C~O';
            $blockInstance->addField($fieldInstance);
        }

        if (!Vtiger_Field_Model::getInstance('telcom_recordingurl', $moduleInstance)) {
            $fieldInstance = new Vtiger_Field();
            $fieldInstance->name = 'telcom_recordingurl';
            $fieldInstance->table = 'vtiger_pbxmanager';
            $fieldInstance->label = 'Recording url';
            $fieldInstance->column = 'telcom_recordingurl';
            $fieldInstance->columntype = 'VARCHAR(255)';
            $fieldInstance->uitype = 1;
            $fieldInstance->typeofdata = 'V~O';
            $blockInstance->addField($fieldInstance);
        }

        if (!Vtiger_Field_Model::getInstance('telcom_is_recorded', $moduleInstance)) {
            $fieldInstance = new Vtiger_Field();
            $fieldInstance->name = 'telcom_is_recorded';
            $fieldInstance->table = 'vtiger_pbxmanager';
            $fieldInstance->label = 'Is recorded';
            $fieldInstance->column = 'telcom_is_recorded';
            $fieldInstance->columntype = 'VARCHAR(255)';
            $fieldInstance->uitype = 1;
            $fieldInstance->typeofdata = 'V~O';
            $blockInstance->addField($fieldInstance);
        }

        if (!Vtiger_Field_Model::getInstance('telcom_recorded_call_id', $moduleInstance)) {
            $fieldInstance = new Vtiger_Field();
            $fieldInstance->name = 'telcom_recorded_call_id';
            $fieldInstance->table = 'vtiger_pbxmanager';
            $fieldInstance->label = 'Recorder call id';
            $fieldInstance->column = 'telcom_recorded_call_id';
            $fieldInstance->columntype = 'VARCHAR(255)';
            $fieldInstance->uitype = 1;
            $fieldInstance->typeofdata = 'V~O';
            $blockInstance->addField($fieldInstance);
        }

        if (!Vtiger_Field_Model::getInstance('telcom_voip_provider', $moduleInstance)) {
            $fieldInstance = new Vtiger_Field();
            $fieldInstance->name = 'telcom_voip_provider';
            $fieldInstance->table = 'vtiger_pbxmanager';
            $fieldInstance->label = 'Provider';
            $fieldInstance->column = 'telcom_voip_provider';
            $fieldInstance->columntype = 'VARCHAR(255)';
            $fieldInstance->uitype = 1;
            $fieldInstance->typeofdata = 'V~O';
            $blockInstance->addField($fieldInstance);
        }

        if (!Vtiger_Field_Model::getInstance('telcom_call_status_code', $moduleInstance)) {
            $fieldInstance = new Vtiger_Field();
            $fieldInstance->name = 'telcom_call_status_code';
            $fieldInstance->table = 'vtiger_pbxmanager';
            $fieldInstance->label = 'Status code';
            $fieldInstance->column = 'telcom_call_status_code';
            $fieldInstance->columntype = 'VARCHAR(255)';
            $fieldInstance->uitype = 1;
            $fieldInstance->typeofdata = 'V~O';
            $blockInstance->addField($fieldInstance);
        }

        if (!Vtiger_Field_Model::getInstance('telcom_called_from_number', $moduleInstance)) {
            $fieldInstance = new Vtiger_Field();
            $fieldInstance->name = 'telcom_called_from_number';
            $fieldInstance->table = 'vtiger_pbxmanager';
            $fieldInstance->label = 'From number';
            $fieldInstance->column = 'telcom_called_from_number';
            $fieldInstance->columntype = 'VARCHAR(255)';
            $fieldInstance->uitype = 1;
            $fieldInstance->typeofdata = 'V~O';
            $blockInstance->addField($fieldInstance);
        }

        if (!Vtiger_Field_Model::getInstance('telcom_called_to_number', $moduleInstance)) {
            $fieldInstance = new Vtiger_Field();
            $fieldInstance->name = 'telcom_called_to_number';
            $fieldInstance->table = 'vtiger_pbxmanager';
            $fieldInstance->label = 'To number';
            $fieldInstance->column = 'telcom_called_to_number';
            $fieldInstance->columntype = 'VARCHAR(255)';
            $fieldInstance->uitype = 1;
            $fieldInstance->typeofdata = 'V~O';
            $blockInstance->addField($fieldInstance);
        }

        $usersModuleModel = Vtiger_Module_Model::getInstance("Users");
        if (!Vtiger_Field_Model::getInstance('telcom_id', $usersModuleModel)) {
            $userInfoBlock = Vtiger_Block_Model::getInstance('LBL_MORE_INFORMATION', $usersModuleModel);

            $fieldInstance = new Vtiger_Field();
            $fieldInstance->name = 'telcom_id';
            $fieldInstance->table = 'vtiger_users';
            $fieldInstance->label = 'Telcom Id';
            $fieldInstance->column = 'telcom_id';
            $fieldInstance->columntype = 'VARCHAR(255)';
            $fieldInstance->uitype = 1;
            $fieldInstance->typeofdata = 'V~O';
            $userInfoBlock->addField($fieldInstance);
        }
        if (!Vtiger_Field_Model::getInstance('telcom_password', $usersModuleModel)) {
            $userInfoBlock = Vtiger_Block_Model::getInstance('LBL_MORE_INFORMATION', $usersModuleModel);

            $fieldInstance = new Vtiger_Field();
            $fieldInstance->name = 'telcom_password';
            $fieldInstance->table = 'vtiger_users';
            $fieldInstance->label = 'Telcom Password';
            $fieldInstance->column = 'telcom_password';
            $fieldInstance->columntype = 'VARCHAR(255)';
            $fieldInstance->uitype = 1;
            $fieldInstance->typeofdata = 'V~O';
            $userInfoBlock->addField($fieldInstance);
        }
    }

    private function addResources()
    {
        Vtiger_Link::addLink(0, 'HEADERSCRIPT', 'Telcom', 'modules/Telcom/resources/Telcom.js');
    }

    private function removeResources()
    {
        Vtiger_Link::deleteLink(0, 'HEADERSCRIPT', 'Telcom', 'modules/Telcom/resources/Telcom.js');
    }

}
