<?php
class AccountsController extends AppController{
	public $name = 'Accounts';
	public $uses = array();
	public $components = array('Excel');

	/**
	 * 一系列值
	 */
	public $userAuditPopedom;

	/**
	 * $clientSoap
	 * soap客户端对象
	 * @var object
	 */
	public $clientSoap;

	/**
	 * beforeFilter
	 */
	public function beforeFilter(){
		parent::beforeFilter();

		//判断权限
		$this->_isAllow($this->userID);
		//获取当前用户审核权限
		if (!TASK_AUDIT_TYPE){
			$this->userAuditPopedom = (int)$this->userAuditAuditPopedom;
		}
		else {
			if ($this->userType == CONTENT_TASK_TYPE){
				$this->userAuditPopedom = (int)$this->userContentAuditPopedom;
			}
			else {
				$this->userAuditPopedom = (int)$this->userTechAuditPopedom;
			}
		}
		//soap客户端对象
		$this->clientSoap = new SoapClient(WS_DOMAIN."/soapServices/wsdl/audit");
	}

	/**
	 * statistics
	 * 统计页面
	 */
	public function statistics(){
		$options = array_merge($this->params, $this->params['url'], $this->passedArgs);
		$model = (int)$options['cmode'];
		$page = (integer)$options['cpage'];

		//获取用户待审核条数
		$allCount = $this->_getNotAuditListCount();

		//统计数据
		//默认时间
		$time = time();
		$time2 = $time - 6*24*60*60;
		$theBeginTime = date('Y/m/d',$time2);
		$theEndTime = date('Y/m/d',$time);

		$tmpBeginTime = $this->Cookie->read('AcountFindTaskTimeStart');
		$tmpEndTime = $this->Cookie->read('AcountFindTaskTimeEnd');
		if ($tmpBeginTime){
			$theBeginTime = $tmpBeginTime;
		}
		if ($tmpEndTime){
			$theEndTime = $tmpEndTime;
		}

		//工作量条件
		$workFindData = array();
		$workFindData['Name'] = $this->Cookie->read('AcountFindName');
		$workFindData['TimeStart'] = $this->Cookie->read('AcountFindTimeStart');
		$workFindData['TimeEnd'] = $this->Cookie->read('AcountFindTimeEnd');

		$timeSearchArray = array('BeginTime'=> $theBeginTime, 'EndTime'=> $theEndTime);
		$taskAccountData = $this->getTaskAcount($timeSearchArray);
		extract($taskAccountData);

		$taskPgmLengthCountArray = json_encode($taskPgmLengthCountArray);
		$taskStateCountArray = json_encode($taskStateCountArray);
		$taskCountDay = json_encode($taskCountDay);
		$taskCountMonth = json_encode($taskCountMonth);
		$taskCountYear = json_encode($taskCountYear);

		//
		$layoutParams = array('layoutMode'=>LAYOUT_MODE_NOT_COUNT,'allCount'=>$allCount,'model'=>$model,'page'=>$page);
		$this->set(compact('layoutParams'));

		$this->set(compact('theBeginTime','theEndTime','taskPgmLengthCountArray'));
		$this->set(compact('workFindData'));
		$this->set(compact('taskStateCountArray'));
		$this->set(compact('taskCountDay','taskCountMonth','taskCountYear'));
	}
	public function taskAccount(){
		$this->layout=false;

		$theBeginTime = $_POST['TimeStart'];
		$theEndTime = $_POST['TimeEnd'];

		//记录条件
		$this->Cookie->write('AcountFindTaskTimeStart',$theBeginTime,false);
		$this->Cookie->write('AcountFindTaskTimeEnd',$theEndTime,false);

		$timeSearchArray = array('BeginTime'=> $theBeginTime, 'EndTime'=> $theEndTime);

		$taskAccountData = $this->getTaskAcount($timeSearchArray);
		extract($taskAccountData);

		echo json_encode(array(
		             'PgmLength'=>$taskPgmLengthCountArray,
		             'State'=>$taskStateCountArray,
		             'Day'=>$taskCountDay,
		             'Month'=>$taskCountMonth,
		             'Year'=>$taskCountYear));
	}

	private function getTaskAcount($timeSearchArray){
		//$timeSearchArray = array('BeginTime'=> $theBeginTime, 'EndTime'=> $theEndTime);
		$countSearch = array_merge(array('TaskType'=> $this->userType),$timeSearchArray);

		$countParamArray = array('Request'=>$countSearch);
		$countParam = $this->_toXmlStr($countParamArray);

		//任务时长统计
		$taskPgmLengthCount = $this->clientSoap->getTaskAndFileAccount($countParam);
		$taskPgmLengthCountArrayt = $this->XmlArray->xml2array($taskPgmLengthCount);
		$taskPgmLengthCountArr = $taskPgmLengthCountArrayt['Response']['CountData'];
		$taskPgmLengthCountArray = $this->_toNumericArray($taskPgmLengthCountArr);

		//任务四种状态统计
		$taskStateCount = $this->clientSoap->getTaskStateAccount($countParam);
		$taskStateCountArrayt = $this->XmlArray->xml2array($taskStateCount);
		$taskStateCountArray = $taskStateCountArrayt['Response']['CountData'];

		//天
		$taskCountArrayd = $this->_getProcessingAccount(TIME_MODE_DAY,$timeSearchArray);
		$taskCountDay = $this->_toNumericArray($taskCountArrayd);

		//月
		$taskCountArraym = $this->_getProcessingAccount(TIME_MODE_MONTH,$timeSearchArray);
		$taskCountMonth = $this->_toNumericArray($taskCountArraym);

		//年
		$taskCountArrayy = $this->_getProcessingAccount(TIME_MODE_YEAR,$timeSearchArray);
		$taskCountYear = $this->_toNumericArray($taskCountArrayy);

		return compact('taskPgmLengthCountArray','taskStateCountArray','taskCountDay','taskCountMonth','taskCountYear');
	}

	/**
	 * workloadAccount
	 * 工作量统计
	 */
	public function workloadAccount(){
		$this->layout=false;
		//分页相关
		$options = array_merge($this->params, $this->params['url'], $this->passedArgs);
		$page = (integer)$options['page'];
		$post = $_POST;

		//获取搜索条件
		$defaultData = array('Name'=>'','TimeStart'=>'','TimeEnd'=>'');
		$searchData = array();
		if ($post){
			$thePostData = $post['data']['Search'];
			$searchData = array_merge($defaultData,$thePostData);

			$this->Cookie->write('AcountFindName',$searchData['Name'],false);
			$this->Cookie->write('AcountFindTimeStart',$searchData['TimeStart'],false);
			$this->Cookie->write('AcountFindTimeEnd',$searchData['TimeEnd'],false);
		}
		else {
			$theOptionData = array();
			$theOptionData['Name'] = $this->Cookie->read('AcountFindName');
			$theOptionData['TimeStart'] = $this->Cookie->read('AcountFindTimeStart');
			$theOptionData['TimeEnd'] = $this->Cookie->read('AcountFindTimeEnd');

			$searchData = array_merge($defaultData,$theOptionData);
		}

		$countParamArray = array('Request'=>array('UserID'=>$this->userID,'TaskType'=>$this->userType,'UserCountPeodom'=>$this->userAcountPopedom,'CurrentPage'=>$page,'PageSize'=>WORKLOAD_LIST_SIZE,'Search'=>$searchData));
		$countParam = $this->_toXmlStr($countParamArray);

		$countDatar = $this->clientSoap->getWorkloadAccount($countParam);
		$countDataList = array();
		if ($countDatar){
			$array = $this->XmlArray->xml2array($countDatar);
			$tempList = $array['Response']['CountData'];
			if ($tempList){
				$countDataList = $tempList;
				$countDataList = $this->_toNumericArray($countDataList);

				$paging = $array['Response']['Paging'];
				$this->params['paging']['Content'] = $paging;
			}
		}

		$this->set(compact('countDataList','searchData'));
	}

	/**
	 * outExcel
	 * 导出数据到Excel
	 */
	public function outExcel(){
		$this->layout=false;
		//
		$options = array_merge($this->params, $this->params['url'], $this->passedArgs);
		$post = $_POST;

		//获取搜索条件
		$defaultData = array('Name'=>'','TimeStart'=>'','TimeEnd'=>'');
		$searchData = array();
		if ($post){
			$thePostData = $post['data']['Search'];
			$searchData = array_merge($defaultData,$thePostData);
		}
		else {
			$theOptionData = array();
			if (isset($options['Name'])){
				$theOptionData['Name'] = $options['Name'];
			}
			if (isset($options['TimeStart'])){
				$theOptionData['TimeStart'] = $options['TimeStart'];
			}
			if (isset($options['TimeEnd'])){
				$theOptionData['TimeEnd'] = $options['TimeEnd'];
			}
			$searchData = array_merge($defaultData,$theOptionData);
		}


		//获取工作量统计数据
		$countParamArray = array('Request'=>array('UserID'=>$this->userID,'TaskType'=>$this->userType,'UserCountPeodom'=>$this->userAcountPopedom,'CurrentPage'=>1,'PageSize'=>WORKLOAD_ACCOUNT_ALL,'Search'=>$searchData));
		$countParam = $this->_toXmlStr($countParamArray);

		$countDatar = $this->clientSoap->getWorkloadAccount($countParam);
		$countDataArray = $this->_toArray($countDatar);
		$countData = $countDataArray['Response']['CountData'];

		$thArray = array('AuditName'=>'用户名','CountAll'=>'处理任务数','CountPass'=>'通过数','CountNotPass'=>'退回数');

		$this->Excel->initExcel($thArray,$countData);
		$this->Excel->outExcel();

		exit;
	}
	private function _getProcessingAccount($mode,$taskStateCountArray){
		$countParamArray = array('Request'=>array(
		                                'TaskType'=> $this->userType,
		                                'TimeDateMode'=>$mode,
		                                'BeginTime'=> $taskStateCountArray['BeginTime'],
		                                'EndTime'=> $taskStateCountArray['EndTime']));
		$countParam = $this->_toXmlStr($countParamArray);

		$taskCount = $this->clientSoap->getTaskProcessingAccount($countParam);
		$taskCountArr = $this->XmlArray->xml2array($taskCount);

		return  $taskCountArr['Response']['CountData'];
	}
}