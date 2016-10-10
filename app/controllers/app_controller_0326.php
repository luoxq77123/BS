<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       cake
 * @subpackage    cake.cake.libs.controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
App::import('Core', 'Xml');
App::import('Vendor', 'NumericArray', array('file' => 'someoperation' . DS . 'class.numeric_array.php'));
App::import('Vendor', 'ConvertPopedom', array('file' => 'someoperation' . DS . 'class.convert_popedom.php'));
/**
 * This is a placeholder class.
 * Create the same file in app/app_controller.php
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package       cake
 * @subpackage    cake.cake.libs.controller
 * @link http://book.cakephp.org/view/957/The-App-Controller
 */
class AppController extends Controller {

	public $components = array('Cookie', 'Session', 'RequestHandler','XmlArray');	//定义默认引用的组件
	public $helpers = array('Html', 'Form', 'Paginator', 'Js', 'Session','ViewOperation');
	public $layout = 'MLY';
	public $uses = array('Relationship');
	/**
	 *
	 * 一系列用户数据
	 * @var unknown_type
	 */
	public $userID;
	public $userName;
	public $userCode;
	public $userType;

	public $userAcountPopedom;
	public $userAuditAuditPopedom;
	public $userContentAuditPopedom;
	public $userTechAuditPopedom;

	public $userColumn;


	//主题使用
	public $view = 'Theme';
	public function beforeRender(){
		$this->theme = AUDIT_THEME_NAME;
	}
	public function beforeFilter(){
		/**
		 * 配置Cookie相关属性
		 */
		$theUserColumn = $this->Session->read('AuditUserColumn');

		$this->Cookie->name = COOKIE_NAME;

		$this->userID = $this->Cookie->read('AuditUserID');//
		$this->userName = $this->Cookie->read('AuditUserName');//
		$this->userCode = $this->Cookie->read('AuditUserCode');//
		$this->userType = $this->Cookie->read('AuditUserType');

		$this->userAcountPopedom = $this->Cookie->read('AuditUserAccountPopedom');

		$this->userAuditAuditPopedom = $this->Cookie->read('AuditUserAuditAuditPopedom');
		$this->userContentAuditPopedom = $this->Cookie->read('AuditUserContentAuditPopedom');
		$this->userTechAuditPopedom = $this->Cookie->read('AuditUserTechAuditPopedom');

		$userInfo = array(
		            'UserID' => $this->userID,
		            'UserName' => $this->userName,
		            'UserCode' => $this->userCode,		              
		            'UserType' => $this->userType,		            
			        'UserColumn' => $theUserColumn		
		);
		if (!IS_THE_CUSTOM_MORE_COLUMN){
			if ($theUserColumn){
				$this->userColumn = array_keys($theUserColumn);
			}
		}
		else {
			$this->userColumn = array();
		}
		//add 1018 全局控制是否显示关联操作
		$user_relation_per = false;
		if($this->userID){
		    $user_relation_per = (bool)$this->role_validate($this->userID);
		}
		$this->set(compact('userInfo','user_relation_per'));
	}
	public function _setSessionTime($onlinetime){
		$allowController = array('contents');
		$allowAction = array('auditList');
		if (in_array($this->params['controller'], $allowController) || in_array($this->params['action'], $allowAction)) {
			$this->log('insession');
			//if (isset($_SESSION)){
			//$onlinetime = $_SESSION['Config']['time'];
			$this->log('time:'.$onlinetime);
			$this->_sessionTime($onlinetime, 30);
			//}
		}
	}
	public function _sessionTime($onlinetime,$interval){
		$new_time = mktime();
		$this->log('time:'.$new_time.';'.$onlinetime);
		$thetime = $new_time-$onlinetime;
		if($thetime > $interval){
			$this->log('indestroy');
			$this->Cookie->destroy();
		}else{
			//$_SESSION['Config']['time'] = mktime();
		}
	}


	/**
	 * 操作日志记录
	 */
	public function _setLogs(){

	}

	public function _toNumericArray($data){
		return NumericArray::toNumericArray($data);
	}
	public function _toPopedomArray($data){
		return ConvertPopedom::convertPopedomToArray($data);
	}
	/**
	 * 转换成数组
	 */
	public function _toArray($param){
		$xml = new Xml($param,array('format' => 'tags'));
		$array = $xml->toArray();
		return $array;
	}
	/**
	 * 转换成Xml字符串
	 */
	public function _toXmlStr($param){
		$xml = new Xml($param,array('format' => 'tags'));
		$xmlString = $xml->toString();
		return $xmlString;
	}

	/**
	 * 操作权限
	 */
	public function _isAllow($cookieID){
		$allowController = array('users','workflows');
		$allowAction = array('login','loginModel','logout','workflowAudit');
		if (in_array($this->params['controller'], $allowController) && in_array($this->params['action'], $allowAction)) {

		}
		else {
			if (empty($cookieID)) {
				$this->redirect(array('controller'=>'users','action'=>'login'));
			}
		}
	}
	/**
	 *
	 * 获取待审核任务条数
	 */
	public function _getNotAuditListCount(){
		//用户待审核任务数目获取
		$countParamArray = array('Request'=>array('UserID'=>$this->userID,'UserAuditPopedom'=>$this->userAuditPopedom,'UserColumn'=>$this->userColumn,'TaskType'=>$this->userType));
		$countParam = $this->_toXmlStr($countParamArray);

		$countInfos = $this->clientSoap->getTaskCount($countParam);
		$countArray = $this->XmlArray->xml2array($countInfos);

		$countData = $countArray['Response']['AllCount'];
		$allCount = 0;
		if ($countData){
			$allCount = (int)$countData;
		}
		return $allCount;
	}
    /**
     * 是否AJAX请求
     * @access protected
     * @return bool
     */
    protected function isAjax() {
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) ) {
            if('xmlhttprequest' == strtolower($_SERVER['HTTP_X_REQUESTED_WITH']))
                return true;
        }
        return false;
    }
    
    //角色权限判断
    function role_validate($user_id,$type = USER_RELATION_PER) {
	$sqlStrAudit = "SELECT d.USERID,d.POPEDOMTYPE,d.POPEDOMNAME
                              FROM SMM_USERPOPEDOM d
                              WHERE d.USERID = " . $user_id . "
                                AND d.POPEDOMNAME LIKE '" . $type . "%'                  
                       UNION
                       SELECT n.USERID,s.POPEDOMTYPE,s.POPEDOMNAME  
                              FROM SMM_ROLEPOPEDOM s
                              INNER JOIN SMM_USER_ROLE n ON n.ROLEID = s.ROLEID
                              WHERE n.USERID = " . $user_id . "
                                AND s.POPEDOMNAME LIKE '" . $type . "%'";
	return $this->Relationship->newFind($sqlStrAudit);
    }
        //转义XML特殊字符
    function format_sepcial_chars($str) {
//	$str = str_replace('&', '&amp;', $str);
	$str = str_replace('<', '&lt;', $str);
	$str = str_replace('>', '&gt;', $str);
	$str = str_replace("'", '&apos;', $str);
	$str = str_replace('"', '&quot;', $str);
	return $str;
    }
}