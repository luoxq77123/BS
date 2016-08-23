<?php

APP::import('Controller', 'AppAudits');
App::import('Vendor', 'Array2Xml2Array', array('file' => 'xmlandarray' . DS . 'class.array2xml2array.php'));

class AuditHandlesController extends AppAuditsController {

    public $name = 'AuditHandles';
    public $uses = array('Content', 'Relationship');

    /**
     * commitTask
     * 提交或更新任务
     * @param mix $state
     */
    public function commitTask() {
	$this->layout = false;
	$post = $_POST;
	$taskID = (int) $post['TaskID'];

	//获取当前任务信息
	$fields = array('auditlevel', 'pgmguid');
	$taskInfop = $this->_getCurTaskInfo($taskID, $fields);
	$theTaskLevel = (int) $taskInfop['auditlevel'];
	if (!in_array($theTaskLevel, $this->userTaskLevel)) {
	    $this->Session->setFlash('您好，该任务已审核！', false);
	} else {
	    //获取提交taskInfo
	    $theNote = $post['ContentAuditNote'];
	    $state = (int) $post['State'];
	    $taskInfo = array(
		'TaskID' => $taskID,
		'UserAuditPopedom' => $this->userAuditPopedom,
		'AuditorID' => $this->userID,
		'AuditorName' => $this->userName,
		'ContentAuditState' => $state,
		'ContentAuditNote' => $theNote
	    );

	    $selectFlatIDString = $post['SelectPlatID'];
	    $platFormUpdate = explode(',', $selectFlatIDString);


	    $updateMetaString = $post['UpdateMetaData'];
	    $updataMetaData = (array) json_decode($updateMetaString);

	    $taskInfoParamArray = array('Request' => array(
		    'TaskInfo' => $taskInfo,
		    'PlatFormUpdate' => $platFormUpdate,
		    'MetaDataUpdate' => $updataMetaData));
	    $taskInfoParam = $this->_toXmlStr($taskInfoParamArray);

	    $result = $this->clientSoap->updateTask($taskInfoParam);

	    $reArray = $this->XmlArray->xml2array($result);
	    $respCode = $reArray['Response']['RespCode'];
	    if ($respCode == DATA_SUCCESS) {
		if ($state == PASS_AUDIT_TASK_STATE) {
		    //cntv 关联入库需要
		    if (IS_CNTV) {
			$guid = $taskInfop['pgmguid'];
			//add 1030 这里用来判断是否对原标题进行了修改，如果进行了修改就要同事更新关联库里面的标题和XML
			$update_title = (isset($updataMetaData['PgmName']) && $updataMetaData['PgmName']) ? $updataMetaData['PgmName'] : false;
			$this->relationshipUpdate($guid, $update_title);
		    }
		    echo '通过任务成功';
		} elseif ($state == RETURN_AUDIT_TASK_STATE) {
		    echo '退回任务成功';
		} elseif ($state == ONLY_SAVE_TASK) {
		    if (IS_CNTV) {
			$guid = $taskInfop['pgmguid'];
			//add 1030 这里用来判断是否对原标题进行了修改，如果进行了修改就要同事更新关联库里面的标题和XML
			$update_title = (isset($updataMetaData['PgmName']) && $updataMetaData['PgmName']) ? $updataMetaData['PgmName'] : false;
			if ($update_title) {
			    $this->only_save_rel_update($guid, $update_title);
			}
		    }
		    echo '保存任务成功';
		}
	    } elseif ($respCode == DATA_FAILD) {
		echo '更新任务失败';
	    } else {
		
	    }
	}
    }

    public function updateTaskState() {
	$this->layout = false;

	$taskID = (int) $_POST['TaskID'];
	$setID = (int) $_POST['SetID'];

	if (in_array($setID, array(1, 3, 4, 5))) {
	    $refreshParamArray = array('Request' => array(
		    'TaskID' => $taskID,
		    'ContentAuditState' => SELECTED_TASK_STATE,
		    'LockerID' => $this->userID,
		    'AuditLevel' => $this->userTaskLevel));
	    $refreshParam = $this->_toXmlStr($refreshParamArray);
	    $result = $this->clientSoap->refreshTaskState($refreshParam);
	}
    }

    /**
     *
     * 解锁操作
     */
    public function unlockOperation() {
	$this->layout = false;

	$unlockID = (int) $_POST['unlocktaskid'];
	$this->unLockTask($unlockID);
    }

    /**
     *
     * 解锁任务
     * @param unknown_type $id
     */
    private function unLockTask($id = NULL) {
	//进行任务解锁
	if ($id) {
	    $refreshParamArray = array('Request' => array(
		    'TaskID' => $id,
		    'LockerID' => 0,
		    'LockState' => NOT_LOCK_STATE,
		    'UserID' => $this->userID,
		    'ContentAuditState' => NOT_AUDIT_TASK_STATE,
		    'AuditLevel' => $this->userTaskLevel));
	    $refreshParam = $this->_toXmlStr($refreshParamArray);
	    $result = $this->clientSoap->refreshLockInfo($refreshParam);

	    //解锁时设置流程
	    if (IS_UPDATE_WORK_FLOW) {
		$this->setWorkFlowExit($id);
	    }
	}
    }

    private function setWorkFlowExit($taskID) {
	$field = array('workflowid');
	$flowIDParamArray = array('Request' => array('Condition' => array('taskid' => $taskID), 'Field' => $field));
	$flowIDParam = $this->_toXmlStr($flowIDParamArray);
	$flowIDList = $this->clientSoap->getTaskWithCondition($flowIDParam);

	$taskArray = $this->XmlArray->xml2array($flowIDList);
	$theTaskInfo = $taskArray['Response']['TaskInfo'];

	$workFlowID = $this->_toNumericArray($theTaskInfo['FlowIDs']);
	$reStepParamArray = array('Request' => array(
		'WorkFlowID' => $workFlowID,
		'TaskType' => $this->userType,
		'AuditLevel' => $this->userTaskLevel,
		'UserName' => $this->userName,
		'Flag' => 'exit'));
	$reStepParam = $this->_toXmlStr($reStepParamArray);
	$setStepState = $this->clientSoap->setWorkFlowStep($reStepParam);
    }

    private function relationshipUpdate($guid, $update_title) {
	if ($update_title) {
	    //基本信息修改不需要过滤特殊符号
	    $sql = "update ET_NM_CNTVPGMREL set OPERATESTATE = 0,PGMNAME = '" . $this->escapeValue($update_title) . "' where OPERATESTATE = 10 and PGMGUID = '" . $guid . "' and PGMTYPE = 1";
	} else {
	    //找到节目状态更新关联库中该节目的状态 10 变为 0
	    $sql = "update ET_NM_CNTVPGMREL set OPERATESTATE = 0 where OPERATESTATE = 10 and PGMGUID = '" . $guid . "' and PGMTYPE = 1";
	}
	//更新关联库中的报文，新加ContentCensor节点
	$relUpdateSql = $this->Content->query($sql);
	$this->log('sql:' . $sql . '|' . 'result:' . $relUpdateSql, 'relupdatesql');
//	$this->log($guid);
	$this->update_xml($guid, $update_title);
    }

    private function only_save_rel_update($guid, $update_title) {
	//基本信息修改不需要过滤特殊符号
	$sql = "update ET_NM_CNTVPGMREL set PGMNAME = '" . $this->escapeValue($update_title) . "' where OPERATESTATE = 10 and PGMGUID = '" . $guid . "' and PGMTYPE = 1";
	//更新关联库中的报文，新加ContentCensor节点
	$this->Content->query($sql);
//	$this->log($guid);
	$this->update_xml($guid, $update_title, true);
    }

    /**
     *
     * 初始化数据
     * @param string $guid
     */
    public function update_xml($guid, $update_title, $only_save = false) {
	if (!$guid) {
	    $this->log('audit_handles_controller:MPC报文处理：节目为空');
	    return false;
	}

	$theData = $this->Relationship->find('first', array('fields' => array('mpcxml'), 'conditions' => array('PGMGUID' => $guid)));
	if( !$theData ) {
		$this->log($guid . '|没有找到数据', 'commit');
	}
	$mpcArray = Array2Xml2Array::xml2array($theData['Relationship']['mpcxml']);
	$add_array = array('ItemCode' => 'ContentCensor', 'Value' => $this->userName, 'ItemName' => '审核人');
	//在报文中找到节点scope值为tv_SobeyExchangeProtocal的节点
//	$this->log($mpcArray['MPC']['Content']['AddTask']['TaskInfo']);
	if (!$only_save) {
	    foreach ($mpcArray['MPC']['Content']['AddTask']['TaskInfo'] as $k => $v) {
		if ($v['Scope'] === 'tv_SobeyExchangeProtocal') {
		    $mpcArray['MPC']['Content']['AddTask']['TaskInfo'][$k]['Data']['UnifiedContentDefine']['ContentInfo']['ContentData']['EntityData']['AttributeItem'][] = $add_array;
		    break;
		}
	    }
	}
	//add 1030 
	if ($update_title) {
	    $mpcArray['MPC']['Content']['AddTask']['BaseInfo']['TaskName'] = $this->format_sepcial_chars($update_title);
	    $documentInfo_update = false;
	    $tv_SobeyExchangeProtocal_update = false;
	    foreach ($mpcArray['MPC']['Content']['AddTask']['TaskInfo'] as $k => $v) {
		if ($v['Scope'] === 'DocumentInfo') {
		    $mpcArray['MPC']['Content']['AddTask']['TaskInfo'][$k]['Data']['DocumentInfo']['PGMNAME'] = $this->format_sepcial_chars($update_title);
		    $documentInfo_update = true;
		}
		if ($v['Scope'] === 'tv_SobeyExchangeProtocal') {
		    $EntityData_attr = &$mpcArray['MPC']['Content']['AddTask']['TaskInfo'][$k]['Data']['UnifiedContentDefine']['ContentInfo']['ContentData']['EntityData']['AttributeItem'];
		    foreach ($EntityData_attr as $kk => &$vv) {
			if ($vv['ItemCode'] === 'ClipName') {
			    $vv['Value'] = $this->format_sepcial_chars($update_title);
			    $tv_SobeyExchangeProtocal_update = true;
			    break;
			}
		    }
		}
		if ($documentInfo_update && $tv_SobeyExchangeProtocal_update) {
		    break;
		}
	    }
	}
//	$this->log($mpcArray['MPC']['Content']['AddTask']['TaskInfo']);
	$newMpcDataXml = new Xml($mpcArray, array('format' => 'tags'));
	$newMpcDataXml->options(array('cdata' => false));
	//修改后的报文 可以存入mpcxml字段中了
	$newMpcDataXmlStr = $newMpcDataXml->toString();
//	    更新对应Mpc数据
	$blobStyle = ':data';
	$sqlStr = "update ET_NM_CNTVPGMREL t set MPCXML=" . $blobStyle . " where t.PGMGUID='" . $guid . "'";
	$reMpcData = $this->Relationship->newSetBlob($sqlStr, $newMpcDataXmlStr);
//	$this->log($reMpcData);
    }

    /**
     * sql值特使处理
     */
    private function escapeValue($value = NULL) {
	return str_replace(array('\\', "\0", "\n", "\r", "'", '"'), array('\\\\', '\\0', '\\n', '\\r', "''", '\\"'), $value);
    }

}