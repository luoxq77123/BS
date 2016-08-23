<?php
APP::import('Controller','AppAudits');
class WorkflowsController extends AppAuditsController{
	public $name = 'Workflows';
	public $uses = array();

	//http://172.28.28.137/workflows/workflowAudit/3899/500/yt/yt
	public function workflowAudit($_workflowid,$_jobid,$_username,$_userpsw){
		$this->autoLayout=false;
		$this->autoRender=false;

		//
		$workflowID = (int)$_workflowid;
		$jobid = (int)$_jobid;

		$userType = $this->getUserType($jobid);

		//内登录
		$loginState = $this->auditLogin($_username, $_userpsw,$userType);
		if ($loginState){
			//获取当前任务ID
			$client = new SoapClient(WS_DOMAIN."/soapServices/wsdl/audit");
			$taskParamArray = array('Request'=>array('Condition'=>array('workflowid'=>$workflowID,'tasktype'=>$userType),'Fields'=>array('taskid')));
			$taskParam = $this->_toXmlStr($taskParamArray);
			$taskList = $client->getTaskWithCondition($taskParam);

			$taskArray = $this->XmlArray->xml2array($taskList);
			$theTaskInfo = $taskArray['Response']['TaskInfo'];

			$curBatchAuditList = array();
			if ($theTaskInfo){
				$curBatchAuditList = $this->_toNumericArray($theTaskInfo['TaskIDs']);
			}

			if ($curBatchAuditList){
				$this->getUserAuditAndLevel();
				$this->lockTask($curBatchAuditList);
				$this->redirect(array('controller' => 'batch_audit_tasks','action' => 'batchAudit','cmode'=>0,'cpage'=>1));
			}
		}
	}
	/**
	 *
	 * 内登录
	 * @param unknown_type $account
	 * @param unknown_type $password
	 * @param unknown_type $userType
	 * @param unknown_type $taskid
	 */
	private function auditLogin($account,$password,$userType) {
		//ws客户端
		$client = new SoapClient(WS_DOMAIN."/soapServices/wsdl/audit");

		//基础数据接口参数定义
		$loginParamArray = array('Request'=>array('Account'=>$account,'Password'=>$password));
		$loginParam = $this->_toXmlStr($loginParamArray);

		//调用登陆接口
		$loginInfos = $client->login($loginParam);

		//分析反馈数据
		$loginArray = $this->_toArray($loginInfos);
		$userData = $loginArray['Response'];

		if ($userData['RespCode'] == USER_SUCCESS){
			//获取用户栏目（是否有进入审核权限）
			$columnParamArray = array('Request'=>array('UserID'=>$userData['UserID']));
			$columnParam = $this->_toXmlStr($columnParamArray);

			$columnInfos = $client->getUserColumns($columnParam);
			$columnArray = $this->_toArray($columnInfos);

			$columnData = $columnArray['Response']['Columns'];
			$columnField = array();

			if ($columnData){
				$columnData = $this->_toNumericArray($columnData);
				foreach ($columnData as $tempData){
					$columnField[$tempData['COLUMN_ID']] = $tempData['COLUMN_NAME'];
				}
			}

			$userParamArray = array('Request'=>array('UserID'=>$userData['UserID']));
			$userParam = $this->_toXmlStr($userParamArray);
			//获取用户审核权限
			$popedomInfos = $client->getUserPopedom($userParam);
			$popedomArray = $this->XmlArray->xml2array($popedomInfos);

			$userContentPopedom = (int)$popedomArray['Response']['ContentPopedom'];
			$userTechPopedom = (int)$popedomArray['Response']['TechPopedom'];
			$userAuditPopedom = (int)$popedomArray['Response']['AuditPopedom'];

			//获取用户的统计权限
			$userAccount = $client->getUserAccountPopedom($userParam);
			$userAccountArray = $this->_toArray($userAccount);

			$userAccountPopedom = $userAccountArray['Response']['Popedom'];

			if (!TASK_AUDIT_TYPE){
				$popedomState = ($userAuditPopedom != NOT_AUDIT_POPEDOM);
			}
			else {
				$popedomState = ($userContentPopedom != NOT_AUDIT_POPEDOM || $userTechPopedom != NOT_AUDIT_POPEDOM);
			}

			if($columnField  && $popedomState){
				//将用户栏目信息写入session
				$this->Session->write('AuditUserColumn',$columnField);

				$this->userID = $userData['UserID'];
				$this->userName = $userData['UserName'];
				$this->userCode = $userData['UserCode'];
				$this->userAcountPopedom = $userAccountPopedom;
				//将用户信息写入cookie
				$this->Cookie->write('AuditUserID',$this->userID);
				$this->Cookie->write('AuditUserName',$this->userName);
				$this->Cookie->write('AuditUserCode',$this->userCode);
				$this->Cookie->write('AuditUserAccountPopedom',$this->userAcountPopedom);

				if (!TASK_AUDIT_TYPE){
					$this->userAuditAuditPopedom = $userAuditPopedom;
					$this->Cookie->write('AuditUserAuditAuditPopedom',$this->userAuditAuditPopedom);

					$this->userType = AUDIT_TASK_TYPE;
					$this->Cookie->write('AuditUserType',$this->userType);
				}
				else {
					//写入技审、内审权限
					$this->userContentAuditPopedom = $userContentPopedom;
					$this->userTechAuditPopedom = $userTechPopedom;
					$this->Cookie->write('AuditUserContentAuditPopedom',$this->userContentAuditPopedom);
					$this->Cookie->write('AuditUserTechAuditPopedom',$this->userTechAuditPopedom);

					$this->userType = $userType;
					$this->Cookie->write('AuditUserType',$this->userType);
				}
				return true;
			}
		}
		return false;
	}
	private function getUserType($jobid){
		$jobtype = floor($jobid/100);
		if ($jobtype==4) {
			return CONTENT_TASK_TYPE;
		}
		elseif ($jobtype==5){
			return TECH_TASK_TYPE;
		}
		elseif ($jobtype==7){
			return AUDIT_TASK_TYPE;
		}
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
			             'AuditLevel'        => $this->userTaskLevel));
			$refreshParam = $this->_toXmlStr($refreshParamArray);
			$result = $this->clientSoap->refreshLockInfo($refreshParam);
		}
	}
}