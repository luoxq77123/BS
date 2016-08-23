<?php
App::import('Model', 'Content');
App::import('Vendor', 'NumericArray', array('file' => 'someoperation' . DS . 'class.numeric_array.php'));
App::import('Vendor', 'ChangeEncode', array('file' => 'someoperation' . DS . 'class.change_encode.php'));
App::import('Vendor', 'ConvertPopedom', array('file' => 'someoperation' . DS . 'class.convert_popedom.php'));
class AuditTaskOperation extends Object {

	public $content;

	public function __construct(){
		$this->content = new Content();
	}

	/**
	 *
	 * 获取任务列表
	 * @param array $theParams
	 */
	public function getTaskListWithConditon($theParams){
		$userID = (integer)$theParams['UserID'];
		$columnField = $theParams['UserColumn'];

		$page = (integer)$theParams['CurrentPage'];
		$limit = (integer)$theParams['PageSize'];

		if (empty($userID)){
			$this->log('获取任务列表：用户ID为空');
			return false;
		}
			
		$fieldArray = array('taskid','groupid','tasktype','pgmguid','pgmname','pgmlength','picpath','pgmcolumnid','creatorid','creatorname','taskcreatedate','auditorid','auditorname','taskauditdate','contentauditstate','contentauditnote','techauditinfo','lockerid','lockstate','locktime','workflowid');
		if (isset($theParams['Fields']) && $theParams['Fields']){
			$fieldArray = NumericArray::toNumericArray($theParams['Fields']);
		}
		if (IS_CNTV){
			$fieldArray = array_merge($fieldArray,array('channelname','columnname'));
		}

		$order = 'taskcreatedate DESC';
		$getCondition = $this->_getTaskCondition($theParams);
		$conditionArray = $getCondition['theCondition'];
		$theState = (int)$getCondition['TmpState'];
		if ($theState==PASS_AUDIT_TASK_STATE || $theState==RETURN_AUDIT_TASK_STATE){
			$order = 'taskauditdate DESC';
		}
		$conditionArray = ChangeEncode::changeEncodeFromUTF8($conditionArray);
		$results = $this->content->find('all',array(
		                               'conditions'=>$conditionArray,
		                               'fields'=>$fieldArray,
		                               'order'=>$order,
		                               'limit'=>$limit,
		                               'page'=>$page));
		$count = $this->content->find('count',array('conditions'=>$conditionArray));
		if ($count){
			$dataValue = array();
			foreach ($results as $temp){
				$tempArray = $temp['Content'];
				$tempArray['tempauditstate'] = $theState;
				$dataValue[] = $tempArray;
			}

			//分页相关
			$pageCount = intval(ceil($count / $limit));
			$defaults = array('limit'=>$limit,'page'=>1);
			$options = array_merge(array('page'=>1,'limit'=>10), $defaults, array('page'=>$page));
			$paging = array(
				  'page'     => $page,
			      'current'	 => count($results),
			      'count'	 => $count,
			      'prevPage' => ($page > 1),
			      'nextPage' => ($count > ($page * $limit)),
			      'pageCount'=> $pageCount,
			      'defaults' => array_merge(array('limit' => 20, 'step' => 1), $defaults),
			      'options'	 => $options				
			);

			return array('Paging'=>$paging,'TaskList'=>$dataValue);
		}
		return false;
	}
	/**
	 *
	 * 获取任务数目
	 * @param array $theParams
	 */
	public function getTaskCountWithCondition($theParams){
		$userID = (integer)$theParams['UserID'];
		$columnField = $theParams['UserColumn'];

		if (empty($userID)){
			return false;
		}

		$getCondition = $this->_getTaskCondition($theParams);
		$conditionArray = $getCondition['theCondition'];

		$conditionArray = ChangeEncode::changeEncodeFromUTF8($conditionArray);
		$count = $this->content->find('count',array('conditions'=>$conditionArray));

		return $count;
	}
	/**
	 *
	 * 获取对应ID的任务
	 * @param int $taskID
	 * @param array $fields
	 */
	public function getTaskWithID($taskID,$fields=NULL){
		if (!$taskID){
			return false;
		}

		$conditionArray = array('taskid' => $taskID);
		$fieldArray = array('taskid','groupid','tasktype','pgmguid','pgmname','pgmlength','picpath','pgmcolumnid','creatorid','creatorname','taskcreatedate','auditorid','auditorname','taskauditdate','contentauditstate','contentauditnote','techauditinfo','lockerid','lockstate','locktime','auditlevel','workflowid','channelname','columnname');
		if ($fields){
			$fieldArray = NumericArray::toNumericArray($fields);
		}

		$taskInfo = $this->content->find('first',array('fields' => $fieldArray,'conditions'=>$conditionArray));
		if ($taskInfo){
			return  $taskInfo['Content'];
		}
		return false;
	}
	/**
	 *
	 * 获取多个ID或条件的任务
	 * @param array $theParams
	 */
	public function getTaskWithConditionWith($theParams){
		$conditionArray = array();
		if (isset($theParams['Condition'])){
			$condition = $theParams['Condition'];
			$conditionArray = array_merge($conditionArray,$condition);
		}

		$fieldArray = array('taskid','groupid','tasktype','pgmguid','pgmname','pgmlength','picpath','pgmcolumnid','creatorid','creatorname','taskcreatedate','auditorid','auditorname','taskauditdate','contentauditstate','contentauditnote','techauditinfo','lockerid','lockstate','locktime','auditlevel','workflowid');
		if (isset($theParams['Fields']) && $theParams['Fields']){
			$fieldArray = NumericArray::toNumericArray($theParams['Fields']);
		}

		$order = 'locktime DESC';
		$conditionArray = ChangeEncode::changeEncodeFromUTF8($conditionArray);
		$taskInfo = $this->content->find('all',array('fields'=>$fieldArray,'conditions'=>$conditionArray,'order'=>$order));
		if ($taskInfo){
			$taskIDs = array();
			$flowIDs = array();
			$taskInfos = array();
			foreach ($taskInfo as $oneTask) {
				$tmpTask = $oneTask['Content'];
				if (in_array('taskid', $fieldArray)){
					$taskIDs[] = $tmpTask['taskid'];
				}
				if (in_array('workflowid', $fieldArray)){
					$flowIDs[] = $tmpTask['workflowid'];
				}
				$taskInfos[] = $tmpTask;
			}
			return  array('TaskIDs'=>$taskIDs,'FlowIDs'=>$flowIDs,'TaskInfos'=>$taskInfos);
		}
		return false;
	}
	public function getTaskWithWorkFlowIDWith($theParams){
		$workflowID = (int)$theParams['WorkFlowID'];
		if ($workflowID){
			$conditionArray = array('workflowid'=>$workflowID,'historictaskstate'=>DEFAULT_HISTORIC_TASK_STATE);

			$fieldArray = array('taskid','tasktype','auditorid','auditorname','taskauditdate','contentauditstate','contentauditnote','auditlevel','workflowid');
			$taskInfo = $this->content->find('all',array('fields'=>$fieldArray,'conditions'=>$conditionArray));

			$taskInfos = array();
			if ($taskInfo){
				foreach ($taskInfo as $oneTask) {
					$taskInfos[] = $oneTask['Content'];
				}
			}
			return $taskInfos;
		}
		return false;
	}
	public function getTaskWithPgmGUIDWith($theParams){
		$pgmGuid= $theParams['PgmGUID'];
		if ($pgmGuid){
			$conditionArray = array('pgmguid'=>$pgmGuid,'historictaskstate'=>DEFAULT_HISTORIC_TASK_STATE);

			$fieldArray = array('taskid','tasktype','pgmguid','auditorid','auditorname','taskauditdate','contentauditstate','contentauditnote','auditlevel');
			$taskInfo = $this->content->find('all',array('fields'=>$fieldArray,'conditions'=>$conditionArray));

			$taskInfos = array();
			if ($taskInfo){
				foreach ($taskInfo as $oneTask) {
					$taskInfos[] = $oneTask['Content'];
				}
			}
			return $taskInfos;
		}
		return false;
	}

	//私有方法
	/**
	 *
	 * 获取任务查询条件
	 * @param array $theParams
	 */
	private function _getTaskCondition($theParams){
		$theParams = array_merge(array('Finding'=>array()),$theParams);

		$userID = (integer)$theParams['UserID'];
		$taskType = (integer)$theParams['TaskType'];
		$userAuditPopedomNum = (integer)$theParams['UserAuditPopedom'];

		$columnField = $theParams['UserColumn'];
		$find = $theParams['Finding'];

		$defaultState = DEFAULT_TASK_STATE;
		$defaultTaskState = DEFAULT_HISTORIC_TASK_STATE;

		$conditionArr = array(
					'tasktype' => $taskType,
					'historictaskstate' => $defaultTaskState,
					'contentauditstate' => $defaultState);
		if (!IS_THE_CUSTOM_MORE_COLUMN){
			$conditionArr = array_merge($conditionArr,array('pgmcolumnid' => $columnField));
		}

		//若存在查询条件，将其加入
		if ($find){
			$conditionArr = array_merge($conditionArr,$this->_getFindingCondition($find));
		}

		//获取用户审核级别
		$userOverTaskLevelTemp = ConvertPopedom::convertPopedomToArray($userAuditPopedomNum);
		$userTaskLevelTemp = array();
		foreach ($userOverTaskLevelTemp as $value) {
			$userTaskLevelTemp[]=$value-1;
		}
		//进行审核的串行方式判断
		$userOverTaskLevel = array();
		$userTaskLevel = array();
		if (!TASK_AUDIT_TYPE){
			$userOverTaskLevel = $userOverTaskLevelTemp;
			$userTaskLevel = $userTaskLevelTemp;
		}
		else {
			if (TASK_AUDIT_MODE == 1 && $taskType == CONTENT_TASK_TYPE){
				//进行级别处理
				foreach ($userOverTaskLevelTemp as $overValue) {
					$userOverTaskLevel[]=$overValue+MAX_TASK_AUDIT_LEVEL;
				}
				foreach ($userTaskLevelTemp as $value) {
					$userTaskLevel[]=$value+MAX_TASK_AUDIT_LEVEL;
				}
			}
			elseif (TASK_AUDIT_MODE == 2 && $taskType == TECH_TASK_TYPE){
				//进行级别处理
				foreach ($userOverTaskLevelTemp as $overValue) {
					$userOverTaskLevel[]=$overValue+MAX_TASK_AUDIT_LEVEL;
				}
				foreach ($userTaskLevelTemp as $value) {
					$userTaskLevel[]=$value+MAX_TASK_AUDIT_LEVEL;
				}
			}
			else {
				$userOverTaskLevel = $userOverTaskLevelTemp;
				$userTaskLevel = $userTaskLevelTemp;
			}
		}

		//更新查询条件
		$willGetTaskState = (int)$conditionArr['contentauditstate'];

		$currentTime = time();
		$theTime = $currentTime-LOCK_TIMEOUT;
		if ($willGetTaskState == NOT_AUDIT_TASK_STATE){
			$tmpCondition = array('auditlevel'=>$userTaskLevel);

			$conditionOne = array_merge($conditionArr,$tmpCondition);
			$conditionTwo = array_merge($conditionArr,$tmpCondition,array('contentauditstate' => PASS_AUDIT_TASK_STATE));
			$conditionArray = array('or'=>array($conditionOne,$conditionTwo));
		}
		elseif ($willGetTaskState == SELECTED_TASK_STATE){
			$tmpCondition = array('auditlevel'=>$userTaskLevel);
			$conditionArray = array_merge($conditionArr,$tmpCondition);
		}
		elseif ($willGetTaskState == IS_AUDITING_TASK_STATE){
			$tmpCondition = array('auditlevel'=>$userTaskLevel);
			$conditionArray = array_merge($conditionArr,$tmpCondition);
		}
		elseif ($willGetTaskState == PASS_AUDIT_TASK_STATE){
			$tmpCondition = array('auditlevel'=>$userOverTaskLevel);
			$conditionArray = array_merge($conditionArr,$tmpCondition);
		}
		elseif ($willGetTaskState == RETURN_AUDIT_TASK_STATE){
			$tmpCondition = array('auditlevel'=>$userOverTaskLevel);
			$conditionArray = array_merge($conditionArr,$tmpCondition);
		}

		return array('TmpState'=>$willGetTaskState,'theCondition'=>$conditionArray);
	}

	/**
	 *
	 * 分析查询条件数组
	 * @param array $find
	 */
	private function _getFindingCondition($find){
		if (!$find){
			return array();
		}

		$conditionArr = array();
		$betweenArr = array();
		foreach ($find as $tempKey=>$tempValue){
			if (strlen($tempValue)){
				//进行编码处理
				$theTempValue = $tempValue;

				//ContentAuditState,PgmColumnID,PgmName
				if ($tempKey=='StartTime'){
					$betweenArr['StartTime'] = $theTempValue;
				}
				elseif ($tempKey=='EndTime'){
					$betweenArr['EndTime'] = $theTempValue;
				}
				elseif ($tempKey=='PgmName'){
					$conditionArr['pgmname LIKE']='%'.trim($theTempValue).'%';
				}
				elseif ($tempKey=='CreatorName'){
					$conditionArr['creatorname LIKE']='%'.trim($theTempValue).'%';
				}
				elseif ($tempKey=='PgmColumnID'){
					$conditionArr['pgmcolumnid']=$theTempValue;
				}
				else {
					$theKey = strtolower($tempKey);
					$conditionArr[$theKey] = $theTempValue;
				}
			}
		}
		if (isset($betweenArr['StartTime'])&&isset($betweenArr['EndTime'])){
			$conditionArr['taskcreatedate BETWEEN ? AND ?'] = array($betweenArr['StartTime'],$betweenArr['EndTime']);
		}
		elseif (isset($betweenArr['StartTime'])){
			$conditionArr['taskcreatedate >='] = $betweenArr['StartTime'];
		}
		elseif (isset($betweenArr['EndTime'])){
			$conditionArr['taskcreatedate <='] = $betweenArr['EndTime'];
		}
		return $conditionArr;
	}
}