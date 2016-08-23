<?php
App::import('Model', 'Content');
App::import('Model', 'TaskAuditStep');
App::import('Vendor', 'DbHandle', array('file' => 'webservices' . DS . 'DB_Operation' . DS  . 'class.db_handle.php'));

App::import('Vendor', 'ChangeEncode', array('file' => 'someoperation' . DS . 'class.change_encode.php'));
App::import('Vendor', 'ConvertPopedom', array('file' => 'someoperation' . DS . 'class.convert_popedom.php'));
class AuditAccountOperation extends Object{
	public $content;
	public $auditStep;

	public function __construct(){
		$this->content = new Content();
		$this->auditStep = new TaskAuditStep();
	}
	/**
	 *
	 * 获取节目时长
	 * @param array $theParams
	 */
	public function getTaskPgmLengthAccountWith($theParams){
		$taskType = (int)$theParams['TaskType'];
		$beginTimet = $theParams['BeginTime'];
		$endTimet = $theParams['EndTime'];

		$format = DbHandle::_getSearchDateFormat();
		$beginTime = date($format,strtotime($beginTimet));
		$endTime = date($format,strtotime("+1 day",strtotime($endTimet)));

		$sqlStr2 = "select to_char(d.taskauditdate, 'YYYY/MM/DD') as thedate, sum(d.pgmlength) as thecount
                           from et_nmpgmauditlist d 
                           where d.tasktype=".$taskType." and 
                                 d.taskauditdate is not null and
                                 d.taskauditdate between '".$beginTime."' and '".$endTime."'
                           group by to_char(d.taskauditdate, 'YYYY/MM/DD')
                           order by to_char(d.taskauditdate, 'YYYY/MM/DD') asc";
		$theResult = $this->content->newFind(ChangeEncode::changeEncodeFromUTF8($sqlStr2));

		$countData = array();
		if($theResult){
			foreach ($theResult as $tmpArray) {
				$tmpvalue = (int)$tmpArray['THECOUNT'];
				$value = floor($tmpvalue/TASK_FRAME_NUM);
				$tmpArray['THECOUNT'] = $value;

				$countData[] = $tmpArray;
			}

			return $countData;
		}
		$this->log('统计：获取任务时长失败');
		return false;
	}
	/**
	 *
	 * 获取节目状态
	 * @param array $theParams
	 */
	public function getTaskStateAccountWith($theParams){
		$taskType = (int)$theParams['TaskType'];
		$beginTimet = $theParams['BeginTime'];
		$endTimet = $theParams['EndTime'];
		
		$format = DbHandle::_getSearchDateFormat();
		$beginTime = date($format,strtotime($beginTimet));
		$endTime = date($format,strtotime("+1 day",strtotime($endTimet)));
		//四种状态统计
//		$theAllCount = $this->_getCountFromDbWithAllState($taskType, $beginTime, $endTime);
		$thePassCount = $this->_getCountFromDbWithPassState($taskType, $beginTime, $endTime);
		$theReturnCount = $this->_getCountFromDbWithReturnState($taskType, $beginTime, $endTime);
		$theNoAuditCount = $this->_getCountFromDbWithNotAuditState($taskType, $beginTime, $endTime);
        $theSelectedCount = $this->_getCountFromDbWithSelectedState($taskType, $beginTime, $endTime);
		$theAuditingCount = $this->_getCountFromDbWithAuditingdState($taskType, $beginTime, $endTime);

//		$theAuditingCount = $theAllCount - $thePassCount - $theReturnCount;
        $theAllCount = $thePassCount + $theReturnCount + $theSelectedCount + $theAuditingCount;
		if ($theNoAuditCount){
			foreach ($theNoAuditCount as $key=>$value) {
//				$theAuditingCount = $theAuditingCount - $value;
                $theAllCount = $theAllCount + $value;
			}
		}
		return array(
		     'AllCount'=>$theAllCount,
			 'PassCount'=>$thePassCount,
			 'ReturnCount'=>$theReturnCount,
			 'NoAuditCount'=>$theNoAuditCount,
             'SelectedCount'=>$theSelectedCount,
			 'AuditingCount'=>$theAuditingCount);
	}
	/**
	 *
	 * 获取任务处理量
	 * @param array $theParams
	 */
	public function getTaskProcessingAccountWith($theParams){
		$taskType = (int)$theParams['TaskType'];
		$theMode = (int)$theParams['TimeDateMode'];
		$beginTimet = $theParams['BeginTime'];
		$endTimet = $theParams['EndTime'];
		
		$format = DbHandle::_getSearchDateFormat();
		$beginTime = date($format,strtotime($beginTimet));
		$endTime = date($format,strtotime("+1 day",strtotime($endTimet)));

		//获取数据
		$countData = array();
		if ($theMode==TIME_MODE_DAY){
			$countData = $this->_getDayTaskAccount($beginTime,$endTime,$taskType);
		}
		elseif ($theMode==TIME_MODE_MONTH){
			$countData = $this->_getMonthTaskAccount($beginTime, $endTime,$taskType);
		}
		elseif ($theMode==TIME_MODE_YEAR){
			$countData = $this->_getYearTaskAccount($beginTime, $endTime,$taskType);
		}
		return $countData;
	}
	/**
	 *
	 * 获取工作量
	 * @param array $theParams
	 */
	public function getWorkloadAccountWithCondition($theParams){
		$userID = (int)$theParams['UserID'];
		$taskType = (int)$theParams['TaskType'];
		$userCountPeodom = (int)$theParams['UserCountPeodom'];

		$page = (int)$theParams['CurrentPage'];
		$limit = (int)$theParams['PageSize'];
		$search = $theParams['Search'];

		$taskCondition = array('tasktype'=>$taskType);
		//若存在查询条件，将其加入
		if ($search){
			$taskCondition = array_merge($taskCondition,$this->_getSearchCondition($search));
		}

		//判断用户统计权限
		if ($userCountPeodom == ONLY_ACCOUNT_POPEDOM){//临时值:1为查看自己，2为查看所有
			$taskCondition = array_merge($taskCondition,array('auditorid'=>$userID));
		}
        
		$taskCondition = ChangeEncode::changeEncodeFromUTF8($taskCondition);
		$param = array(
			'conditions'=> $taskCondition,
			'fields'    => array('auditorid','auditorname'),
			'group'     => array('auditorid','auditorname'),
			'order'     => 'auditorid ASC');	

		//是否获取的是所有的工作量数据
		if ($limit != WORKLOAD_ACCOUNT_ALL){
			//获取可查看的人员总数
			$allAuditors = $this->auditStep->find('all',$param);
			$allCount = count($allAuditors);
			$param = array_merge($param,array('limit'=> $limit,'page'=> $page));
		}
		$theAuditors = $this->auditStep->find('all',$param);

		if (!$theAuditors){
			return false;
		}
		//循环查询
		$countData = array();
		foreach ($theAuditors as $tempAuditor) {
			$aAuditor = $tempAuditor['TaskAuditStep'];
			$aAuditorID = (int)$aAuditor['auditorid'];
			$aAuditorName = $aAuditor['auditorname'];

			$aCondition = array_merge($taskCondition,array('auditorid'=>$aAuditorID));

			//获取所有处理任务数
			//$aCountAll = $this->auditStep->find('count',array('conditions'=>$aCondition));

			$aCondition['contentauditstate'] = PASS_AUDIT_TASK_STATE;
			$aCountPass = $this->auditStep->find('count',array('conditions'=>$aCondition));

			$aCondition['contentauditstate'] = RETURN_AUDIT_TASK_STATE;
			$aCountNotPass = $this->auditStep->find('count',array('conditions'=>$aCondition));

			$aCountAll = $aCountPass + $aCountNotPass;
			//单个人数据
			$acountData = array(
					      'AuditID' => $aAuditorID,
				          'AuditName' => $aAuditorName,
				          'CountAll' => $aCountAll,
				          'CountPass' => $aCountPass,
				          'CountNotPass' => $aCountNotPass);
			$countData[] = $acountData;
		}

		$paging = array();
		if ($limit != WORKLOAD_ACCOUNT_ALL){
			//数据分页相关
			$pageCount = intval(ceil($allCount / $limit));
			$defaults = array('limit'=>$limit,'page'=>1);
			$options = array_merge(array('page'=>1, 'limit'=>10), $defaults, array('page'=>$page));
			$paging = array(
				      'page'     => $page,
			          'current'	 => count($countData),
			          'count'	 => $allCount,
			          'prevPage' => ($page > 1),
			          'nextPage' => ($allCount > ($page * $limit)),
			          'pageCount'=> $pageCount,
			          'defaults' => array_merge(array('limit' =>10, 'step' => 1), $defaults),
			          'options'	 => $options);					                           
		}
		return array('Paging'=>$paging,'CountData'=>$countData);
	}

	//根据条件获取各状态数目
	private function _getCountFromDbWithAllState($taskType,$beginTime,$endTime){
		$pcLevel = "";
		if (!TASK_AUDIT_TYPE){

		}
		else {
			if (TASK_AUDIT_MODE == 1){
				if($taskType == CONTENT_TASK_TYPE){
					$pcLevel = $pcLevel."t.auditlevel <>0 and ";
				}
			}
			elseif(TASK_AUDIT_MODE == 2){
				if($taskType == TECH_TASK_TYPE){
					$pcLevel = $pcLevel."t.auditlevel <>0 and ";
				}
			}
		}
		
		$sqlStr = "select count(taskid) as thecount
		                  from et_nmpgmauditlist t 
		                  where t.tasktype=".$taskType." and ".$pcLevel."	                   
		                        (t.taskauditdate between '".$beginTime."' and '".$endTime."' or
		                         t.taskauditdate is null
		                        )";
		$theResult = $this->content->newFind(ChangeEncode::changeEncodeFromUTF8($sqlStr));
		if ($theResult){
			$CountArray = $theResult[0];
			return $CountArray['THECOUNT'];
		}
		$this->log('统计：获取节目状态失败');
		return 0;
	}
	private function _getCountFromDbWithPassState($taskType,$beginTime,$endTime){
		if (!TASK_AUDIT_TYPE){
			$level = TASK_AUDIT_AUDIT_LEVEL;
		}
		else {
			if($taskType == CONTENT_TASK_TYPE){
				$level = TASK_CONTENT_AUDIT_LEVEL;
				if (TASK_AUDIT_MODE == 1){
					$level = $level + MAX_TASK_AUDIT_LEVEL;
				}
			}
			else{
				$level = TASK_TECH_AUDIT_LEVEL;
				if (TASK_AUDIT_MODE == 2){
					$level = $level + MAX_TASK_AUDIT_LEVEL;
				}
			}
		}
		
		$sqlStr = "select count(taskid) as thecount
		                  from et_nmpgmauditlist t 
		                  where t.tasktype=".$taskType." and
		                        t.contentauditstate=".PASS_AUDIT_TASK_STATE." and 
		                        t.auditlevel=".$level." and
		                        (t.taskauditdate between '".$beginTime."' and '".$endTime."' or
		                         t.taskauditdate is null
		                        )";
        $theResult = $this->content->newFind(ChangeEncode::changeEncodeFromUTF8($sqlStr));
		if ($theResult){
			$CountArray = $theResult[0];
			return $CountArray['THECOUNT'];
		}
		$this->log('统计：获取通过状态的任务失败');
		return 0;
	}
	private function _getCountFromDbWithReturnState($taskType,$beginTime,$endTime){
		$sqlStr = "select count(taskid) as thecount
		                  from et_nmpgmauditlist t 
		                  where t.tasktype=".$taskType." and
		                        t.contentauditstate=".RETURN_AUDIT_TASK_STATE." and
		                        (t.taskauditdate between '".$beginTime."' and '".$endTime."' or
		                         t.taskauditdate is null
		                        )";
        $theResult = $this->content->newFind(ChangeEncode::changeEncodeFromUTF8($sqlStr));
		if ($theResult){
			$CountArray = $theResult[0];
			return $CountArray['THECOUNT'];
		}
		$this->log('统计：获取打回状态的任务失败');
		return 0;
	}
	private function _getCountFromDbWithNotAuditState($taskType,$beginTime,$endTime){
		if (!TASK_AUDIT_TYPE){
			$level = TASK_AUDIT_AUDIT_LEVEL;
		}
		else {
			if($taskType == CONTENT_TASK_TYPE){
				$level = TASK_CONTENT_AUDIT_LEVEL;
			}
			else{
				$level = TASK_TECH_AUDIT_LEVEL;
			}
		}

		$countData = array();
		for($levelValue = 0;$levelValue < $level;$levelValue++){
			$tmpLevel = $levelValue;
			if (!TASK_AUDIT_TYPE){

			}
			else {
				if (TASK_AUDIT_MODE == 1 && $taskType == CONTENT_TASK_TYPE){
					$tmpLevel = $tmpLevel + MAX_TASK_AUDIT_LEVEL;
				}
				elseif (TASK_AUDIT_MODE == 2 && $taskType == TECH_TASK_TYPE){
					$tmpLevel = $tmpLevel + MAX_TASK_AUDIT_LEVEL;
				}
			}


			$currentTime = time();
			$theTime = $currentTime-LOCK_TIMEOUT;
			$sqlStr = "select count(taskid) as thecount
		                      from et_nmpgmauditlist t 
		                      where t.tasktype=".$taskType." and                      
		                            (t.taskauditdate between '".$beginTime."' and '".$endTime."' or
		                             t.taskauditdate is null
		                            ) and
		                            t.auditlevel=".$tmpLevel." and
		                            (t.contentauditstate=".NOT_AUDIT_TASK_STATE." or
		                             t.contentauditstate=".PASS_AUDIT_TASK_STATE."
		                            )";
            $theResult = $this->content->newFind(ChangeEncode::changeEncodeFromUTF8($sqlStr));

			$theLevelNum = $levelValue+1;
			$tmpKey = 'NoAudit_'.$theLevelNum;
			$tmpValue = 0;
			if ($theResult){
				$CountArray = $theResult[0];
				$tmpValue = $CountArray['THECOUNT'];
			}
			else {
				$this->log('统计：获取'.$theLevelNum.'级待审核状态的任务失败');
			}
			$countData = array_merge($countData,array($tmpKey=>$tmpValue));
		}
		return $countData;
}
	private function _getCountFromDbWithSelectedState($taskType,$beginTime,$endTime){
		$sqlStr = "select count(taskid) as thecount
		                  from et_nmpgmauditlist t 
		                  where t.tasktype=".$taskType." and
		                        t.contentauditstate=".SELECTED_TASK_STATE." and
		                        (t.taskauditdate between '".$beginTime."' and '".$endTime."' or
		                         t.taskauditdate is null
		                        )";
		$theResult = $this->content->newFind(ChangeEncode::changeEncodeFromUTF8($sqlStr));
		if ($theResult){
			$CountArray = $theResult[0];
			return $CountArray['THECOUNT'];
		}
		$this->log('统计：获取已挑选状态的任务失败');
		return 0;
	}
	private function _getCountFromDbWithAuditingdState($taskType,$beginTime,$endTime){
		$sqlStr = "select count(taskid) as thecount
		                  from et_nmpgmauditlist t 
		                  where t.tasktype=".$taskType." and
		                        t.contentauditstate=".IS_AUDITING_TASK_STATE." and
		                        (t.taskauditdate between '".$beginTime."' and '".$endTime."' or
		                         t.taskauditdate is null
		                        )";
		$theResult = $this->content->newFind(ChangeEncode::changeEncodeFromUTF8($sqlStr));
		if ($theResult){
			$CountArray = $theResult[0];
			return $CountArray['THECOUNT'];
		}
		$this->log('统计：获取审核中状态的任务失败');
		return 0;
	}


	/**
	 *
	 * 获取搜索条件
	 * @param array $search
	 */
	private function _getSearchCondition($search){
		if (!$search){
			return array();
		}

		$taskCondition = array();
		$betweenArr = array();
		foreach ($search as $tempKey=>$tempValue){
			if (strlen($tempValue)){
				//进行编码处理
				$theTempValue = $tempValue;

				if ($tempKey=='TimeStart'){
					$betweenArr['TimeStart'] = $theTempValue;
				}
				elseif ($tempKey=='TimeEnd'){
					$betweenArr['TimeEnd'] = $theTempValue;
				}
				elseif ($tempKey=='Name'){
					$taskCondition['auditorname LIKE']='%'.trim($theTempValue).'%';
				}
			}
		}
		if (isset($betweenArr['TimeStart'])&&isset($betweenArr['TimeEnd'])){
			$taskCondition['taskauditdate BETWEEN ? AND ?'] = array($betweenArr['TimeStart'],$betweenArr['TimeEnd']);
		}
		elseif (isset($betweenArr['TimeStart'])){
			$taskCondition['taskauditdate >='] = $betweenArr['TimeStart'];
		}
		elseif (isset($betweenArr['TimeEnd'])){
			$taskCondition['taskauditdate <='] = $betweenArr['TimeEnd'];
		}

		return $taskCondition;
	}
	/**
	 *
	 * 获取日任务处理量
	 * @param string $beginTime
	 * @param string $endTime
	 * @param int $taskType
	 */
	private function _getDayTaskAccount($beginTime,$endTime,$taskType){
		$sqlStr = "select to_char(t.taskauditdate, 'YYYY/MM/DD') as thedate, count(*) as thecount
                          from et_nmpgmauditlist t
                          where t.tasktype=".$taskType." and
                                t.taskauditdate is not null and
                                t.taskauditdate between '".$beginTime."' and '".$endTime."'
                          group by to_char(t.taskauditdate, 'YYYY/MM/DD')
                          order by to_char(t.taskauditdate, 'YYYY/MM/DD') asc";
		$countData = $this->content->newFind(ChangeEncode::changeEncodeFromUTF8($sqlStr));
		if (!$countData){
			$this->log('统计：获取日任务处理量失败');
		}
		return $countData;
	}
	/**
	 *
	 * 获取月任务处理量
	 * @param string $beginTime
	 * @param string $endTime
	 * @param int $taskType
	 */
	private function _getMonthTaskAccount($beginTime,$endTime,$taskType){
		$sqlStr = "select to_char(t.taskauditdate, 'YYYY') as theyear, to_char(t.taskauditdate, 'MM') as themonth,count(*) as thecount
                          from et_nmpgmauditlist t
                          where t.tasktype=".$taskType." and
                                t.taskauditdate is not null and
                                t.taskauditdate between '".$beginTime."' and '".$endTime."'
                          group by to_char(t.taskauditdate, 'YYYY'), to_char(t.taskauditdate, 'MM')
                          order by to_char(t.taskauditdate, 'YYYY') asc,to_char(t.taskauditdate, 'MM') asc";
		$countData = $this->content->newFind(ChangeEncode::changeEncodeFromUTF8($sqlStr));;
		if (!$countData){
			$this->log('统计：获取月任务处理量失败');
		}
		return $countData;
	}
	/**
	 *
	 * 获取年任务处理量
	 * @param string $beginTime
	 * @param string $endTime
	 * @param int $taskType
	 */
	private function _getYearTaskAccount($beginTime,$endTime,$taskType){
		$sqlStr = "select to_char(t.taskauditdate, 'YYYY') as theyear, count(*) as thecount
                          from et_nmpgmauditlist t
                          where t.tasktype=".$taskType." and
                                t.taskauditdate is not null and
                                t.taskauditdate between '".$beginTime."' and '".$endTime."'
                          group by to_char(t.taskauditdate, 'YYYY')
                          order by to_char(t.taskauditdate, 'YYYY') asc";
		$countData = $this->content->newFind(ChangeEncode::changeEncodeFromUTF8($sqlStr));;
		if (!$countData){
			$this->log('统计：获取年任务处理量失败');
		}
		return $countData;
	}

	/**
	 *
	 * 获取用户审核级别
	 */
	private function _getUserAuditLevel(){
		$taskType = (int)$this->curTaskInfo['tasktype'];
		$userAuditPopedomNum = (int)$this->paramTaskInfo['UserAuditPopedom'];
		//获取用户审核级别
		$userOverTaskLevel = ConvertPopedom::convertPopedomToArray($userAuditPopedomNum);
		$userTaskLevel = array();
		if (!TASK_AUDIT_TYPE){
			foreach ($userOverTaskLevel as $value) {
				$userTaskLevel[]=$value-1;
			}
		}
		else {
			if (TASK_AUDIT_MODE == 1 && $taskType == CONTENT_TASK_TYPE){
				//进行级别处理
				foreach ($userOverTaskLevel as $value) {
					$userTaskLevel[]=$value-1+MAX_TASK_AUDIT_LEVEL;
				}
			}
			elseif (TASK_AUDIT_MODE == 2 && $taskType == TECH_TASK_TYPE){
				//进行级别处理
				foreach ($userOverTaskLevel as $value) {
					$userTaskLevel[]=$value-1+MAX_TASK_AUDIT_LEVEL;
				}
			}
			else {
				foreach ($userOverTaskLevel as $value) {
					$userTaskLevel[]=$value-1;
				}
			}
		}
		return $userTaskLevel;
	}
}