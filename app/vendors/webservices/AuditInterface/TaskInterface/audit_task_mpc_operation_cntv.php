<?php
App::import('Model', 'Content');
APP::import('Model', 'ColumnPolicy');
App::import('Vendor', 'DbHandle', array('file' => 'webservices' . DS . 'DB_Operation' . DS  . 'class.db_handle.php'));

App::import('Vendor', 'Array2Xml2Array', array('file' => 'xmlandarray' . DS . 'class.array2xml2array.php'));
App::import('Vendor', 'Array2Xml', array('file' => 'xmlandarray' . DS . 'array2xml.php'));
App::import('Vendor', 'Xml2Array', array('file' => 'xmlandarray' . DS . 'xml2array.php'));
App::import('Vendor', 'NumericArray', array('file' => 'someoperation' . DS . 'class.numeric_array.php'));
class AuditTaskMpcCntvOperation extends Object{
	public $content;
	public $columnPolicy;

	public $mpcArray = array();
	public $updateMpcArray = array();
	public $newTaskInfosMpc = array();

	public $documentTaskInfo = array();
	public $editCatalog = array();
	public $sobeyExchangeProtocal = array();
	public $entityData = array();


	public $columnID;
	public $serverIP;
	public $policyID;
	public $mqName;

	//元数据信息
	public $keyAttributeItem;

	public function __construct(){
		$this->content = new Content();
		$this->columnPolicy = new ColumnPolicy();
	}
	/**
	 *
	 * 初始化数据
	 * @param int $taskID
	 */
	public function initData($taskID,$_metaData = NULL,$workflowID=NULL){
		if (!$taskID){
			$this->log('MPC报文处理：任务ID为空');
			return false;
		}

		$theData = $this->content->find('first',array('fields'=>array('pgmcolumnid','mpctaskinfo'),'conditions'=> array('taskid'=>$taskID)));
		$this->columnID = (int)$theData['Content']['pgmcolumnid'];

		$mpcXmlData = $theData['Content']['mpctaskinfo'];
		//$mpcArray = Array2Xml2Array::xml2array($mpcXmlData);
		$mpcArray = XML2Array::createArray($mpcXmlData);

		$taskInfoMpc = $mpcArray['MPC']['Content']['AddTask']['TaskInfo'];
		$taskInfoMpc = NumericArray::toNumericArray($taskInfoMpc);

		$newTaskInfoMpcA = array();
		$documentTaskInfoA = array();
		$sobeyExchangeProtocalA = array();
		foreach ($taskInfoMpc as $oneTaskInfoMpc){
			if ($oneTaskInfoMpc['Scope'] == 'DocumentInfo'){
				$documentTaskInfoA = $oneTaskInfoMpc;
			}
			elseif ($oneTaskInfoMpc['Scope'] == 'tv_SobeyExchangeProtocal'){
				$sobeyExchangeProtocalA = $oneTaskInfoMpc;
			}
			else {
				$newTaskInfoMpcA[] = $oneTaskInfoMpc;
			}
		}
		$this->mpcArray = $mpcArray;

		$this->newTaskInfosMpc = $newTaskInfoMpcA;

		$this->documentTaskInfo = $documentTaskInfoA;
		$this->editCatalog = $documentTaskInfoA['Data']['DocumentInfo']['CNTVInfo'];
		$this->sobeyExchangeProtocal = $sobeyExchangeProtocalA;
		$this->entityData = $sobeyExchangeProtocalA['Data']['UnifiedContentDefine']['ContentInfo']['ContentData']['EntityData'];

		//元数据项
		$metaData = $_metaData;
		extract($metaData);
		$this->keyAttributeItem = $newKeyAttributeArray;

		return true;
	}
	/**
	 *
	 * 获取流程ID
	 */
	public function getWorkFlowID(){
		return NULL;
	}
	/**
	 *
	 * 获取新的流程Type
	 */
	public function getNewWorkFlowType(){
		return NULL;
	}
	/**
	 *
	 * 获取编辑方式
	 */
	public function getWorkStationType(){
		$cntvInfo = $this->documentTaskInfo['Data']['DocumentInfo']['CNTVInfo'];
		$cntvAttribute = NumericArray::toNumericArray($cntvInfo['AttributeItem']);
		if ($cntvAttribute){
			foreach ($cntvAttribute as $item) {
				if ($item['ItemCode'] == 'TYPE'){
					return $item['Value'];
				}
			}
		}
		return false;
	}

	/**
	 *
	 * @param array $taskIDData
	 */
	public function setNewMpc($taskIDData){
		$newMpcArray = $this->mpcArray;
		$newTaskInfoMpc = $this->newTaskInfosMpc;

		$newDocumentTaskInfo = $this->documentTaskInfo;		
		
		$newSobeyExchangeProtocal = $this->sobeyExchangeProtocal;
		$newEntityData = $this->entityData;

		//策略ID及更新字段
		$this->policyID = $this->_getPolicyID();
		$newEditCatalog = $this->_getUpdatedEditCatalogItems();

		$entityDataItems = $this->_getUpdateContentInfo($taskIDData);


		//特殊值处理
		$editAttributes= Configure::read('editedAttributes');
		if (defined('PGMNAME') && in_array(PGMNAME, $editAttributes)){
			if (isset($this->keyAttributeItem['PgmName'])){
				$newPgmName = $this->keyAttributeItem['PgmName']['Value'];
				$newMpcArray['MPC']['Content']['AddTask']['BaseInfo']['TaskName'] = $newPgmName;
				$newDocumentTaskInfo['Data']['DocumentInfo']['PGMNAME'] = $newPgmName;
				$newDocumentTaskInfo['Data']['DocumentInfo']['EDITCATALOG']['PGMNAME'] = $newPgmName;
			}
		}
			
		//获取到更新后的Mpc报文
		$newDocumentTaskInfo['Data']['DocumentInfo']['CNTVInfo'] = $newEditCatalog;
		
		$newEntityData['AttributeItem'] = $entityDataItems;
		$newSobeyExchangeProtocal['Data']['UnifiedContentDefine']['ContentInfo']['ContentData']['EntityData'] = $newEntityData;
		
		$newTaskInfoMpc[] = $newDocumentTaskInfo;
		$newTaskInfoMpc[] = $newSobeyExchangeProtocal;

		$newMpcArray['MPC']['Content']['AddTask']['TaskInfo'] = $newTaskInfoMpc;
		$newMpcArray['MPC']['Content']['AddTask']['PolicyID'] = $this->policyID;

		//更新优先级别
		$newMpcArray['MPC']['Content']['AddTask']['BaseInfo']['TaskPriority'] = 0;

		$this->updateMpcArray = $newMpcArray;
	}
	/**
	 *
	 * 回写Mpc报文
	 * @param array $taskIDData
	 */
	public function rewriteMpc($taskIDData){
		//进行MPC回写
/*
		$newMpcDataXml = new Xml($this->updateMpcArray,array('format' => 'tags'));
		$newMpcDataXml->options(array('cdata'=>false));
		$newMpcDataXmlStr = $newMpcDataXml->toString();
*/
                Array2XML::init('1.0', 'UTF-8', false);
                $xml = Array2XML::createXML('MPC', $this->updateMpcArray['MPC']);
                $r = $xml->saveXML();
                $r = str_replace('<?xml version="1.0" encoding="UTF-8"?>', '', $r);
                $newMpcDataXmlStr = trim($r);

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
		$this->log('MPC调度：columnid->'.$this->columnID.';serverip->'.$this->serverIP.';policyid->'.$this->policyID,LOG_DEBUG);
		//流程发起处理
/*
		$nextMpcArray = array('MPCWebCmd' => array(
					               'CommandType' => 'AddTask',
					               'AddTask' => $this->updateMpcArray));
		$nextMpcXml = new Xml($nextMpcArray,array('format' => 'tags'));
		$nextMpcXml->options(array('cdata'=>false));
		$nextMpcXmlStr = $nextMpcXml->toString();
*/
                $nextMpcArray = array(
                                     'CommandType' => 'AddTask',
                                     'AddTask' => $this->updateMpcArray);

                Array2XML::init('1.0', 'UTF-8', false);
                $xml = Array2XML::createXML('MPCWebCmd', $nextMpcArray);
                $r = $xml->saveXML();

                $r = str_replace('<?xml version="1.0" encoding="UTF-8"?>', '', $r);
                $nextMpcXmlStr = trim($r);
				//$this->log($nextMpcXmlStr);
		try {
			header("content-type:text/html;charset=utf-8");
			$clientSoap = new SoapClient(MPC_WS_DOMAIN.'/mpc/CallSobeyInterface.asmx?wsdl');
			$mpcResult = $clientSoap->__soapCall('CallSobeyMpc', array('parameters'=>array('mpcXml'=>$nextMpcXmlStr)));
		} catch (Exception $e) {
			$this->log('MPC发起任务：'.$e->getMessage());
		}

		//将返回结果写入文件
		$mpcReFilePath = WEBROOT_DOMAIN.MPC_RETRUN_FILENAME.'.xml';
		$mpcFile = fopen($mpcReFilePath, 'w');
		fwrite($mpcFile, $mpcResult);
		fclose($mpcFile);

		$resultState = $this->_getCommitMpcResultState($mpcResult);
		$this->log($resultState);
		if (!$resultState){
			$this->log('MPC报文处理：MPC提交任务失败');
		}
		return $resultState;
	}
	private function _getCommitMpcResultState($result){
		$data = $result->CallSobeyMpcResult;
		if ($data=='Success') {
			return true;
		}
		return false;
	}
	/**
	 *
	 * 获取策略ID
	 */
	private function _getPolicyID(){
		$policy = $this->columnPolicy->find('first',array('fields'=>array('policyid','serverip','mqname'),'conditions'=>array('columnid'=>$this->columnID,'usetype'=>5)));
		if ($policy){
			$this->serverIP = $policy['ColumnPolicy']['serverip'];
			$this->mqName = $policy['ColumnPolicy']['mqname'];
			return (int)$policy['ColumnPolicy']['policyid'];
		}
		$this->log('MPC报文处理：获取策略ID失败');
		return 0;
	}
	/**
	 *
	 * 获取更新后的信息组
	 * @param array $taskIDData
	 */
	private function _getUpdatedEditCatalogItems($taskIDData){
		$newEditCatalog = $this->editCatalog;
		if (isset($this->keyAttributeItem['PgmName'])){
			$newEditCatalog['PGMNAME'] = $this->keyAttributeItem['PgmName']['Value'];
		}
		if (isset($this->keyAttributeItem['PgmName_Modify'])){
			$newEditCatalog['PGMNAME_Modify'] = $this->keyAttributeItem['PgmName_Modify']['Value'];
		}
		return $newEditCatalog;
	}
	private function  _getUpdateContentInfo($taskIDData){
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
		
		$entityDataItems = $this->entityData['AttributeItem'];
		$entityDataItems = NumericArray::toNumericArray($entityDataItems);
		
		$keyAttributeArray = array();
		foreach ($entityDataItems as $theOneItem) {
			$theKey = $theOneItem['ItemCode'];
			$keyAttributeArray[$theKey] = $theOneItem;
		}
		
	    //更新素材名
	    $keyAttributeArray['ClipName']['Value'] = $this->keyAttributeItem['PgmName']['Value'];
		
		//获取Content
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

		return array_values($keyAttributeArray);		
	}
}
