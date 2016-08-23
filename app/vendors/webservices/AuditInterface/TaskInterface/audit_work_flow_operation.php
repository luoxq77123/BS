<?php
App::import('Vendor', 'NumericArray', array('file' => 'someoperation' . DS . 'class.numeric_array.php'));
class AuditWorkFlowOperation extends Object{
	public $data;

	public $workFlowID,$flag,$type,$level,$name,$typeID=null,$taskID=null;


	/**
	 *
	 * @param int $_workFlowID
	 * @param sting $_flag  更新标志值:'begin'、'exit'、'pass'、'back'。当且仅当为'pass'时，则必须设置$_typeID值，其余情况下为空
	 * @param int $_type  任务类型:0或1
	 * @param int $_level 审核级别
	 * @param sting $_name 操作人
	 * @param int $_taskID
	 * @param int $_typeID
	 */
	public function __construct($_workFlowID,$_flag,$_type,$_level,$_name,$_taskID=null,$_typeID=null){
		$this->workFlowID = $_workFlowID;
		$this->flag = $_flag;
		$this->type = (int)$_type;
		$this->level = (int)$_level;
		$this->name = $_name;
		if ($_taskID){
			$this->taskID = (int)$_taskID;
		}
		if ($_typeID){
			$this->typeID = (int)$_typeID;
		}
	}
	public function setWorkFlow(){
		//初始化值
		$this->_setData();

		//http请求接口

		//打加接口地址和更新流程接口地址不同
		if ($this->flag == 'back') {
			$url = HTTP_POST_WORK_FLOW.'/api/2/backto';
		}
		else {
			$url = HTTP_POST_WORK_FLOW.'/api/2/updateworkflow';
		}

		$httpCh = curl_init();
		curl_setopt($httpCh, CURLOPT_URL, $url);
		curl_setopt($httpCh, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($httpCh, CURLOPT_POST, 1);
		curl_setopt($httpCh, CURLOPT_POSTFIELDS, $this->data);

		$reData = curl_exec($httpCh);
		curl_close($httpCh);

		//进行结果处理返回
		$result =  $this->_getResultState($reData);
		if (!$result){
			$this->log('流程更新：更新流程失败');
		}
		return $result;
	}
	private function _setData(){
		//stepID、stepName、jobID
		$currentLevel = $this->level + 1;
		if (!TASK_AUDIT_TYPE){
			$stepid = Audit_STEP_ID_PREFIX-$currentLevel;
			$tmpName = $currentLevel.Audit_STEP_TYPE_SUFFIX;
			if (TASK_AUDIT_AUDIT_LEVEL==1){
				$tmpName = "审核";
			}

			$jobID = Audit_JOB_ID_PREFIX+$currentLevel;
		}
		else {
			if ($this->type == CONTENT_TASK_TYPE){
				if(TASK_AUDIT_MODE == 1){
					$currentLevel = $currentLevel%MAX_TASK_AUDIT_LEVEL;
				}
				$stepid = CONTENT_STEP_ID_PREFIX-$currentLevel;
				$tmpName = $currentLevel.CONTENT_STEP_TYPE_SUFFIX;
				if (TASK_CONTENT_AUDIT_LEVEL==1){
					$tmpName = "内审";
				}

				$jobID = CONTENT_JOB_ID_PREFIX+$currentLevel;
			}
			else {
				if(TASK_AUDIT_MODE == 2){
					$currentLevel = $currentLevel%MAX_TASK_AUDIT_LEVEL;
				}
				$stepid = TECH_STEP_ID_PREFIX-$currentLevel;
				$tmpName = $currentLevel.TECH_STEP_TYPE_SUFFIX;
				if (TASK_TECH_AUDIT_LEVEL==1){
					$tmpName = "技审";
				}

				$jobID = TECH_JOB_ID_PREFIX+$currentLevel;
			}
		}
		$stepName = $tmpName;

		//
		$dataArray = array();
		if ($this->flag == 'back'){
			//打回时，workflowid只能为单个
			$dataArray = array('workflowid'=>(int)$this->workFlowID,'from_stepid'=>$stepid,'to_stepid'=>BACK_TO_STEP_ID,'username'=>$this->name);

			$jobArray = array('from_jobid'=>$jobID,'to_jobid'=>BACK_TO_JOB_ID);
			$dataArray = array_merge($dataArray,$jobArray);
		}
		else{
			//stepstatus、executeguage、jobstatus
			if ($this->flag == 'begin'){
				$executeStatus =  MPCSTEP_STATE_RUN;
				$excuteGuage = AUDIT_EXCUTEGUAGE_ZERO;

				$jobStatus = MPCJOB_STATE_RUN;
			}
			elseif($this->flag == 'exit'){
				$executeStatus =  MPCSTEP_STATE_WAITE;
				$excuteGuage = AUDIT_EXCUTEGUAGE_ZERO;

				$jobStatus = MPCJOB_STATE_WAITE;
			}
			elseif($this->flag == 'pass'){
				$executeStatus = MPCSTEP_STATE_FINISH;
				$excuteGuage = AUDIT_EXCUTEGUAGE_OVER;

				$jobStatus = MPCJOB_STATE_FINISH;
			}
			elseif($this->flag == 'cancel'){
				$executeStatus =  MPCSTEP_STATE_WAITE;
				$excuteGuage = AUDIT_EXCUTEGUAGE_ZERO;
			}

			$stepArray = array('stepid'=>$stepid,'stepname'=>$stepName,'stepstatus'=>$executeStatus,'executeguage'=>$excuteGuage,'username'=>$this->name);
			$jobArray = array('jobid'=>$jobID,'jobstatus'=>$jobStatus,'taskid'=>$this->taskID,'username'=>$this->name);

			$workFlowIDs = NumericArray::toNumericArray($this->workFlowID);
			foreach ($workFlowIDs as $oneID) {
				$tmpDataArray = array('workflowid' =>$oneID,'step'=>array($stepArray),'job'=>array($jobArray));
				if ($this->flag == 'pass'){
					$tmpDataArray = array_merge($tmpDataArray,array('typeid'=>$this->typeID));
				}
				$dataArray[] = $tmpDataArray;
			}
		}

		$this->data = json_encode($dataArray);
	}
	private function _getResultState($theReturn){
		$dataArray = json_decode($theReturn);
		$status = (int)$dataArray->status;
		if ($status==1){
			return true;
		}
		return false;
	}
}