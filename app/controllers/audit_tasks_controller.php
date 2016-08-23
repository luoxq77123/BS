<?php
APP::import('Controller','AppAudits');
class AuditTasksController extends AppAuditsController {
	public $name = 'AuditTasks';
	public $uses = array();

	/**
	 * 审核模式
	 * Enter description here ...
	 * @param int $id
	 */
	public function auditData($id = NULL){
		//当前任务ID
		$taskID = (int)$id;

		$options = array_merge($this->params, $this->params['url'], $this->passedArgs);
		$model = (int)$options['cmode'];
		$page = (int)$options['cpage'];
		//进行级别判断（只查看任务）
		$this->_isAllowAudit($this->userOverLevel, $model,$page);



		//获取为审核中任务的ID
		$taskID = (int)$id;

		//若有工作流，进行更新
		if(IS_UPDATE_WORK_FLOW){
			$this->updateWorkflow($workFlowIDList);
		}

		//获取布局信息
		$allCount = $this->_getNotAuditListCount();
		$layoutParams = array('layoutMode'=>LAYOUT_MODE_NOT_CTD,'allCount'=>$allCount,'model'=>$model,'page'=>$page,'userType'=>$this->userType);
		$this->set(compact('layoutParams'));

		//更新当前任务状态
		$this->inAuditState($taskID);
		//
		$this->getAuditingTask($taskID);




		//判断锁定状态以及更新数据
		$this->beforeAudit($taskID,$model,$page);

		$this->intoAuditData($taskID,$model,$page);
	}

	/**
	 * 浏览模式
	 * Enter description here ...
	 * @param int $id
	 */
	public function browseData($id = NULL){
		//当前任务ID
		$taskID = (int)$id;

		$options = array_merge($this->params, $this->params['url'], $this->passedArgs);
		$model = (int)$options['cmode'];
		$page = (int)$options['cpage'];

		$this->intoAuditData($taskID,$model,$page);

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
	 * 审核或浏览模式数据
	 * Enter description here ...
	 * @param int $taskID
	 * @param int $model
	 * @param int $page
	 */
	private function intoAuditData($taskID,$model,$page){
		//用户待审核任务数目获取
		$allCount = $this->_getNotAuditListCount();

		//获取当前任务信息
		$taskInfo = $this->_getCurTaskInfo($taskID);

		//页面基础数据接口参数定义
		$codeRatesParamArray = array('Request'=>array('TaskID'=>$taskID));
		$codeRatesParam = $this->_toXmlStr($codeRatesParamArray);

		$taskFileParamArray = array('Request'=>array('TaskID'=>$taskID));
		$taskFileParam = $this->_toXmlStr($taskFileParamArray);

		//调用接口
		$codeRates = $this->clientSoap->getCodeRates($codeRatesParam);
		$taskfiles = $this->clientSoap->getTaskFiles($taskFileParam);


		$fileAlias = array();
		$taskFile = array();

		if ($taskID){
			$codeRatesArray = $this->XmlArray->xml2array($codeRates);
			$fileAlias = $codeRatesArray['Response']['TheFileAlias'];
			$fileAlias = $this->_toNumericArray($fileAlias);

			//文件信息
			$taskFileArray = $this->XmlArray->xml2array($taskfiles);
			$taskFile = $taskFileArray['Response']['TaskFile'];
			$taskFile = $this->_toNumericArray($taskFile);
		}

		//元数据的获取
		$this->metaData($taskID,$page);

		//页面传值
		$layoutParams = array('layoutMode'=>LAYOUT_MODE_NOT_CTD,'allCount'=>$allCount,'model'=>$model,'page'=>$page,'userType'=>$this->userType);
		$this->set(compact('layoutParams'));
		$this->set(compact('taskInfo','fileAlias','taskFile'));
	}

	/**
	 * beforeAudit
	 * 进入审核前一系列操作
	 * @param int $id
	 */
	private  function beforeAudit($id = null,$tmode =0,$tpage=1){
		$taskID = (int)$id;
		//确认任务列表页类型
		if ($tmode==THUMB_MODEL){
			$contentAction = 'auditList';
		}
		else {
			$contentAction = 'detailList';
		}

		//设置锁定
		$refreshParamArray = array('Request'=>array(
		                                  'TaskID'            => $taskID,
		                                  'ContentAuditState' => IS_AUDITING_TASK_STATE,
		                                  'LockerID'          => $this->userID,
		                                  'LockState'         => IS_LOCK_STATE,
		                                  'AuditLevel'        => $this->userTaskLevel));
		$refreshParam = $this->_toXmlStr($refreshParamArray);
		$result = $this->clientSoap->refreshLockInfo($refreshParam);
		//获取锁定列表
		$paramArray = array(
		           'taskid'=>$taskID,
		           'tasktype'=> $this->userType,
		           'lockerid'=> $this->userID,
		           'lockstate'=> IS_LOCK_STATE,
		           'auditlevel'=>$this->userTaskLevel);
		$field = array('taskid','pgmname','picpath','workflowid');
		$lockTaskParamArray = array('Request'=>array('Condition'=>$paramArray,'Fields'=>$field));
		$lockTaskParam = $this->_toXmlStr($lockTaskParamArray);
		$lockTaskList = $this->clientSoap->getTaskWithCondition($lockTaskParam);

		$taskArray = $this->XmlArray->xml2array($lockTaskList);
		$theTaskInfo = $taskArray['Response']['TaskInfo'];

		$curBatchAuditList = $workFlowIDList = array();
		if ($theTaskInfo){
			$curBatchAuditList = $this->_toNumericArray($theTaskInfo['TaskIDs']);
			$workFlowIDList = $this->_toNumericArray($theTaskInfo['FlowIDs']);
		}

		//进行判断
		if (!$curBatchAuditList){
			$this->Session->setFlash('您好，该任务已审核或正在审核，无法进入审核！',false);
			$this->redirect(array('controller' => 'contents','action' => $contentAction,'page'=>$tpage));
		}


		//进入时更新流程步骤
		if(IS_UPDATE_WORK_FLOW){
			$reStepParamArray = array('Request'=>array(
			                               'WorkFlowID'  => $workFlowIDList,
	                                       'TaskType'   => $this->userType,
	                                       'AuditLevel' => $this->userTaskLevel,
			                               'UserName'=> $this->userName,
	                                       'Flag' => 'begin'));
			$reStepParam = $this->_toXmlStr($reStepParamArray);
			$setStepState = $this->clientSoap->setWorkFlowStep($reStepParam);
		}
	}
}