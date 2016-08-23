<?php
App::import('Vendor', 'AuditWorkFlowOperation', array('file' => 'webservices' . DS . 'AuditInterface'. DS . 'TaskInterface' . DS . 'audit_work_flow_operation.php'));
App::import('Model', 'Content');

App::import('Vendor', 'NumericArray', array('file' => 'someoperation' . DS . 'class.numeric_array.php'));
App::import('Vendor', 'ChangeEncode', array('file' => 'someoperation' . DS . 'class.change_encode.php'));
class AuditTaskSpecialOperation extends Object{
	public $content;

	public function __construct(){
		$this->content = new Content();
	}

	public function refreshTaskStateWithInfo($theParams){
		$theTaskID = $theParams['TaskID'];
		$contentState = (int)$theParams['ContentAuditState'];
		$lockerID = (int)$theParams['LockerID'];
		$theLevel = $theParams['AuditLevel'];

		$taskIDArray = NumericArray::toNumericArray($theTaskID);
		if ($taskIDArray){
			//需要更新的相关数据
			//添加taskid
			$idStr = $this->_getTaskIDString($taskIDArray);

			//添加level
			$auditLevel = NumericArray::toNumericArray($theLevel);
			$levelStr = $this->_getTaskLevelString($auditLevel);

			$sqlStr = "update et_nmpgmauditlist t
                         set t.contentauditstate=".$contentState."  
                         where ".$idStr." and
                               ".$levelStr." and 
                               t.lockerid=".$lockerID." and
                               t.lockstate=".IS_LOCK_STATE;
			$refreshState = $this->content->newSetData(ChangeEncode::changeEncodeFromUTF8($sqlStr));
		}
		else {
			$refreshState = false;
		}

		if (!$refreshState){
			$this->log('任务状态更新：更新任务状态信息失败');
		}
		return $refreshState;
	}
	public function refreshLockInfoWithInfo($theParams){
		$theTaskID = $theParams['TaskID'];
		$lockerID = (int)$theParams['LockerID'];
		$lockState = (int)$theParams['LockState'];
		$contentState = (int)$theParams['ContentAuditState'];
		$theLevel = $theParams['AuditLevel'];

		$taskIDArray = NumericArray::toNumericArray($theTaskID);
		if ($taskIDArray){
			//需要更新的相关数据
			//添加taskid
			$idStr = $this->_getTaskIDString($taskIDArray);

			//添加level
			$auditLevel = NumericArray::toNumericArray($theLevel);
			$levelStr = $this->_getTaskLevelString($auditLevel);

			$timeStr = "";
			if (isset($theParams['LockTime']) && $theParams['LockTime']){
				$theTime = time();
				$timeStr = ",t.locktime='".$theTime."' ";
			}

			$sqlStr = "update et_nmpgmauditlist t
                         set t.lockerid=".$lockerID.",t.lockstate=".$lockState.",t.contentauditstate=".$contentState.$timeStr."  
                         where ".$idStr." and
                               ".$levelStr;	
			//加锁或解锁
			if ($lockState==IS_LOCK_STATE){
				$sqlStr = $sqlStr." and
                               (t.lockerid=".$lockerID." or
                                t.lockstate<>".IS_LOCK_STATE." or
                                t.lockstate is null
                               )";
			}
			elseif ($lockState==NOT_LOCK_STATE){
				$userID = (int)$theParams['UserID'];
				$sqlStr = $sqlStr." and
                               t.lockerid=".$userID." and
                               t.lockstate=".IS_LOCK_STATE;	
			}
			$refreshState = $this->content->newSetData(ChangeEncode::changeEncodeFromUTF8($sqlStr));
		}
		else {
			$refreshState = false;
		}

		if (!$refreshState){
			$this->log('任务锁定：更新任务锁定信息失败');
		}
		return $refreshState;
	}
	public function setWorkFlowStepWithInfo($theParams){
		$theWorkFlowID = $theParams['WorkFlowID'];
		$taskType = (int)$theParams['TaskType'];
		$auditLevel = (int)$theParams['AuditLevel'];
		$userName = $theParams['UserName'];
		$flag = $theParams['Flag'];

		//调用接口
		$workFlowOperation = new AuditWorkFlowOperation($theWorkFlowID, $flag, $taskType, $auditLevel, $userName);
		return  $workFlowOperation->setWorkFlow();
	}
	private function _getTaskIDString($taskIDArray){
		$idStr = "";
		if ($taskIDArray){
			$idStr = "t.taskid in(";

			$num = 0;
			foreach ($taskIDArray as $oneID) {
				if ($num==0){
					$idStr = $idStr.$oneID;
				}
				else {
					$idStr = $idStr.",".$oneID;
				}
				$num++;
			}
			$idStr = $idStr.") ";
		}
		return  $idStr;
	}
	private function _getTaskLevelString($auditLevel){
		$levelStr = "";
		if ($auditLevel){
			$levelStr = "t.auditlevel in(";

			$num = 0;
			foreach ($auditLevel as $oneLevel) {
				if ($num==0){
					$levelStr = $levelStr.$oneLevel;
				}
				else {
					$levelStr = $levelStr.",".$oneLevel;
				}
				$num++;
			}
			$levelStr = $levelStr.")";
		}
		return $levelStr;
	}
}