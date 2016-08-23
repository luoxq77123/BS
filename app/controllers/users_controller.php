<?php
App::import('Vendor', 'Audit', array('file' => 'webservices' . DS . 'audit.php'));
class UsersController extends AppController {

	public $name = 'Users';
	public $uses = array();

	/**
	 * 用户登录
	 *
	 */
	public function login(){
		$this->layout = false;

		if ($this->data) {
			$account = $this->data['User']['name'];
			$password = $this->data['User']['password'];

			//ws客户端
//			$client = new SoapClient(WS_DOMAIN."/soapServices/wsdl/audit");
			$client = new Audit();

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
				//var_dump($userAccountPopedom);die();

				if (!TASK_AUDIT_TYPE){
					$popedomState = ($userAuditPopedom != NOT_AUDIT_POPEDOM);
				}
				else {
					$popedomState = ($userContentPopedom != NOT_AUDIT_POPEDOM || $userTechPopedom != NOT_AUDIT_POPEDOM);
				}

				if($columnField  && $popedomState){
					//将用户栏目信息写入session
					$this->Session->write('AuditUserColumn',$columnField);
					//将用户栏目信息写入cookie
					//$this->Cookie->write('UserColumn',$columnField,false);

					$cookieUserID = $userData['UserID'];
					$cookieUserName = $userData['UserName'];
					$cookieUserCode = $userData['UserCode'];
					$cookieUserAccountPopedom = $userAccountPopedom;
					//将用户信息写入cookie
					$this->Cookie->write('AuditUserID',$cookieUserID);
					$this->Cookie->write('AuditUserName',$cookieUserName);
					$this->Cookie->write('AuditUserCode',$cookieUserCode);
					$this->Cookie->write('AuditUserAccountPopedom',$cookieUserAccountPopedom);
					
					if (!TASK_AUDIT_TYPE){
						$cookieUserAuditAuditPopedom = $userAuditPopedom;
						$this->Cookie->write('AuditUserAuditAuditPopedom',$cookieUserAuditAuditPopedom);

						$cookieUserType = AUDIT_TASK_TYPE;
						$this->Cookie->write('AuditUserType',$cookieUserType);
					}
					else {
						//写入技审、内审权限
						$cookieUserContentAuditPopedom = $userContentPopedom;
						$cookieUserTechAuditPopedom = $userTechPopedom;
						$this->Cookie->write('AuditUserContentAuditPopedom',$cookieUserContentAuditPopedom);
						$this->Cookie->write('AuditUserTechAuditPopedom',$cookieUserTechAuditPopedom);

						//选择进入的方式（内审或技审）
						if ($userContentPopedom!=NOT_AUDIT_POPEDOM && $userTechPopedom!=NOT_AUDIT_POPEDOM){
							//要进行选择
							$this->redirect(array('controller' => 'users','action' => 'loginModel'));
						}
						elseif ($userContentPopedom!=NOT_AUDIT_POPEDOM && $userTechPopedom==NOT_AUDIT_POPEDOM){
							//内审
							$cookieUserType = CONTENT_TASK_TYPE;
							$this->Cookie->write('AuditUserType',$cookieUserType);
						}
						elseif ($userContentPopedom==NOT_AUDIT_POPEDOM && $userTechPopedom!=NOT_AUDIT_POPEDOM){
							//技审
							$cookieUserType = TECH_TASK_TYPE;
							$this->Cookie->write('AuditUserType',$cookieUserType);
						}
					}
						
					$this->redirect(array('controller' => 'contents','action' => 'auditList'));

				}
				else {
					//无权限进入
					$this->Session->setFlash('对不起，您没有审核权限',false);

					$this->redirect(array('controller' => 'users','action' => 'login'));
				}

			}
			elseif($userData['RespCode'] == LOGIN_NAMEORPW_ERROR){
				//用户名或密码错误
				$this->Session->setFlash('用户名或密码错误',false);

				$this->redirect(array('controller' => 'users','action' => 'login'));
			}
			else {
				$this->Session->setFlash('登录错误',false);
				$this->redirect(array('controller' => 'users','action' => 'login'));
			}
		}
	}
	/**
	 * 用户退出
	 *
	 */
	public function logout(){

		//		$this->Session->setFlash('成功退出！',false);
		//删除session
		$this->Session->delete('AuditUserColumn');
		//删cookie
		$this->Cookie->delete('AuditUserID');
		$this->Cookie->delete('AuditUserName');
		$this->Cookie->delete('AuditUserCode');
		$this->Cookie->delete('AuditUserAccountPopedom');
		$this->Cookie->delete('AuditUserType');
		$this->Cookie->delete('AuditUserAuditAuditPopedom');
		$this->Cookie->delete('AuditUserContentAuditPopedom');
		$this->Cookie->delete('AuditUserTechAuditPopedom');
		//$this->Cookie->delete('UserColumn');

		$this->Cookie->delete('AuditBatchList');

		//删除查询条件
		$this->Cookie->delete('FindingStartTime');
		$this->Cookie->delete('FindingEndTime');
		$this->Cookie->delete('FindingContentAuditState');
		$this->Cookie->delete('FindingPgmColumnID');
		$this->Cookie->delete('FindingPgmName');
		
		//
		$this->Cookie->delete('AcountFindTaskTimeStart');
		$this->Cookie->delete('AcountFindTaskTimeEnd');
		//
		$this->Cookie->delete('AcountFindName');
		$this->Cookie->delete('AcountFindTimeStart');
		$this->Cookie->delete('AcountFindTimeEnd');

		$this->redirect(array('action' => 'login'));
	}

	public function loginModel(){
		$this->layout = false;

		if ($this->data) {
			$model = $this->data['Model']['choose'];
			$chooseModel = (int)$model;
			if ($chooseModel == CONTENT_TASK_TYPE){
				$cookieUserType = CONTENT_TASK_TYPE;
			}
			else{
				$cookieUserType = TECH_TASK_TYPE;
			}
			$this->Cookie->write('AuditUserType',$cookieUserType);

			//			$this->Session->setFlash('登录成功！欢迎您',false);
			$this->redirect(array('controller' => 'contents','action' => 'auditList'));
		}
	}
}