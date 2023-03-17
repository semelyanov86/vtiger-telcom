<?php

class TelcomEventNotification extends AbstractTelcomNotification {
    public const OUTGOING_TYPE = 'outbound';

    public const INCOMING_TYPE = 'inbound';

    private $dataMapping = array(
        'user' => 'user', 
        'callstatus' => 'callstatus',
        'sourceuuid' => 'sourceuuid',
        'telcom_call_status_code' => 'status',
        'telcom_called_from_number' => 'source',
        'telcom_called_to_number' => 'destination',
        'telcom_recordingurl' => 'callRecord',
        'telcom_voip_provider' => 'provider',
        'totalduration' => 'durationSeconds',
        'billduration' => 'billduration',
    );

    public function process() {
        http_response_code(201);
        $voipModel = $this->getVoipRecordModelFromNotificationModel();
        $voipModel->save();
    }

    /**
     * @return string[]
     */
    protected function getNotificationDataMapping() {
        return $this->dataMapping;
    }

    protected function prepareNotificationModel() {
        $this->set('sourceuuid', $this->getSourceUUId());
        $this->set('provider', $this->getProviderName());
        
        $userModel = $this->getAssignedUser();
        if($userModel != null) {
            $this->set('user', $userModel->getId());
        }
        
        $direction = $this->getDirection();
        if($direction != null) {
            $this->dataMapping['direction'] = 'direction';
            $this->set('direction', $direction);
        }
        
        $status = $this->getStatus();
        if($status != null) {
            $this->set('callstatus', $status);            
        }

        $type = $this->getType();
        if ($type === self::INCOMING_TYPE || $type === self::OUTGOING_TYPE) {
            $this->dataMapping['customernumber'] = 'phone';
        }
        
        $this->processDates();
    }

    /**
     * @return string
     */
    private function getStatus() {
        if (!$this->get('is_completed')) {
            return 'ringing';
        }
        if ($this->get('status') != 200) {
            return 'no-response';
        }
        if ($this->checkIfCompleted()) {
            return 'completed';
        }
        return 'no-answer';
    }

    /**
     * @return bool
     */
    private function checkIfCompleted()
    {
        return $this->get('durationSeconds') && $this->get('durationSeconds') > 0;
    }

    /**
     * @return string|null
     */
    public function getDirection() {
        $type = parent::getDirection();
        if($type === TelcomEventType::INCOMING) {
            return self::INCOMING_TYPE;
        }
        
        if($type === TelcomEventType::OUTGOING) {
            return self::OUTGOING_TYPE;
        }
        
        return null;
    }

    /**
     * @return void
     */
    private function processDates() {
        $type = $this->getType();
        $this->dataMapping['totalduration'] = 'durationSeconds';
        $this->set('totalduration', $this->get('durationSeconds'));
        if($type === self::INCOMING_TYPE || $type === self::OUTGOING_TYPE) {
            $this->dataMapping['starttime'] = 'starttime';
            
            $this->set('starttime', date("Y-m-d H:i:s"));
        }

        if($this->checkIfCompleted()) {
            $currentTime = time();
            
            $this->dataMapping['endtime'] = 'endtime';
            $this->set('endtime', date('Y-m-d H:i:s', $currentTime));

            $this->dataMapping['billduration'] = 'billduration';
            $this->set('billduration', $this->get('durationSeconds'));
        }
    }

    private function getTotalDuration() {
        $totalDuration = null;
        if($this->pbxManagerModel != null) {
            $totalDuration = $this->pbxManagerModel->get('totalduration');
        }
        return $totalDuration;
    }
    
    private function getStartTime() {
        $startTime = null;
        if($this->pbxManagerModel != null) {
            $startDateTime = $this->pbxManagerModel->get('starttime');
            if(!empty($startDateTime)) {
                $startTime = strtotime($startDateTime);
            }
        }
        
        return $startTime;
    }

    public function validateNotification()
    {
        $protocolId = $this->getProtocolId();
        if(empty($protocolId)) {
            throw new DomainException(ProvidersErrors::VALIDATE_REQUEST_ERROR);
        }

        $userId = $this->get('user');
        if (empty($userId)) {
            throw new DomainException(ProvidersErrors::VALIDATE_REQUEST_ERROR);
        }

        $destination = $this->getDestination();
        if (empty($destination)) {
            throw new DomainException(ProvidersErrors::VALIDATE_REQUEST_ERROR);
        }

        $direction = $this->getDirection();
        if (empty($direction)) {
            throw new DomainException(ProvidersErrors::VALIDATE_REQUEST_ERROR);
        }
        return true;
    }
}
