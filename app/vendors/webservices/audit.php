<?php

App::import('Vendor', 'AuditCommon', array('file' => 'webservices' . DS . 'AuditInterface' . DS . 'audit_common.php'));

class Audit extends AuditCommon {
    /* 用户接口 */

    /**
     * login
     * @param string $params
     * @return string
     */
    function login($params) {
	$returnArray = array();
	$respCode = $respDesc = $userID = $userName = $userCode = '';
	if ($params) {
//参数解析
	    $theParams = $this->_resolutionParams($params);
	    $account = $theParams['Account'];
	    $password = $theParams['Password'];

	    $userOperation = new AuditUserOperation();
	    $theUserInfo = $userOperation->getUserInfoWithLogin($account, $password);
	    if ($theUserInfo) {
		$respCode = USER_SUCCESS;
		$respDesc = '请求执行成功';

		$userInfo = $this->_dataTranscode($theUserInfo);
		$userID = $userInfo['userid'];
		$userName = $userInfo['username'];
		$userCode = $userInfo['usercode'];
	    } else {
		$respCode = LOGIN_NAMEORPW_ERROR;
		$respDesc = '用户名或密码错误';
	    }
	} else {
	    $respCode = USER_PARAMS_ERROR;
	    $respDesc = '参数错误';
	}
//进行结果反馈
	$returnArray = array('RespCode' => $respCode, 'RespDesc' => $respDesc, 'UserID' => $userID, 'UserName' => $userName, 'UserCode' => $userCode);
	return $this->_returnData($returnArray);
    }

    /**
     * getUserInfo
     * @param string $params
     * @return string
     */
    function getUserInfo($params) {
	$respCode = $respDesc = $userID = $userName = $userCode = '';
	if ($params) {
//参数解析
	    $theParams = $this->_resolutionParams($params);
	    $theUserID = (int) $theParams['UserID'];

	    $userOperation = new AuditUserOperation();
	    $theUserInfo = $userOperation->getUserInfoWithID($theUserID);
	    if ($theUserInfo) {
		$respCode = USER_SUCCESS;
		$respDesc = '请求执行成功';

		$userInfo = $this->_dataTranscode($theUserInfo);
		$userID = $userInfo['userid'];
		$userName = $userInfo['username'];
		$userCode = $userInfo['usercode'];
	    } else {
		$respCode = NOT_THEUSER;
		$respDesc = '该用户不存在';
	    }
	} else {
	    $respCode = USER_PARAMS_ERROR;
	    $respDesc = '参数错误';
	}
//进行结果反馈
	$returnArray = array('RespCode' => $respCode, 'RespDesc' => $respDesc, 'UserID' => $userID, 'UserName' => $userName, 'UserCode' => $userCode);
	return $this->_returnData($returnArray);
    }

    /**
     * getUserColumns
     * @param string $params
     * @return string
     */
    function getUserColumns($params) {
	$respCode = $respDesc = '';
	$userColumn = array();
	if ($params) {
//参数解析
	    $theParams = $this->_resolutionParams($params);
	    $theUserID = (int) $theParams['UserID'];

	    $userOperation = new AuditUserOperation();
	    $columnData = $userOperation->getUserColumnsWithID($theUserID);
	    if ($columnData) {
		$respCode = USER_SUCCESS;
		$respDesc = '请求执行成功';

		if (DB_DRIVER == 'db2') {
		    $columnData = $this->_dataTranscode($columnData);
		}
		$userColumn = $columnData;
	    } else {
		$respCode = NOT_THEUSER;
		$respDesc = '该用户不存在';
	    }
	} else {
	    $respCode = USER_PARAMS_ERROR;
	    $respDesc = '参数错误';
	}
	$userColumn = $this->assoc_unique($userColumn, 'COLUMN_ID');
//进行结果反馈
	$returnArray = array('RespCode' => $respCode, 'RespDesc' => $respDesc, 'Columns' => $userColumn);
	return $this->_returnData($returnArray);
    }

    /**
     * getUserPopedom
     * @param string $params
     * @return string
     */
    function getUserPopedom($params) {
	$respCode = $respDesc = '';
	$contentPopedom = NOT_AUDIT_POPEDOM;
	$techPopedom = NOT_AUDIT_POPEDOM;
	if ($params) {
//参数解析
	    $theParams = $this->_resolutionParams($params);
	    $theUserID = (int) $theParams['UserID'];

	    $userOperation = new AuditUserOperation();
	    $userPopedom = $userOperation->getUserPopedomWithID($theUserID);
	    if ($userPopedom) {
		$respCode = USER_SUCCESS;
		$respDesc = '请求执行成功';

		$auditPopedom = $userPopedom['AuditPopedom'];
		$contentPopedom = $userPopedom['ContentPopedom'];
		$techPopedom = $userPopedom['TechPopedom'];
	    } else {
		$respCode = NOT_THEUSER;
		$respDesc = '该用户不存在';
	    }
	} else {
	    $respCode = USER_PARAMS_ERROR;
	    $respDesc = '参数错误';
	}
//进行结果反馈
	$returnValue = array('RespCode' => $respCode, 'RespDesc' => $respDesc, 'AuditPopedom' => $auditPopedom, 'ContentPopedom' => $contentPopedom, 'TechPopedom' => $techPopedom);
	return $this->_returnData($returnValue);
    }

    /**
     * getUserAccountPopedom
     * @param string $params
     * @return string
     */
    function getUserAccountPopedom($params) {
	$respCode = $respDesc = '';
	$popedom = ONLY_ACCOUNT_POPEDOM;
	if ($params) {
//参数解析
	    $theParams = $this->_resolutionParams($params);
	    $theUserID = (int) $theParams['UserID'];

	    $userOperation = new AuditUserOperation();
	    $accountPopedom = $userOperation->getUserAccountPopedomWithID($theUserID);
	    if ($accountPopedom) {
		$respCode = USER_SUCCESS;
		$respDesc = '请求执行成功';

		$popedom = $accountPopedom;
	    } else {
		$respCode = NOT_THEUSER;
		$respDesc = '该用户不存在';
	    }
	} else {
	    $respCode = USER_PARAMS_ERROR;
	    $respDesc = '参数错误';
	}
//进行结果反馈
	$returnValue = array('RespCode' => $respCode, 'RespDesc' => $respDesc, 'Popedom' => $popedom);
	return $this->_returnData($returnValue);
    }

    /* 基础数据接口 */

    /**
     * getTaskList
     * @param string $params
     * @return string
     */
    function getTaskList($params) {
	$respCode = $respDesc = '';
	$paging = $resultValue = array();
	if ($params) {
//参数解析
	    $theParams = $this->_resolutionParams($params);
	    $taskOperation = new AuditTaskOperation();
	    $returnData = $taskOperation->getTaskListWithConditon($theParams);
	    if ($returnData) {
		$respCode = DATA_SUCCESS;
		$respDesc = '请求执行成功';

//编码转换相关处理
		$paging = $returnData['Paging'];
		$resultValue = $this->_dataTranscode($returnData['TaskList']);
	    } else {
		$respCode = DATA_NOT_TASK;
		$respDesc = '该用户没有待审核任务';
	    }
	} else {
	    $respCode = DATA_PARAMS_ERROR;
	    $respDesc = '参数错误';
	}

	$returnValue = array('RespCode' => $respCode, 'RespDesc' => $respDesc, 'Paging' => $paging, 'TaskList' => $resultValue);
	return $this->_returnData($returnValue);
    }

    /**
     * getTaskCount
     * @param string $params
     * @return string
     */
    function getTaskCount($params) {
	$respCode = $respDesc = '';
	$allCount = 0;
	if ($params) {
//参数解析
	    $theParams = $this->_resolutionParams($params);

	    $taskOperation = new AuditTaskOperation();
	    $count = $taskOperation->getTaskCountWithCondition($theParams);

	    if ($count) {
		$respCode = DATA_SUCCESS;
		$respDesc = '请求执行成功';

		$allCount = $count;
	    } else {
		$respCode = DATA_NOT_TASK;
		$respDesc = '该用户没有待审核任务';
	    }
	} else {
	    $respCode = DATA_PARAMS_ERROR;
	    $respDesc = '参数错误';
	}

	$returnValue = array('RespCode' => $respCode, 'RespDesc' => $respDesc, 'AllCount' => $allCount);
	return $this->_returnData($returnValue);
    }

    /**
     * getTask
     * @param string $params
     * @return string
     */
    function getTask($params) {
	$respCode = $respDesc = '';
	$returnData = array();
	if ($params) {
//参数解析
	    $theParams = $this->_resolutionParams($params);
	    $taskID = (int) $theParams['TaskID'];
	    $fields = array();
	    if (isset($theParams['Fields'])) {
		$fields = $theParams['Fields'];
	    }

	    $taskOperation = new AuditTaskOperation();
	    $theTaskInfo = $taskOperation->getTaskWithID($taskID, $fields);
	    if ($theTaskInfo) {
		$respCode = DATA_SUCCESS;
		$respDesc = '请求执行成功';

		$returnData = $this->_dataTranscode($theTaskInfo);
	    } else {
		$respCode = DATA_NOT_DATA;
		$respDesc = '没有对应数据';
	    }
	} else {
	    $respCode = DATA_PARAMS_ERROR;
	    $respDesc = '参数错误';
	}

	$returnValue = array('RespCode' => $respCode, 'RespDesc' => $respDesc, 'TaskInfo' => $returnData);
	return $this->_returnData($returnValue);
    }

    /**
     * getTaskWithCondition
     * @param string $params
     * @return string
     */
    function getTaskWithCondition($params) {
	$respCode = $respDesc = '';
	$returnData = array();
	if ($params) {
//参数解析
	    $theParams = $this->_resolutionParams($params);

	    $taskOperation = new AuditTaskOperation();
	    $taskInfo = $taskOperation->getTaskWithConditionWith($theParams);
	    if ($taskInfo) {
		$respCode = DATA_SUCCESS;
		$respDesc = '请求执行成功';

		$returnData = $this->_dataTranscode($taskInfo);
	    } else {
		$respCode = DATA_NOT_DATA;
		$respDesc = '没有对应数据';
	    }
	} else {
	    $respCode = DATA_PARAMS_ERROR;
	    $respDesc = '参数错误';
	}

	$returnValue = array('RespCode' => $respCode, 'RespDesc' => $respDesc, 'TaskInfo' => $returnData);
	return $this->_returnData($returnValue);
    }

    /**
     * getTaskWithWorkFlowID
     * @param string $params
     * @return string
     */
    function getTaskWithWorkFlowID($params) {
//		$this->log($params);
	$respCode = $respDesc = '';
	$returnData = array();
	if ($params) {
//参数解析
	    $theParams = $this->_resolutionParams($params);

	    $taskOperation = new AuditTaskOperation();
	    $taskInfo = $taskOperation->getTaskWithWorkFlowIDWith($theParams);
	    if ($taskInfo) {
		$respCode = DATA_SUCCESS;
		$respDesc = '请求执行成功';

		$returnData = $this->_dataTranscode($taskInfo);
	    } else {
		$respCode = DATA_NOT_DATA;
		$respDesc = '没有对应数据';
	    }
	} else {
	    $respCode = DATA_PARAMS_ERROR;
	    $respDesc = '参数错误';
	}

	$returnValue = array('RespCode' => $respCode, 'RespDesc' => $respDesc, 'TaskInfo' => $returnData);
	return $this->_returnData($returnValue);
    }

    /**
     * getTechAuditList
     * @param string $params
     * @return string
     */
    function getTechAuditList($params) {
	$respCode = $respDesc = '';
	$paging = $results = array();
	if ($params) {
//参数解析
	    $theParams = $this->_resolutionParams($params);

	    $specialTaskInfo = new AuditTaskSpecialInfo();
	    $techData = $specialTaskInfo->getTechAuditListWithID($theParams);
	    if ($techData) {
		$respCode = DATA_SUCCESS;
		$respDesc = '请求执行成功';

		$paging = $techData['Paging'];
		$results = $techData['BugData'];
	    } else {
		$respCode = DATA_NOT_TECH_DATA;
		$respDesc = '没有技审数据';
	    }
	} else {
	    $respCode = DATA_PARAMS_ERROR;
	    $respDesc = '参数错误';
	}

	$returnValue = array('RespCode' => $respCode, 'RespDesc' => $respDesc, 'Paging' => $paging, 'BugData' => $results);
	return $this->_returnData($returnValue);
    }

    /**
     * getMetaDataList
     * @param string $params
     * @return string
     */
    function getMetaDataList($params) {
	$respCode = $respDesc = '';
	$thePlatForm = $theAttribute = array();
	if ($params) {
//参数解析
	    $theParams = $this->_resolutionParams($params);
	    $specialTaskInfo = new AuditTaskSpecialInfo();
	    $metaData = $specialTaskInfo->getMetaDataListWithID($theParams);
	    if ($metaData) {
		$respCode = DATA_SUCCESS;
		$respDesc = '请求执行成功';

//获取对应数据
		$thePlatForm = $metaData['PlatFormInfo'];
		$theAttribute = $metaData['AttributeItem'];
		$paging = $metaData['Paging'];
	    } else {
		$respCode = DATA_NOT_META_DATA;
		$respDesc = '没有元数据';
	    }
	} else {
	    $respCode = DATA_PARAMS_ERROR;
	    $respDesc = '参数错误';
	}

	$returnValue = array('RespCode' => $respCode, 'RespDesc' => $respDesc, 'PlatFormInfo' => $thePlatForm, 'AttributeItem' => $theAttribute, 'Paging' => $paging);
	return $this->_returnData($returnValue);
    }

    /**
     * getCodeRates
     * @param string $params
     * @return string
     */
    function getCodeRates($params) {
	$respCode = $respDesc = '';
	$fileAliasData = array();
	if ($params) {
//参数解析
	    $theParams = $this->_resolutionParams($params);
	    $taskID = (int) $theParams['TaskID'];

	    $specialTaskInfo = new AuditTaskSpecialInfo();
	    $codeRates = $specialTaskInfo->getCodeRatesWithID($taskID);
	    if ($codeRates) {
		$respCode = DATA_SUCCESS;
		$respDesc = '请求执行成功';

		$fileAliasData = $this->_dataTranscode($codeRates);
	    } else {
		$respCode = DATA_NOT_DATA;
		$respDesc = '没有对应数据';
	    }
	} else {
	    $respCode = DATA_PARAMS_ERROR;
	    $respDesc = '参数错误';
	}
	$returnValue = array('RespCode' => $respCode, 'RespDesc' => $respDesc, 'TheFileAlias' => $fileAliasData);
	return $this->_returnData($returnValue);
    }

    /**
     * getTaskFiles
     * @param string $params
     * @return string
     */
    function getTaskFiles($params) {
	$respCode = $respDesc = '';
	$taskFileData = array();
	if ($params) {
//参数解析
	    $theParams = $this->_resolutionParams($params);

	    $specialTaskInfo = new AuditTaskSpecialInfo();
	    $taskFiles = $specialTaskInfo->getTaskFilesWithID($theParams);
	    if ($taskFiles) {
		$respCode = DATA_SUCCESS;
		$respDesc = '请求执行成功';

		$taskFileData = $this->_dataTranscode($taskFiles);
	    } else {
		$respCode = DATA_NOT_DATA;
		$respDesc = '没有对应数据';
	    }
	} else {
	    $respCode = DATA_PARAMS_ERROR;
	    $respDesc = '参数错误';
	}

	$returnValue = array('RespCode' => $respCode, 'RespDesc' => $respDesc, 'TaskFile' => $taskFileData);
	return $this->_returnData($returnValue);
    }

    /**
     * updateTask
     * @param string $params
     * @return string
     */
    function updateTask($params) {
	$respCode = $respDesc = '';
	if ($params) {
//参数解析
	    $theParams = $this->_resolutionParams($params);

	    $taskUpdateOperation = new AuditTaskUpdateOperation();
	    $updateState = $taskUpdateOperation->updateTaskWithInfo($theParams);

	    if ($updateState) {
		$respCode = DATA_SUCCESS;
		$respDesc = '请求执行成功';
	    } else {
		$respCode = DATA_FAILD;
		$respDesc = '请求执行失败';
	    }
	} else {
	    $respCode = DATA_PARAMS_ERROR;
	    $respDesc = '参数错误';
	}

	$returnValue = array('RespCode' => $respCode, 'RespDesc' => $respDesc);
	return $this->_returnData($returnValue);
    }

    /**
     * refreshTaskState
     * @param string $params
     * @return string
     */
    function refreshTaskState($params) {
	$respCode = $respDesc = '';
	$refreshState = true;
	if ($params) {
//参数解析
	    $theParams = $this->_resolutionParams($params);

	    $taskSpecialOperation = new AuditTaskSpecialOperation();
	    $refreshState = $taskSpecialOperation->refreshTaskStateWithInfo($theParams);

	    if ($refreshState) {
		$respCode = DATA_SUCCESS;
		$respDesc = '请求执行成功';
	    } else {
		$respCode = DATA_FAILD;
		$respDesc = '请求执行失败';
	    }
	} else {
	    $respCode = DATA_PARAMS_ERROR;
	    $respDesc = '参数错误';
	}

	$returnValue = array('RespCode' => $respCode, 'RespDesc' => $respDesc);
	return $this->_returnData($returnValue);
    }

    /**
     * refreshLockInfo
     * @param string $params
     * @return string
     */
    function refreshLockInfo($params) {
	$respCode = $respDesc = '';
	$refreshState = true;
	if ($params) {
//参数解析
	    $theParams = $this->_resolutionParams($params);

	    $taskSpecialOperation = new AuditTaskSpecialOperation();
	    $refreshState = $taskSpecialOperation->refreshLockInfoWithInfo($theParams);

	    if ($refreshState) {
		$respCode = DATA_SUCCESS;
		$respDesc = '请求执行成功';
	    } else {
		$respCode = DATA_FAILD;
		$respDesc = '请求执行失败';
	    }
	} else {
	    $respCode = DATA_PARAMS_ERROR;
	    $respDesc = '参数错误';
	}

	$returnValue = array('RespCode' => $respCode, 'RespDesc' => $respDesc);
	return $this->_returnData($returnValue);
    }

    /**
     * setWorkFlowStep
     * @param string $params
     * @return string
     */
    function setWorkFlowStep($params) {
	$respCode = $respDesc = '';
	if ($params) {
//参数解析
	    $theParams = $this->_resolutionParams($params);

	    $taskSpecialOperation = new AuditTaskSpecialOperation();
	    $wfsState = $taskSpecialOperation->setWorkFlowStepWithInfo($theParams);

	    if ($wfsState) {
		$respCode = DATA_SUCCESS;
		$respDesc = '请求执行成功';
	    } else {
		$respCode = DATA_FAILD;
		$respDesc = '请求执行失败';
	    }
	} else {
	    $respCode = DATA_PARAMS_ERROR;
	    $respDesc = '参数错误';
	}
	$returnValue = array('RespCode' => $respCode, 'RespDesc' => $respDesc);
	return $this->_returnData($returnValue);
    }

    /* 统计接口 */

    /**
     * getTaskPgmLengthAccount
     * @param string $params
     * @return string
     */
    function getTaskAndFileAccount($params) {
	$respCode = $respDesc = '';
	$countData = array();
	if ($params) {
//参数解析
	    $theParams = $this->_resolutionParams($params);

	    $accountOperation = new AuditAccountOperation();
	    $taskPgmLengthData = $accountOperation->getTaskPgmLengthAccountWith($theParams);
	    if ($taskPgmLengthData) {
		$respCode = DATA_SUCCESS;
		$respDesc = '请求执行成功';

		$countData = $taskPgmLengthData;
	    } else {
		$respCode = DATA_FAILD;
		$respDesc = '无统计数据';
	    }
	} else {
	    $respCode = DATA_PARAMS_ERROR;
	    $respDesc = '参数错误';
	}
	$returnValue = array('RespCode' => $respCode, 'RespDesc' => $respDesc, 'CountData' => $countData);
	return $this->_returnData($returnValue);
    }

    /**
     * getTaskStateAccount
     * @param string $params
     * @return string
     */
    function getTaskStateAccount($params) {
	$respCode = $respDesc = '';
	$countData = array();
	if ($params) {
//参数解析
	    $theParams = $this->_resolutionParams($params);

	    $accountOperation = new AuditAccountOperation();
	    $stateData = $accountOperation->getTaskStateAccountWith($theParams);
	    if ($stateData) {
		$respCode = DATA_SUCCESS;
		$respDesc = '请求执行成功';

		$countData = $stateData;
	    } else {
		$respCode = DATA_FAILD;
		$respDesc = '无统计数据';
	    }
	} else {
	    $respCode = DATA_PARAMS_ERROR;
	    $respDesc = '参数错误';
	}
	$returnValue = array('RespCode' => $respCode, 'RespDesc' => $respDesc, 'CountData' => $countData);
	return $this->_returnData($returnValue);
    }

    /**
     * getTaskProcessingAccount
     * @param string $params
     * @return string
     */
    function getTaskProcessingAccount($params) {
	$respCode = $respDesc = '';
	$countData = array();
	if ($params) {
//参数解析
	    $theParams = $this->_resolutionParams($params);

	    $accountOperation = new AuditAccountOperation();
	    $processData = $accountOperation->getTaskProcessingAccountWith($theParams);
	    if ($processData) {
		$respCode = USER_SUCCESS;
		$respDesc = '请求执行成功';

		$countData = $processData;
	    } else {
		$respCode = DATA_FAILD;
		$respDesc = '无统计数据';
	    }
	} else {
	    $respCode = DATA_PARAMS_ERROR;
	    $respDesc = '参数错误';
	}

	$returnValue = array('RespCode' => $respCode, 'RespDesc' => $respDesc, 'CountData' => $countData);
	return $this->_returnData($returnValue);
    }

    /**
     * getWorkloadAccount
     * @param string $params
     * @return string
     */
    function getWorkloadAccount($params) {
	$respCode = $respDesc = '';
	$paging = array();
	$countData = array();
	if ($params) {
//参数解析
	    $theParams = $this->_resolutionParams($params);

	    $accountOperation = new AuditAccountOperation();
	    $accountData = $accountOperation->getWorkloadAccountWithCondition($theParams);
	    if ($accountData) {
		$respCode = DATA_SUCCESS;
		$respDesc = '请求执行成功';

		if (DB_DRIVER == 'db2') {
		    $accountData = $this->_dataTranscode($accountData);
		}
		$paging = $accountData['Paging'];
		$countData = $accountData['CountData'];
	    } else {
		$respCode = DATA_FAILD;
		$respDesc = '无统计数据';
	    }
	} else {
	    $respCode = DATA_PARAMS_ERROR;
	    $respDesc = '参数错误';
	}

	$returnValue = array('RespCode' => $respCode, 'RespDesc' => $respDesc, 'Paging' => $paging, 'CountData' => $countData);
	return $this->_returnData($returnValue);
    }

    function assoc_unique($arr, $key) {
	$rAr = array();
	for ($i = 0; $i < count($arr); $i++) {
	    if (!isset($rAr[$arr[$i][$key]])) {
		$rAr[$arr[$i][$key]] = $arr[$i];
	    }
	}
	return array_values($rAr);
    }

}