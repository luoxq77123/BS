<?php
APP::import('Controller','AppAudits');
App::import('Vendor', 'Audit', array('file' => 'webservices' . DS . 'audit.php'));
class BatchAuditTasksController extends AppAuditsController{
	public $name = 'BatchAuditTasks';
	public $uses = array();
	public function beforeFilter(){
		parent::beforeFilter();
		//soap客户端对象
		$this->clientSoap = new Audit();
	}
	/**
	 * batchAudit
	 * 进行批量审核
	 */
	public function batchAudit($id = NULL){
		$options = array_merge($this->params, $this->params['url'], $this->passedArgs);
		$model = (int)$options['cmode'];
		$page = (int)$options['cpage'];
		//进行级别判断（只查看任务）
		$this->_isAllowAudit($this->userOverLevel, $model,$page);

		//获取锁定的任务
		$theTaskInfos = $this->getLockedTask();
		$listTaskInfo = $workFlowIDList = $taskIDList = array();
		if ($theTaskInfos){
			$listTaskInfo = $this->_toNumericArray($theTaskInfos['TaskInfos']);
			$workFlowIDList = $this->_toNumericArray($theTaskInfos['FlowIDs']);
			$taskIDList = $this->_toNumericArray($theTaskInfos['TaskIDs']);
		}
		$this->set(compact('listTaskInfo'));

		//若有工作流，进行更新
		if(IS_UPDATE_WORK_FLOW){
			$this->updateWorkflowBegin($workFlowIDList);
		}

		//获取布局信息
		$allCount = $this->_getNotAuditListCount();
		$layoutParams = array('layoutMode'=>LAYOUT_MODE_NOT_CTD,'allCount'=>$allCount,'model'=>$model,'page'=>$page,'userType'=>$this->userType);


		//获取为审核中任务的ID
		$taskID = (int)$id;
		if (!$taskID){
			if ($taskIDList){
				$taskID = (int)$taskIDList[0];
			}
		}

		if ($taskID){
			$taskIndex = array_search($taskID, $taskIDList);
			$this->set(compact('taskIndex'));
			$this->set(compact('layoutParams'));

			//更新当前任务状态
			$this->inAuditState($taskID);
			//
			$this->getAuditingTask($taskID);

			if (IS_WORKFLOW_AUDIT){
				$this->render('batch_audit_workflow');
			}
			else {
				$this->render('batch_audit');
			}
		}
		else {
			$layoutParams['layoutMode'] = LAYOUT_MODE_ALL;
			$this->set(compact('layoutParams'));

			$this->render('batch_audit_not_data');
		}
	}

	/**
	 *
	 * 添加任务到工作区
	 */
	public function addWorkArea($id=NULL){
		$this->layout= false;
		//add 1018 判断权限
	        $levelCount = count($this->userOverLevel);
		if ($levelCount > 1 && in_array(0, $this->userOverLevel)){
		    echo json_encode(array('NotLockTag'=>100,'msg'=>'权限不足，不能认领任务'));
		    exit;
		}
		//添加到工作区 ，即为加锁
		$willBatchAuditList = $this->Cookie->read('AuditBatchList');
		if ($id){
			$willBatchAuditList = $id;
		}
		$willBatchAuditList = NumericArray::toNumericArray($willBatchAuditList);

		//进行任务锁定
		$this->lockTask($willBatchAuditList);

		//删除选择列表cookie
		$this->Cookie->delete('AuditBatchList');


		//获取锁定成功列表
		$theTaskInfos = $this->getLockedTask();
		$listTaskInfo = $taskIDList = array();
		if ($theTaskInfos){
			$listTaskInfo = $this->_toNumericArray($theTaskInfos['TaskInfos']);
			$taskIDList = $this->_toNumericArray($theTaskInfos['TaskIDs']);
		}

		//未锁定成功任务获取
		$notLockTask = array();
		if ($willBatchAuditList){
			foreach ($willBatchAuditList as $value) {
				if (!in_array($value, $taskIDList)){
					array_push($notLockTask, $value);
				}
			}
		}

		$noLockArray = array('NotLockTag'=>0);
		if ($notLockTask){
			$noLockArray['NotLockTag']=1;
		}
		echo json_encode($noLockArray);
	}
	/**
	 *
	 * 进行任务的相互切换
	 */
	public function changeTask(){
		$this->layout= false;

		$taskID = (int)$_POST['outauditid'];
		//先对切换前任务进行状态更新
		$this->outAuditState($taskID);
	}
	/**
	 *
	 * 获取当前任务文件信息【码率切换】
	 */
	public function getFileInfo(){
		$this->layout= false;
		$taskID = $_POST['taskid'];
		$filealias = $_POST['filealias'];
		echo $this->getFileList($taskID,$filealias);
	}


	/**
	 * 
	 * 获取当前审核的任务信息
	 * @param unknown_type $taskID
	 */
	private function getAuditingTask($taskID){
		$taskInfo = $this->_getCurTaskInfo($taskID);

		//获取任务对应码率
		$codeRatesParamArray = array('Request'=>array('TaskID'=>$taskID));
		$codeRatesParam = $this->_toXmlStr($codeRatesParamArray);
		$codeRates = $this->clientSoap->getCodeRates($codeRatesParam);

		$fileAlias = array();
		if ($taskID){
			//码率
			$codeRatesArray = $this->XmlArray->xml2array($codeRates);
			$fileAlias = $codeRatesArray['Response']['TheFileAlias'];
			$fileAlias = $this->_toNumericArray($fileAlias);
		}

		$selectFileAlias = '';
		if ($fileAlias){
			$selectFileAlias = $fileAlias[0];
			$defaultFileAlias= Configure::read('DEFAULT_FILE_ALIAS');
			foreach ($defaultFileAlias as $oneDefault) {
				if (in_array($oneDefault, $fileAlias)){
					$selectFileAlias = $oneDefault;
				}
			}
		}

		//获取文件信息
		$taskFile = $this->getFileList($taskID,$selectFileAlias);
		// var_dump($taskFile);
		//
		$this->set(compact('taskInfo'));
		$this->set(compact('selectFileAlias','fileAlias','taskFile'));

		//元数据的获取
		$metaData = $this->getmetaData($taskID);
		extract($metaData);

		$this->set(compact('platFormInfos','attributes'));
	}
	/**
	 * 
	 * 获取文件列表
	 * @param unknown_type $taskID
	 * @param unknown_type $fileAlias
	 */
	private function getFileList($taskID,$fileAlias=NULL){
		$taskFileParamArray = array('Request'=>array('TaskID'=>$taskID,'FileAlias'=>$fileAlias));
		$taskFileParam = $this->_toXmlStr($taskFileParamArray);
		$taskfiles = $this->clientSoap->getTaskFiles($taskFileParam);

		$taskFile = array();
		if ($taskID){
			//文件信息
			$taskFileArray = $this->XmlArray->xml2array($taskfiles);
			$taskFile = $taskFileArray['Response']['TaskFile'];
			$taskFile = $this->_toNumericArray($taskFile);
		}
		return json_encode($taskFile);
	}

	/**
	 *
	 * 锁定任务
	 * @param unknown_type $id
	 */
	private  function lockTask($id=NULL){
		//进行任务锁定
		if ($id){
			$refreshParamArray = array('Request'=>array(
		                 'TaskID'            => $id,                 
		                 'LockerID'          => $this->userID,
		                 'LockState'         => IS_LOCK_STATE,
			             'ContentAuditState' => SELECTED_TASK_STATE,
			             'AuditLevel'        => $this->userTaskLevel,
			             'LockTime'          =>1));
			$refreshParam = $this->_toXmlStr($refreshParamArray);
			$result = $this->clientSoap->refreshLockInfo($refreshParam);
		}
	}

	/**
	 *
	 * 更新状态为审核中
	 * @param unknown_type $id
	 */
	private function inAuditState($id=NULL){
		if ($id){
			$refreshParamArray = array('Request'=>array(
		                 'TaskID'            => $id, 
			             'ContentAuditState' => IS_AUDITING_TASK_STATE,                
		                 'LockerID'          => $this->userID,			             
			             'AuditLevel'        => $this->userTaskLevel));
			$refreshParam = $this->_toXmlStr($refreshParamArray);
			$result = $this->clientSoap->refreshTaskState($refreshParam);
		}
	}
	/**
	 *
	 * 更新状态为已挑选
	 * @param unknown_type $id
	 */
	private function outAuditState($id=NULL){
		if ($id){
			$refreshParamArray = array('Request'=>array(
		                 'TaskID'            => $id, 
			             'ContentAuditState' => SELECTED_TASK_STATE,                
		                 'LockerID'          => $this->userID,			             
			             'AuditLevel'        => $this->userTaskLevel));
			$refreshParam = $this->_toXmlStr($refreshParamArray);
			$result = $this->clientSoap->refreshTaskState($refreshParam);
		}
	}

	/**
	 *
	 * 获取锁定状态任务
	 */
	private function getLockedTask(){
		$paramArray = array(
		     'lockerid'  => $this->userID,		     
		     'lockstate' => IS_LOCK_STATE,
		     'contentauditstate' => array(SELECTED_TASK_STATE,IS_AUDITING_TASK_STATE),
		     'tasktype'  => $this->userType,
		     'auditlevel'=> $this->userTaskLevel);
		$field = array('taskid','pgmname','picpath','workflowid');

		$lockTaskParamArray = array('Request'=>array(
		                               'Condition'=>$paramArray,
		                               'Fields'=>$field));
		$lockTaskParam = $this->_toXmlStr($lockTaskParamArray);
		$lockTaskList = $this->clientSoap->getTaskWithCondition($lockTaskParam);

		$taskArray = $this->XmlArray->xml2array($lockTaskList);
		return  $taskArray['Response']['TaskInfo'];
	}
	private function updateWorkflowBegin($workFlowIDList){
		if (!$workFlowIDList){
			return false;
		}
		$reStepParamArray = array('Request'=>array(
			                          'WorkFlowID'  => $workFlowIDList,
	                                  'TaskType'   => $this->userType,
	                                  'AuditLevel' => $this->userTaskLevel,
			                          'UserName'=> $this->userName,
	                                  'Flag' => 'begin'));
		$reStepParam = $this->_toXmlStr($reStepParamArray);
		return $this->clientSoap->setWorkFlowStep($reStepParam);
	}
	private function getLayoutData($model,$page){
		//用户待审核任务数目获取
		$allCount = $this->_getNotAuditListCount();
		return array('layoutMode'=>LAYOUT_MODE_NOT_CTD,'allCount'=>$allCount,'model'=>$model,'page'=>$page,'userType'=>$this->userType);
	}
}