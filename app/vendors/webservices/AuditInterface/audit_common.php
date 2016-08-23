<?php
App::import('Vendor', 'AuditUserOperation', array('file' => 'webservices' . DS . 'AuditInterface'. DS . 'UserInterface' . DS . 'audit_user_operation.php'));
App::import('Vendor', 'AuditTaskOperation', array('file' => 'webservices' . DS . 'AuditInterface'. DS . 'TaskInterface' . DS . 'audit_task_operation.php'));
App::import('Vendor', 'AuditTaskSpecialInfo', array('file' => 'webservices' . DS . 'AuditInterface'. DS . 'TaskInterface' . DS . 'audit_task_special_info.php'));
App::import('Vendor', 'AuditTaskUpdateOperation', array('file' => 'webservices' . DS . 'AuditInterface'. DS . 'TaskInterface' . DS . 'audit_task_update_operation.php'));
App::import('Vendor', 'AuditTaskSpecialOperation', array('file' => 'webservices' . DS . 'AuditInterface'. DS . 'TaskInterface' . DS . 'audit_task_special_operation.php'));
App::import('Vendor', 'AuditAccountOperation', array('file' => 'webservices' . DS . 'AuditInterface'. DS . 'AccountInterface' . DS . 'audit_account_operation.php'));

App::import('Core', 'Xml');
App::import('Vendor', 'Array2Xml2Array', array('file' => 'xmlandarray' . DS . 'class.array2xml2array.php'));
App::import('Vendor', 'ChangeEncode', array('file' => 'someoperation' . DS . 'class.change_encode.php'));
App::import('Model', 'Login');
class AuditCommon extends Object {
	
	/**
	 * _resolutionParams
	 * 传入参数解析
	 * @param string $params
	 */
	public function _resolutionParams($params){
		$theParamsArray = Array2Xml2Array::xml2array($params);
		return $theParamsArray['Request'];
	}

	/**
	 * _dataTranscode
	 * 数据编码转换
	 * @param mix $data
	 */
	public function _dataTranscode($data){
		return ChangeEncode::changeEncodeToUTF8($data);
	}

	public function _returnData($returnData){
		$returnXml = new Array2Xml2Array('Response');
		return $returnXml->array2Xml($returnData);
		//		$returnValue = new Xml(array('Response'=>$returnData),array('format' => 'tags'));
		//		return $returnValue->toString();
	}
}