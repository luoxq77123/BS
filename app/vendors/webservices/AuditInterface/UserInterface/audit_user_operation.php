<?php
App::import('Model', 'Login');
App::import('Vendor', 'DbHandle', array('file' => 'webservices' . DS . 'DB_Operation' . DS  . 'class.db_handle.php'));

App::import('Vendor', 'NumericArray', array('file' => 'someoperation' . DS . 'class.numeric_array.php'));
App::import('Vendor', 'ChangeEncode', array('file' => 'someoperation' . DS . 'class.change_encode.php'));
class AuditUserOperation extends Object {
	public $userMode;

	public function __construct(){
		//
		$this->userMode = new Login();
	}

	public function getUserInfoWithLogin($account,$password){
		if (!$account){
			return false;
		}
		if ($password){
			$condition = array('loginname' => $account,'loginpassword'=>$password);
		}
		else {
			$conditionOne = array('loginname' => $account,'loginpassword' => null);
			$conditionTwo = array('loginname' => $account,'loginpassword' => '');
			$condition = array('or'=>array($conditionOne,$conditionTwo));
		}
		$condition = ChangeEncode::changeEncodeFromUTF8($condition);
		$fields = array('userid','loginname','loginpassword','username','usercode');

		$userInfo = $this->userMode->find('first',array('fields' => $fields,'conditions' => $condition));
		if ($userInfo){
			return $userInfo['Login'];
		}
		$this->log('用户登录：登录用户名或密码错误');
		return false;
	}

	public function getUserInfoWithID($userID){
		if (!$userID){
			return false;
		}
		$condition = array('userid' => $userID);
		$fields = array('userid','username','usercode');

		$userInfo = $this->userMode->find('first',array('fields' => $fields,'conditions' => $condition));
		if ($userInfo){
			return $userInfo['Login'];
		}
		$this->log('获取用户信息：获取信息失败');
		return false;
	}
	public function getUserColumnsWithID($userID){
		if (!$userID){
			return false;
		}

		$nfield = DbHandle::_changeDbFieldToChar("N.COLUMN_ID");
		$cfield = DbHandle::_changeDbFieldToChar("C.COLUMN_ID");
		//SELECT * FROM (
		$sqlStr = "SELECT COLUMN_ID,COLUMN_NAME,
                              CASE POPEDOMTYPE
                                   WHEN '".COLUMN_QUERY_ZW."' THEN '".COLUMN_QUERY."'
                                   WHEN '".COLUMN_MAINTAIN_ZW."' THEN '".COLUMN_MAINTAIN."'
                              ELSE NUll 	
                              END 
                              AS POPEDOMTYPE      
                              FROM SMM_ROLEPOPEDOM s
                              INNER JOIN SMM_COLUMN N ON ".$nfield." = S.POPEDOMNAME
                              WHERE ROLEID IN (SELECT ROLEID
                                                      FROM SMM_USER_ROLE
                                                      WHERE USERID = ".$userID.")
                                AND POPEDOMTYPE IN ('".COLUMN_MAINTAIN_ZW."','".COLUMN_QUERY_ZW."')
                       UNION
                       SELECT COLUMN_ID, COLUMN_NAME,d.POPEDOMTYPE                
                              FROM SMM_USERPOPEDOM d
                              INNER JOIN SMM_COLUMN C ON ".$cfield." = D.POPEDOMNAME
                              WHERE USERID = ".$userID."
                                AND POPEDOMTYPE IN  ('".COLUMN_QUERY."','".COLUMN_MAINTAIN."')			
                       ";
		            //)
                   //ORDER BY NLSSORT(COLUMN_NAME,'NLS_SORT=SCHINESE_PINYIN_M')

		$sqlStr = DbHandle::_getOracleOrderSql($sqlStr);
		$theResult = $this->userMode->newFind(ChangeEncode::changeEncodeFromUTF8($sqlStr));
		if (!$theResult){
			$this->log('获取用户栏目：获取栏目失败');
		}
		return $theResult;
	}
	public function getUserPopedomWithID($userID){
		if (!$userID){
			return false;
		}

		$contentPopedom = NOT_AUDIT_POPEDOM;
		$techPopedom = NOT_AUDIT_POPEDOM;
		$auditPopedom = NOT_AUDIT_POPEDOM;
		if (!TASK_AUDIT_TYPE){
			$sqlStrAudit = "SELECT d.USERID,d.POPEDOMTYPE,d.POPEDOMNAME
                              FROM SMM_USERPOPEDOM d
                              WHERE d.USERID = ".$userID."
                                AND d.POPEDOMNAME LIKE '".USER_AUDIT_AUDIT."%'                  
                       UNION
                       SELECT n.USERID,s.POPEDOMTYPE,s.POPEDOMNAME   
                              FROM SMM_ROLEPOPEDOM s
                              INNER JOIN SMM_USER_ROLE n ON n.ROLEID = s.ROLEID
                              WHERE n.USERID = ".$userID."
                                AND s.POPEDOMNAME LIKE '".USER_AUDIT_AUDIT."%'";
			$theAuditResult = $this->userMode->newFind(ChangeEncode::changeEncodeFromUTF8($sqlStrAudit));
			if ($theAuditResult){
				foreach ($theAuditResult as $popedomInfoAudit) {
					$tmpPopedomName = $popedomInfoAudit['POPEDOMNAME'];
					$tmpPopedom = (int)trim(str_replace(USER_AUDIT_AUDIT, '', $tmpPopedomName));
					$thePopedomNum = pow(2, $tmpPopedom);
					$auditPopedom += $thePopedomNum;
				}
				return array('AuditPopedom'=> $auditPopedom,'ContentPopedom'=> $contentPopedom,'TechPopedom'=>$techPopedom);
			}
		}
		else {
			$sqlStrContent = "SELECT d.USERID,d.POPEDOMTYPE,d.POPEDOMNAME
                              FROM SMM_USERPOPEDOM d
                              WHERE d.USERID = ".$userID."
                                AND d.POPEDOMNAME LIKE '".USER_CONTENT_AUDIT."%'                  
                       UNION
                       SELECT n.USERID,s.POPEDOMTYPE,s.POPEDOMNAME   
                              FROM SMM_ROLEPOPEDOM s
                              INNER JOIN SMM_USER_ROLE n ON n.ROLEID = s.ROLEID
                              WHERE n.USERID = ".$userID."
                                AND s.POPEDOMNAME LIKE '".USER_CONTENT_AUDIT."%'";
			$sqlStrTech = "SELECT d.USERID,d.POPEDOMTYPE,d.POPEDOMNAME
                              FROM SMM_USERPOPEDOM d
                              WHERE d.USERID = ".$userID."
                                AND  d.POPEDOMNAME LIKE '".USER_TECH_AUDIT."%'
                       UNION
                       SELECT n.USERID,s.POPEDOMTYPE,s.POPEDOMNAME   
                              FROM SMM_ROLEPOPEDOM s
                              INNER JOIN SMM_USER_ROLE n ON n.ROLEID = s.ROLEID
                              WHERE n.USERID = ".$userID."
                                AND s.POPEDOMNAME LIKE '".USER_TECH_AUDIT."%'";		

			$theContentResult = $this->userMode->newFind(ChangeEncode::changeEncodeFromUTF8($sqlStrContent));
			if ($theContentResult){
				foreach ($theContentResult as $popedomInfoContent) {
					$tmpPopedomName = $popedomInfoContent['POPEDOMNAME'];
					$tmpPopedom = (int)trim(str_replace(USER_CONTENT_AUDIT, '', $tmpPopedomName));
					$thePopedomNum = pow(2, $tmpPopedom);
					$contentPopedom += $thePopedomNum;
				}
			}
			
			$theTechResult = $this->userMode->newFind(ChangeEncode::changeEncodeFromUTF8($sqlStrTech));
			if ($theTechResult){
				foreach ($theTechResult as $popedomInfoTech) {
					$tmpPopedomNameT = $popedomInfoTech['POPEDOMNAME'];
					$tmpPopedomT = (int)trim(str_replace(USER_TECH_AUDIT, '', $tmpPopedomNameT));
					$thePopedomNumT = pow(2, $tmpPopedomT);
					$techPopedom += $thePopedomNumT;
				}
			}
			return array('AuditPopedom'=> $auditPopedom,'ContentPopedom'=> $contentPopedom,'TechPopedom'=>$techPopedom);
		}
		$this->log('获取用户权限：获取权限失败');
		return false;
	}
	public function getUserAccountPopedomWithID($userID){
		if (!$userID){
			return false;
		}

		$sqlStr = "SELECT d.USERID,d.POPEDOMTYPE,d.POPEDOMNAME
                              FROM SMM_USERPOPEDOM d
                              WHERE d.USERID = ".$userID."
                                AND d.POPEDOMNAME IN ('".USER_ACCOUNT_POPEDOM."')
                       UNION
                       SELECT n.USERID,s.POPEDOMTYPE,s.POPEDOMNAME   
                              FROM SMM_ROLEPOPEDOM s
                              INNER JOIN SMM_USER_ROLE n ON n.ROLEID = s.ROLEID
                              WHERE n.USERID = ".$userID."
                                AND s.POPEDOMNAME IN ('".USER_ACCOUNT_POPEDOM."')";		

		$popedoms = $this->userMode->newFind(ChangeEncode::changeEncodeFromUTF8($sqlStr));
		
		
		$popedom = ONLY_ACCOUNT_POPEDOM;
		if ($popedoms){
			$popedoms = NumericArray::toNumericArray($popedoms);
			$countPopedom = count($popedoms);

			if ($countPopedom == 1){
				$tempArray = $popedoms[0];
				if($tempArray['POPEDOMNAME'] == USER_ACCOUNT_POPEDOM){
					$popedom = ALL_ACCOUNT_POPEDOM;
				}
			}
			
		}
		return $popedom;
	}
}