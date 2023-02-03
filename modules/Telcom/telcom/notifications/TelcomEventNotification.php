<?php

class TelcomEventNotification extends AbstractTelcomNotification {        
    private $dataMapping = array(
        'user' => 'user', 
        'callstatus' => 'callstatus',
        'sourceuuid' => 'sourceuuid',
    );

    public function process() {
        $voipModel = $this->getVoipRecordModelFromNotificationModel();
        $voipModel->save();
    }

    protected function getNotificationDataMapping() {
        return $this->dataMapping;
    }

    protected function prepareNotificationModel() {
        $this->set('sourceuuid', $this->getSourceUUId());
        
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
        if ($type === TelcomEventType::INCOMING || $type === TelcomEventType::OUTGOING) {
            $this->dataMapping['customernumber'] = 'phone';
        }
        
        $this->processDates();
    }
    
    private function getStatus() {
        $type = $this->getType();
        if($type === TelcomEventType::INCOMING || $type === TelcomEventType::OUTGOING) {
            return 'ringing';
        }
        
        if($type === TelcomEventType::ACCEPTED) {
            return 'in-progress';
        }
        
        if($type === TelcomEventType::COMPLETED) {
            return 'completed';
        }
        
        if($type === TelcomEventType::CANCELLED) {
            return 'no-answer';
        }
        
        return null;
    }
    
    private function getDirection() {
        $type = $this->getType();
        if($type === TelcomEventType::INCOMING) {
            return 'inbound';
        }
        
        if($type === TelcomEventType::OUTGOING) {
            return 'outbound';
        }
        
        return null;
    }
    
    
    private function processDates() {
        $type = $this->getType();
        if($type === TelcomEventType::INCOMING || $type === TelcomEventType::OUTGOING) {
            $this->dataMapping['starttime'] = 'starttime';
            $this->dataMapping['sp_voip_provider'] = 'sp_voip_provider';
            
            $this->set('starttime', date("Y-m-d H:i:s"));
            $this->set('sp_voip_provider', $this->getProviderName());
        }
        
        if($type === TelcomEventType::ACCEPTED) {
            $currentTime = time();
            $startTime = $this->getStartTime();
            if($startTime && ($currentTime - $startTime) > 0) {
                $this->dataMapping['totalduration'] = 'totalduration';
                $this->set('totalduration', $currentTime - $startTime);
            }
        }
        
        if($type === TelcomEventType::COMPLETED || $type === TelcomEventType::CANCELLED) {
            $currentTime = time();
            
            $this->dataMapping['endtime'] = 'endtime';
            $this->set('endtime', date('Y-m-d H:i:s', $currentTime));
            
            $startTime = $this->getStartTime();
            $oldTotalDuration = $this->getTotalDuration();                        
            if($startTime && ($currentTime - $startTime) > 0) {
                $newTotalDuration = $currentTime - $startTime;
                $this->dataMapping['totalduration'] = 'totalduration';
                $this->set('totalduration', $newTotalDuration);
                if ($oldTotalDuration !== null && $type === TelcomEventType::COMPLETED) {
                    $billDuration = $newTotalDuration - $oldTotalDuration;
                    if ($billDuration > 0) {                    
                        $this->dataMapping['billduration'] = 'billduration';
                        $this->set('billduration', $billDuration);
                    }
                }
            }
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
        // TODO: Implement validateNotification() method.
    }
}
