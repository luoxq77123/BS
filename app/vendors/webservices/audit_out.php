<?php
App::import('Vendor', 'AuditCommon', array('file' => 'webservices' . DS . 'AuditInterface' . DS . 'audit_common.php'));
class AuditOut extends AuditCommon{

	/**
	 * getTaskWithWorkFlowID
	 * @param string $params
	 * @return string $data
	 */
	function getTaskWithWorkFlowID($params){				
		$respCode = $respDesc = '';
		$returnData = array();
		
		$paramsT = $params->params;
		//$this->log('任务打回：[input]'.$paramsT);
		if ($paramsT){
			//参数解析
			$theParams = $this->_resolutionParams($paramsT);

			$taskOperation = new AuditTaskOperation();
			$taskInfo = $taskOperation->getTaskWithWorkFlowIDWith($theParams);
			if ($taskInfo){
				$respCode = DATA_SUCCESS;
				$respDesc = '请求执行成功';

				$returnData = $this->_dataTranscode($taskInfo);
			}
			else {
				$respCode = DATA_NOT_DATA;
				$respDesc = '没有对应数据';
			}
		}
		else {
			$respCode = DATA_PARAMS_ERROR;
			$respDesc = '参数错误';
		}

		$returnValue = array('RespCode' => $respCode,'RespDesc' => $respDesc,'TaskInfo' => $returnData);
        //$this->log('任务打回：[output]');
        //$this->log($returnValue);
		return array('data'=>$this->_returnData($returnValue));
	}	
	
	
	/**
	 * getTaskWithPgmGUID
	 * @param string $params
	 * @return string $data
	 */
	function getTaskWithPgmGUID($params){				
		$respCode = $respDesc = '';
		$returnData = array();
		//PgmGUID
		$paramsT = $params->params;
		//$this->log('任务打回：[input]'.$paramsT);
		if ($paramsT){
			//参数解析
			$theParams = $this->_resolutionParams($paramsT);

			$taskOperation = new AuditTaskOperation();
			$taskInfo = $taskOperation->getTaskWithPgmGUIDWith($theParams);
			if ($taskInfo){
				$respCode = DATA_SUCCESS;
				$respDesc = '请求执行成功';

				$returnData = $this->_dataTranscode($taskInfo);
			}
			else {
				$respCode = DATA_NOT_DATA;
				$respDesc = '没有对应数据';
			}
		}
		else {
			$respCode = DATA_PARAMS_ERROR;
			$respDesc = '参数错误';
		}

		$returnValue = array('RespCode' => $respCode,'RespDesc' => $respDesc,'TaskInfo' => $returnData);
		//$this->log('任务打回：[output]');
		//$this->log($returnValue);
		return array('data'=>$this->_returnData($returnValue));
	}
}