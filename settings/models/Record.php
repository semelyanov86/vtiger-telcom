<?php

class Settings_Telcom_Record_Model extends Settings_Vtiger_Record_Model {

    const settingsTable = 'vtiger_telcom_settings';
    const defaultProvideTable = 'vtiger_telcom_provider';
    const optionsTable = 'vtiger_telcom_options';
    
    const USE_CLICK_TO_CALL_FIELD = 'use_click_to_call';
    
    public function getId() {
        return $this->get('id');
    }

    public function getName() {
        return $this->get('id');
    }
    
    public static function isOutgoingCallsEnabled() {
        Users_Privileges_Model::getCurrentUserPrivilegesModel();
        $permission = Users_Privileges_Model::isPermitted('PBXManager', 'MakeOutgoingCalls');
        
        return $permission && self::isClickToCallEnabled();
    }
    
    public static function isClickToCallEnabled() {
        $db = PearDatabase::getInstance();
        $result = $db->pquery("SELECT value FROM " . self::optionsTable . " WHERE name=?", array(
            self::USE_CLICK_TO_CALL_FIELD
        ));
        
        if($result) {
            $resultRow = $db->fetchByAssoc($result);
            return ($resultRow != null && $resultRow['value'] == 1);
        }
        
        return false;
    }
    
    public static function getInstance() {
        $db = PearDatabase::getInstance();

        $query = 'SELECT * FROM ' . self::settingsTable;

        $instance = new Settings_Telcom_Record_Model();

        $result = $db->query($query);
        $fieldsInfo = array();
        if ($result) {
            while ($resRow = $db->fetchByAssoc($result)) {
                $fieldsInfo[$resRow['provider_name']][] = array(
                    'field_name' => $resRow['field_name'],
                    'field_label' => $resRow['field_label'],
                    'field_value' => $resRow['field_value']
                );
            }
        }
        $instance->set('fields_info', $fieldsInfo);

        $defaultProvider = self::getDefaultProvider();
        $instance->set('default_provider', $defaultProvider);
        return $instance;
    }

    public function getSettingsFieldsInfo() {
        return $this->get('fields_info');
    }

    public static function getProviders() {
        $db = PearDatabase::getInstance();
        $providers = array();
        $result = $db->pquery("SELECT DISTINCT provider_name FROM " . self::settingsTable);
        if ($result) {
            while ($resRow = $db->fetchByAssoc($result)) {
                $providers[] = $resRow['provider_name'];
            }
        }

        return $providers;
    }

    public function saveSettings($request) {
        $db = PearDatabase::getInstance();
        $fieldsInfo = $this->get('fields_info');

        foreach ($fieldsInfo as $providerName => $providerFields) {
            foreach ($providerFields as $fieldInfo) {
                $db->pquery("UPDATE " . self::settingsTable . " SET field_value=? WHERE field_name=?", array(trim($request->get($fieldInfo['field_name'])), $fieldInfo['field_name']));
            }
        }
        $defaultProvider = self::getDefaultProvider();
        if (empty($defaultProvider)) {
            $db->pquery("INSERT INTO " . self::defaultProvideTable . " values(?)", array($request->get('default_provider')));
        } else {
            $db->pquery("UPDATE " . self::defaultProvideTable . " SET default_provider=?", array($request->get('default_provider')));
        }
        
        $isClickToCall = ($request->get('use_click_to_call') == "on" || $request->get('use_click_to_call') == 1);
        $db->pquery("UPDATE " . self::optionsTable . " SET value=? WHERE name=?", array(
            ($isClickToCall ? 1 : 0), self::USE_CLICK_TO_CALL_FIELD
        ));
    }

    public function getProviderFieldObject() {
        $provider = null;
        $provider['existing_providers'] = self::getProviders();
        $provider['default_provider'] = $this->get('default_provider');
        return $provider;
    }

    public static function getTelcomSipUsername() {
        return self::getVoipSettingsFieldValue('telcom_sip_username');
    }

    public static function getTelcomSipPassword() {
        return self::getVoipSettingsFieldValue('telcom_sip_password');
    }

    public static function getTelcomSipAuthname() {
        return self::getVoipSettingsFieldValue('telcom_sip_authname');
    }

    public static function getTelcomSipRealm() {
        return self::getVoipSettingsFieldValue('telcom_sip_realm');
    }

    public static function getTelcomSipDisplayName() {
        return self::getVoipSettingsFieldValue('telcom_sip_display_name');
    }

    public static function getTelcomSipWsAddressDomain() {
        return self::getVoipSettingsFieldValue('telcom_sip_ws_address_domain');
    }

    public static function getTelcomSipWsAddressPort() {
        return self::getVoipSettingsFieldValue('telcom_sip_ws_address_port');
    }

    public static function getTelcomSipStaffId() {
        return self::getVoipSettingsFieldValue('telcom_sip_staff_id');
    }

    public static function getUserOutgoing() {
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $outgoingNumber = null;
        if ($currentUser) {
            $outgoingNumber = $currentUser->get('telcom_outgoing_number');
        }
        if (empty($outgoingNumber)) {
            $outgoingNumber = self::getYandexDefaultOutgoing();
        }
        return $outgoingNumber;
    }

    public static function getDefaultProvider() {
        $db = PearDatabase::getInstance();
        $defaultProvider = null;
        $result = $db->query("SELECT default_provider FROM " . self::defaultProvideTable);
        if ($result && $resRow = $db->fetchByAssoc($result)) {
            $defaultProvider = $resRow['default_provider'];
        }

        if (empty($defaultProvider)) {
            $providers = self::getProviders();
            $defaultProvider = $providers[0];
        }

        return $defaultProvider;
    }

    private static function getVoipSettingsFieldValue($fieldName) {
        $db = PearDatabase::getInstance();
        $fieldValue = null;
        $result = $db->pquery("SELECT field_value FROM " . self::settingsTable . " WHERE field_name=?", array($fieldName));
        if ($result && $resRow = $db->fetchByAssoc($result)) {
            $fieldValue = $resRow['field_value'];
        }
        return $fieldValue;
    }
}
