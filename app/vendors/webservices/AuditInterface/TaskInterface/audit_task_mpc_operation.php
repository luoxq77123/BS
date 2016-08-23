<?php
App::import('Model', 'Content');
App::import('Model', 'Flatform');
App::import('Vendor', 'DbHandle', array('file' => 'webservices' . DS . 'DB_Operation' . DS  . 'class.db_handle.php'));

App::import('Vendor', 'Array2Xml2Array', array('file' => 'xmlandarray' . DS . 'class.array2xml2array.php'));
App::import('Vendor', 'BitOperation', array('file' => 'someoperation' . DS . 'class.bit_operation.php'));
App::import('Vendor', 'NumericArray', array('file' => 'someoperation' . DS . 'class.numeric_array.php'));
class AuditTaskMpcOperation extends Object{
	public $content;
	public $flatFormParam;

	public $mpcArray = array();
	public $updateMpcArray = array();
	public $newTaskInfosMpc = array();

	public $documentTaskInfo = array();
	public $mediaBaseInfo = array();
	public $editCatalog = array();

	public $workFlowID;
	public $workFlowType;
	public $newWorkFlowType;

	//元数据信息
	public $flatFormItem;
	public $keyAttributeItem;
	public $platFormNumber;

	public function __construct(){
		$this->content = new Content();
		$this->flatFormParam = new Flatform();
	}
	/**
	 *
	 * 初始化数据
	 * @param int $taskID
	 * @param int $platFormNumber
	 */
	public function initData($taskID,$_metaData = NULL,$workflowID=NULL){
		if (!$taskID){
			$this->log('MPC报文处理：任务ID为空');
			return false;
		}
		$theData = $this->content->find('first',array('fields'=>array('mpctaskinfo'),'conditions'=> array('taskid'=>$taskID)));
		$mpcXmlData = $theData['Content']['mpctaskinfo'];
		$mpcArray = Array2Xml2Array::xml2array($mpcXmlData);

		$taskInfoMpc = $mpcArray['MPC']['Content']['AddTask']['TaskInfo'];
		$taskInfoMpc = NumericArray::toNumericArray($taskInfoMpc);

		$newTaskInfoMpcA = array();
		$documentTaskInfoA = array();
		foreach ($taskInfoMpc as $oneTaskInfoMpc){
			if ($oneTaskInfoMpc['Scope'] == 'DocumentInfo'){
				$documentTaskInfoA = $oneTaskInfoMpc;
			}
			else {
				$newTaskInfoMpcA[] = $oneTaskInfoMpc;
			}
		}
		$this->mpcArray = $mpcArray;

		$this->newTaskInfosMpc = $newTaskInfoMpcA;

		$this->documentTaskInfo = $documentTaskInfoA;
		$this->mediaBaseInfo = $documentTaskInfoA['Data']['DocumentInfo']['NewMediaBaseInfo'];
		$this->editCatalog = $documentTaskInfoA['Data']['DocumentInfo']['EDITCATALOG'];

		//元数据项
		$metaData = $_metaData;
		extract($metaData);
		$this->flatFormItem = $platFormArray;
		$this->keyAttributeItem = $newKeyAttributeArray;
		//$this->log('keyMPC:');
		//$this->log($this->keyAttributeItem);

		$this->platFormNumber = $this->_getPlatFormNumber();


		if(IS_UPDATE_WORK_FLOW){
			$workFlow = $this->mediaBaseInfo['WorkFlow'];
			$this->workFlowID = (int)$workflowID;
			$this->workFlowType = (int)$workFlow['WorkFlowType'];

			//$this->log('platFormNumber:');
			//$this->log($this->platFormNumber);
			//$this->log('pre_workflowType:');
			//$this->log($this->workFlowType);
			if ($this->platFormNumber){
				$tworkFlowType = BitOperation::shr32($this->workFlowType, WORKFLOW_CHANGE_BIT);
				$ttworkFlowType = BitOperation::shl32($tworkFlowType, WORKFLOW_CHANGE_BIT);
				$this->newWorkFlowType = $ttworkFlowType+$this->platFormNumber;
			}
			//$this->log('new_workflowType:');
			//$this->log($this->newWorkFlowType);
		}
		return true;
	}
	/**
	 *
	 * 获取流程ID
	 */
	public function getWorkFlowID(){
		return $this->workFlowID;
	}
	/**
	 *
	 * 获取新的流程Type
	 */
	public function getNewWorkFlowType(){
		return $this->newWorkFlowType;
	}
	/**
	 *
	 * 获取编辑方式
	 */
	public function getWorkStationType(){
		return $this->mediaBaseInfo['WorkStationType'];
	}

	/**
	 *
	 * @param array $taskIDData
	 */
	public function setNewMpc($taskIDData){
		$newMpcArray = $this->mpcArray;
		$newTaskInfoMpc = $this->newTaskInfosMpc;

		$newDocumentTaskInfo = $this->documentTaskInfo;
		$newMediaBaseInfo = $this->mediaBaseInfo;
		$newEditCatalog = $this->editCatalog;

		$platForms = array('PlatFormInfo'=>$this->flatFormItem);
		//策略ID及更新字段
		$policyID = $this->_getPolicyID();
		$editCatalogItems = $this->_getUpdatedEditCatalogItems($taskIDData);

		//获取到更新后的Mpc报文
		$newMediaBaseInfo['PlatFormInfos'] = $platForms;
		$newMediaBaseInfo['WorkFlow']['WorkFlowType'] = $this->newWorkFlowType;
		$newEditCatalog['AttributeItem'] = $editCatalogItems;

		$newDocumentTaskInfo['Data']['DocumentInfo']['NewMediaBaseInfo'] = $newMediaBaseInfo;
		$newDocumentTaskInfo['Data']['DocumentInfo']['EDITCATALOG'] = $newEditCatalog;
		$newTaskInfoMpc[] = $newDocumentTaskInfo;

		$newMpcArray['MPC']['Content']['AddTask']['TaskInfo'] = $newTaskInfoMpc;
		$newMpcArray['MPC']['Content']['AddTask']['PolicyID'] = $policyID;

		//特殊值处理
		$editAttributes= Configure::read('editedAttributes');
		if (defined('PGMNAME') && in_array(PGMNAME, $editAttributes)){
			if (isset($this->keyAttributeItem['Title'])){
				$newMpcArray['MPC']['Content']['AddTask']['BaseInfo']['TaskName'] = $this->keyAttributeItem['Title']['Value'];
			}
		}
		if (defined('CATALOG') && in_array(CATALOG, $editAttributes)){
			if (isset($this->keyAttributeItem['CatalogType'])){
				$newMpcArray['MPC']['Content']['AddTask']['BaseInfo']['ColumnName'] = $this->keyAttributeItem['CatalogType']['Value'];
			}
		}

		$this->updateMpcArray = $newMpcArray;
		//$this->log('newMPC:');
		//$this->log($this->updateMpcArray);
	}
	/**
	 *
	 * 回写Mpc报文
	 * @param array $taskIDData
	 * @param array $platForms
	 */
	public function rewriteMpc($taskIDData){
		//进行MPC回写
		$newMpcDataXml = new Xml($this->updateMpcArray,array('format' => 'tags'));
		$newMpcDataXml->options(array('cdata'=>false));
		$newMpcDataXmlStr = $newMpcDataXml->toString();

		//更新对应Mpc数据
		$mpcUpdState = true;
		if ($taskIDData){
			foreach ($taskIDData as $tempdata) {
				$tempID = (int)$tempdata['Content']['taskid'];
				//更新对应Mpc数据
				$blobStyle = DbHandle::_getDbSetBlobStyle();
				$sqlStr = "update et_nmpgmauditlist t set mpctaskinfo=".$blobStyle." where t.taskid=".$tempID;
				$reMpcData = $this->content->newSetBlob($sqlStr,$newMpcDataXmlStr);

				//元数据更新的状态
				$mpcUpdState = $mpcUpdState&&$reMpcData;
			}
		}

		if (!$mpcUpdState){
			$this->log('MPC报文处理：MPC报文更新失败');
		}
		return $mpcUpdState;
	}
	public function commitMpc(){
		//流程发起处理
		$nextMpcArray = array('MPCWebCmd' => array(
					               'CommandType' => 'AddTask',
					               'AddTask' => $this->updateMpcArray));
		$nextMpcXml = new Xml($nextMpcArray,array('format' => 'tags'));
		$nextMpcXml->options(array('cdata'=>false));
		$nextMpcXmlStr = $nextMpcXml->toString();

		try {
			$mpcClient = new SoapClient(WS_DOMAIN.'/mpcinterface.wsdl', array('encoding' => 'UTF-8'));
			$mpcResult = $mpcClient->mpccommit($nextMpcXmlStr);
		} catch (Exception $e) {
			$this->log('MPC发起任务：'.$e->getMessage());
		}

		//将返回结果写入文件
		$mpcReFilePath = WEBROOT_DOMAIN.MPC_RETRUN_FILENAME.'.xml';
		$mpcFile = fopen($mpcReFilePath, 'w');
		fwrite($mpcFile, $mpcResult);
		fclose($mpcFile);

		$resultState = $this->_getCommitMpcResultState($mpcResult);
		if (!$resultState){
			$this->log('MPC报文处理：MPC提交任务失败');
		}
		return $resultState;
	}
	private function _getCommitMpcResultState($theReturn){
		$dataArray = Array2Xml2Array::xml2array($theReturn);
		$status = (int)$dataArray['MPCWebCmd']['Rtn_AddTask']['MPC_Status']['Status'];
		if ($status==1){
			return true;
		}
		return false;
	}
	/**
	 *
	 * 获取策略ID
	 */
	private function _getPolicyID(){
		//获取视频制式
		$videoStandard = $this->mediaBaseInfo['VideoStandard'];
		$videoStandardNumbert = (int)$videoStandard;
		if($videoStandardNumbert == 1){
			$videoStandardNumber=0;
		}
		elseif ($videoStandardNumbert == 16){
			$videoStandardNumber=1;
		}

		//获取策略ID
		$workStationNum = BitOperation::shl32(XCUT_WORKSTATION_DEFAULT, XCUT_WORKSTATION_BIT);
		$productNum = BitOperation::shl32(XCUT_PRODUCT_DEFAULT, XCUT_PRODUCT_BIT);
		$videoStandardNum = BitOperation::shl32($videoStandardNumber, XCUT_VIDEOSTANDARD_BIT);
		$convertNum = BitOperation::shl32(XCUT_CONVERT_DEFAULT, XCUT_CONVERT_BIT);
		$censorNum = BitOperation::shl32(XCUT_CENSOR_DEFAULT, XCUT_CENSOR_BIT);

		$paramType = $workStationNum+$productNum+$videoStandardNum+$convertNum+$censorNum;

		$policy = $this->flatFormParam->find('first',array('fields'=>array('policyid'),'conditions'=>array('compoundid'=>$this->platFormNumber,'paramtype'=>$paramType)));
		if ($policy){
			return (int)$policy['Flatform']['policyid'];
		}
		$this->log('MPC报文处理：获取策略ID失败');
		return 0;
	}
	/**
	 *
	 * 获取平台代号
	 * @param array $platForms
	 */
	private function _getPlatFormNumber(){
		$thePlatForms = $this->flatFormItem;
		if (!$thePlatForms){
			return 0;
		}

		$platFormNumber = 0;
		$platFormInfo = NumericArray::toNumericArray($thePlatForms);
		foreach ($platFormInfo as $tempPlat) {
			$selectState = (int)$tempPlat['IsSelected'];
			if($selectState == IS_SELECTED){
				$platFormID = (int)$tempPlat['PlatFormID'];
				$platFormNumber += $platFormID;
			}
		}
		return $platFormNumber;
	}
	/**
	 *
	 * 获取更新后的信息组
	 * @param array $taskIDData
	 */
	private function _getUpdatedEditCatalogItems($taskIDData){
		//获取审核人以及审核时间
		//		$updateAddItems = array();
		$theAddItems = array();
		foreach ($taskIDData as $tempdata) {
			$tempType = (int)$tempdata['Content']['tasktype'];
			$tempAuditor = $tempdata['Content']['auditorname'];
			$tempDate = $tempdata['Content']['taskauditdate'];

			if (!TASK_AUDIT_TYPE){
				//				$updateAddItems = array_merge($updateAddItems,array('AuditCensor'=>$tempAuditor,'AuditDateTime'=>$tempDate));

				$theAddItems['AuditCensor'] = array('ItemCode'=>'AuditCensor','ItemName'=>'审核人员','Value'=>$tempAuditor);
				$theAddItems['AuditDateTime'] = array('ItemCode'=>'AuditDateTime','ItemName'=>'审核时间','Value'=>$tempDate);
			}
			else {
				if ($tempType == CONTENT_TASK_TYPE){
					//					$updateAddItems = array_merge($updateAddItems,array('ContentCensor'=>$tempAuditor,'ContentDateTime'=>$tempDate));

					$theAddItems['ContentCensor'] = array('ItemCode'=>'ContentCensor','ItemName'=>'内审人员','Value'=>$tempAuditor);
					$theAddItems['ContentDateTime'] = array('ItemCode'=>'ContentDateTime','ItemName'=>'内审时间','Value'=>$tempDate);
				}
				elseif ($tempType == TECH_TASK_TYPE){
					//					$updateAddItems = array_merge($updateAddItems,array('TechCensor'=>$tempAuditor,'TechDateTime'=>$tempDate));

					$theAddItems['TechCensor'] = array('ItemCode'=>'TechCensor','ItemName'=>'技审人员','Value'=>$tempAuditor);
					$theAddItems['TechDateTime'] = array('ItemCode'=>'TechDateTime','ItemName'=>'技审时间','Value'=>$tempDate);
				}
			}
		}

		//
		$keyAttributeArray = $this->keyAttributeItem;
		$attributeCode = array_keys($keyAttributeArray);

		foreach ($theAddItems as $tmpUpdateCode=>$tmpUpdateItem) {
			if (in_array($tmpUpdateCode, $attributeCode)){
				//更新
				$tmpCodeItem = $keyAttributeArray[$tmpUpdateCode];
				$tmpCodeItem['Value'] = $tmpUpdateItem['Value'];
				$keyAttributeArray[$tmpUpdateCode] = $tmpCodeItem;
			}
			else{
				//添加
				$keyAttributeArray[$tmpUpdateCode] = $tmpUpdateItem;
			}
		}
		//$this->log('newKey:');
		//$this->log($keyAttributeArray);

		return array_values($keyAttributeArray);
	}
}