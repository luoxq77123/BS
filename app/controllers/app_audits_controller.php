<?php
class AppAuditsController extends AppController{
	public $name='AppAudits';
	public $uses = array();

	/**
	 * 一系列值
	 */
	public $userAuditPopedom;
	public $userOverLevel=array();
	public $userTaskLevel=array();

	/**
	 * $clientSoap
	 * soap客户端对象
	 * @var object
	 */
	public $clientSoap;

	/**
	 * beforeFilter
	 */
	public function beforeFilter(){
		parent::beforeFilter();

		//判断权限
		$this->_isAllow($this->userID);

		$this->getUserAuditAndLevel();

		//soap客户端对象
		$this->clientSoap = new SoapClient(WS_DOMAIN."/soapServices/wsdl/audit");
	}
	public function getUserAuditAndLevel(){
		//获取当前用户审核权限
		if (!TASK_AUDIT_TYPE){
			$this->userAuditPopedom = (int)$this->userAuditAuditPopedom;
		}
		else {
			if ($this->userType == CONTENT_TASK_TYPE){
				$this->userAuditPopedom = (int)$this->userContentAuditPopedom;
			}
			else {
				$this->userAuditPopedom = (int)$this->userTechAuditPopedom;
			}
		}

		//获取用户审核级别
		$userOverTaskLevel = $this->_toPopedomArray($this->userAuditPopedom);
		$userTaskLevelArray = array();
		if (!TASK_AUDIT_TYPE){
			foreach ($userOverTaskLevel as $value) {
				$userTaskLevelArray[]=$value-1;
			}
		}
		else {
			if (TASK_AUDIT_MODE == 1 && $this->userType == CONTENT_TASK_TYPE){
				//进行级别处理
				foreach ($userOverTaskLevel as $value) {
					$userTaskLevelArray[]=$value-1+MAX_TASK_AUDIT_LEVEL;
				}
			}
			elseif (TASK_AUDIT_MODE == 2 && $this->userType == TECH_TASK_TYPE){
				//进行级别处理
				foreach ($userOverTaskLevel as $value) {
					$userTaskLevelArray[]=$value-1+MAX_TASK_AUDIT_LEVEL;
				}
			}
			else {
				foreach ($userOverTaskLevel as $value) {
					$userTaskLevelArray[]=$value-1;
				}
			}
		}
		$this->userOverLevel = $userOverTaskLevel;
		$this->userTaskLevel=$userTaskLevelArray;
	}
	public function _getCurTaskInfo($taskID,$fields=NUll){
		//获取当前任务信息
		$taskParamArray = array('Request'=>array('TaskID'=>$taskID,'Fields'=>$fields));
		$taskParam = $this->_toXmlStr($taskParamArray);

		$taskInfos = $this->clientSoap->getTask($taskParam);
		$taskInfo  = array();
		if ($taskID){
			$taskArray = $this->XmlArray->xml2array($taskInfos);
			$taskInfo = $taskArray['Response']['TaskInfo'];
		}
		return $taskInfo;
	}
	public function _isAllowAudit($level,$tmode,$tpage){
		//确认任务列表页类型
		if ($tmode==THUMB_MODEL){
			$contentAction = 'auditList';
		}
		else {
			$contentAction = 'detailList';
		}

		$levelCount = count($level);
		if ($levelCount > 1 && in_array(0, $level)){
			$this->Session->setFlash('对不起，您没有任务的审核权限！',false);
			$this->redirect(array('controller' => 'contents','action' => $contentAction,'page'=>$tpage));
		}
	}

	/**
	 *
	 * 元数据ajax获取
	 * @param unknown_type $taskID
	 */
	public function metaData($taskID){
		$this->layout=false;
		$metaData = $this->getmetaData($taskID);
		extract($metaData);

		$this->set(compact('platFormInfos','attributes'));
		$this->set(compact('taskID'));
	}
	public  function getmetaData($taskID){
		$metadata = array('platFormInfos'=>array(),'attributes'=>array());
		if (!TASK_AUDIT_TYPE){
			$metadata = $this->_metaDataOperation($taskID);
		}
		else {
			if ($this->userType == CONTENT_TASK_TYPE){
				$metadata = $this->_metaDataOperation($taskID);
			}
		}
		return $metadata;
	}
	private function _metaDataOperation($taskID){
		//分页相关
		$options = array_merge($this->params, $this->params['url'], $this->passedArgs);
		$page = 1;
		if (isset($options['page'])){
			$page = (int)$options['page'];
		}

		$platFormInfos = array();
		$attributes = array();

		$metaParamArray = array('Request'=>array('TaskID'=>$taskID,'CurrentPage'=>$page,'PageSize'=>META_LIST_SIZE));
		$metaParam = $this->_toXmlStr($metaParamArray);

		$metaData= $this->clientSoap->getMetaDataList($metaParam);

		$array = $this->XmlArray->xml2array($metaData);
		if ($array){
			$platFormInfos = $array['Response']['PlatFormInfo'];
			$platFormInfos = $this->_toNumericArray($platFormInfos);

			$attributeItems = $array['Response']['AttributeItem'];
			if ($attributeItems){
				$paging_ = $array['Response']['Paging'];
				$this->params['paging']['Content'] = $paging_;

				$attributes = $this->_toNumericArray($attributeItems);
			}
		}

		//
		return compact('platFormInfos','attributes');
	}

	/**
	 * techData
	 * 技审信息
	 */
	public function techData($id = NULL,$fileAlias = NULL){
		$this->layout=false;

		if (!TASK_AUDIT_TYPE){
			$this->_techDataOperation($id,$fileAlias);
		}
		else {
			if ($this->userType == TECH_TASK_TYPE){
				$this->_techDataOperation($id,$fileAlias);
			}
			else {
				$techAudit = array();
				$techClass = array();
				$techState = array();
				$this->set(compact('techAudit','techState','techClass'));
			}
		}
	}
	private function _techDataOperation($id = NULL,$fileAlias = NULL){
		$techAudit = array();
		$techClass = array();
		$techState = array();

		//分页相关
		$options = array_merge($this->params, $this->params['url'], $this->passedArgs);
		$page = 1;
		if (isset($options['page'])){
			$page = (int)$options['page'];
		}
		$taskID = (integer)$id;

		//接口参数定义
		if (DEFAULT_IS_ALL_TECHINFO == '1'){
			$fileAlias = DEFAULT_IS_ALL_TECHINFO;
		}
		$techParamArray = array('Request'=>array('TaskID'=>$taskID,'FileAlias'=>$fileAlias,'CurrentPage'=>$page,'PageSize'=>TECH_LIST_SIZE));
		$techParam = $this->_toXmlStr($techParamArray);

		$techAuditList = $this->clientSoap->getTechAuditList($techParam);

		if ($taskID){
			$techArray = $this->XmlArray->xml2array($techAuditList);

			$techAudit = $techArray['Response']['BugData'];
			if ($techAudit){
				$paging_ = $techArray['Response']['Paging'];
				$this->params['paging']['Tech'] = $paging_;
					
				$techAudit = $this->_toNumericArray($techAudit);
			}
		}

		$techClass= Configure::read('techclass');
		$techState= Configure::read('techstate');

		//
		$this->set(compact('techAudit','techState','techClass'));
	}
}