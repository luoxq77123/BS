<?php
App::import('Vendor', 'Audit', array('file' => 'webservices' . DS . 'audit.php'));
class ContentsController extends AppController {

    public $name = 'Contents';
    public $uses = array('Relationship');

    /**
     * 一系列值
     */
    public $userAuditPopedom;
    //public $userColumn=array();

    /**
     * $clientSoap
     * soap客户端对象
     * @var object
     */
    public $clientSoap;

    /**
     * beforeFilter
     */
    public function beforeFilter() {
	parent::beforeFilter();
	//判断权限
	$this->_isAllow($this->userID);
	//获取当前用户审核权限
	if (!TASK_AUDIT_TYPE) {
	    $this->userAuditPopedom = (int) $this->userAuditAuditPopedom;
	} else {
	    if ($this->userType == CONTENT_TASK_TYPE) {
		$this->userAuditPopedom = (int) $this->userContentAuditPopedom;
	    } else {
		$this->userAuditPopedom = (int) $this->userTechAuditPopedom;
	    }
	}
	//soap客户端对象
//	$this->clientSoap = new SoapClient(WS_DOMAIN . "/soapServices/wsdl/audit");
	$this->clientSoap = new Audit();
    }

    /**
     * auditList
     * 审核节目数据【缩略图或详细列表】
     */
    public function auditList($model = THUMB_MODEL) {
	//是否需要对前一页面的节目解锁
	if (isset($this->params['named']['guid'])) {
	    //状态改变
	    $sql_update_status = "update ET_NM_CNTVPGMREL set OPERATESTATE = 0 where PGMGUID ='" . $this->params['named']['guid'] . "'";
	    $this->Relationship->newSetData($sql_update_status);
	}
	//分页相关
	$options = array_merge($this->params, $this->params['url'], $this->passedArgs);
	$page = (integer) $options['page'];
	$post = $_POST;

	//用户待审核任务数目获取
	$allCount = $this->_getNotAuditListCount();
	//获取查询提交数据
	$curState = NOT_AUDIT_TASK_STATE;
	$findingArr = array();
	$defaultArr = array('StartTime' => '', 'EndTime' => '', 'ContentAuditState' => '', 'PgmColumnID' => '', 'PgmName' => '', 'CreatorName' => '');
	if ($post) {
	    $find = $post['data'];
	    $findingArr = array_merge($defaultArr, $find);

	    //将搜索条件写入cookie
	    $this->Cookie->write('FindingStartTime', $findingArr['StartTime'], false);
	    $this->Cookie->write('FindingEndTime', $findingArr['EndTime'], false);
	    $this->Cookie->write('FindingContentAuditState', $findingArr['ContentAuditState'], false);
	    $this->Cookie->write('FindingPgmColumnID', $findingArr['PgmColumnID'], false);
	    $this->Cookie->write('FindingPgmName', $findingArr['PgmName'], false);
	    $this->Cookie->write('FindingCreatorName', $findingArr['CreatorName'], false);
	} else {
	    //读取查询条件
	    $findingArr['StartTime'] = $this->Cookie->read('FindingStartTime');
	    $findingArr['EndTime'] = $this->Cookie->read('FindingEndTime');
	    $findingArr['ContentAuditState'] = $this->Cookie->read('FindingContentAuditState');
	    $findingArr['PgmColumnID'] = $this->Cookie->read('FindingPgmColumnID');
	    $findingArr['PgmName'] = $this->Cookie->read('FindingPgmName');
	    $findingArr['CreatorName'] = $this->Cookie->read('FindingCreatorName');

	    $findingArr = array_merge($defaultArr, $findingArr);
	}

	//进行页数判断:多于一页的情况
	if ($page > 1) {
	    if (!empty($findingArr['ContentAuditState'])) {
		$curState = (int) $findingArr['ContentAuditState'];
	    }
	    if ($curState == NOT_AUDIT_TASK_STATE) {
		$tmpCount = THE_LIST_SIZE * ($page - 1);
		if ($allCount <= $tmpCount) {
		    $this->redirect(array('controller' => 'contents', 'action' => 'auditList', $model, 'page' => $page - 1));
		}
	    }
	}

	//基础数据接口参数定义
	$requestParam = array(
	    'UserID' => $this->userID,
	    'UserAuditPopedom' => $this->userAuditPopedom,
	    'UserColumn' => $this->userColumn,
	    'TaskType' => $this->userType,
	    'CurrentPage' => $page,
	    'PageSize' => THE_LIST_SIZE,
	    'Finding' => $findingArr);
	if ($model == THUMB_MODEL) {
	    $fields = array('taskid', 'tasktype', 'pgmname', 'pgmlength', 'picpath', 'creatorname', 'taskcreatedate', 'contentauditstate');
	    $requestParam = array_merge($requestParam, array('Fields' => $fields));
	}
	$taskParamArray = array('Request' => $requestParam);
	$taskParam = $this->_toXmlStr($taskParamArray);
	$result = $this->clientSoap->getTaskList($taskParam);
	$pgmAuditList = array();
	if ($result) {
	    $array = $this->XmlArray->xml2array($result);
	    $pgmAuditListt = $array['Response']['TaskList'];
	    if ($pgmAuditListt) {
		$pgmAuditList = $pgmAuditListt;
		$pgmAuditList = $this->_toNumericArray($pgmAuditList);

		$paging = $array['Response']['Paging'];
		$this->params['paging']['Content'] = $paging;
	    }
	}
	//批量挑选的任务
	$selectedList = $this->Cookie->read('AuditBatchList');
	/**
	 * 传递数据说明
	 * allCount    ：当前用户待审核任务数
	 * model       ：任务列表页的当前显示模式
	 * page        ：当前页码
	 * pgmAuditList：节目列表数据
	 */
	$layoutParams = array('layoutMode' => LAYOUT_MODE_ALL, 'allCount' => $allCount, 'model' => $model, 'page' => $page);
	$this->set(compact('layoutParams'));

	$this->set(compact('findingArr'));
	$this->set(compact('selectedList'));
	$this->set(compact('pgmAuditList'));
	if ($model == THUMB_MODEL) {
	    $this->render('audit_list');
	} else {
	    $this->render('audit_detail_list');
	}
    }

    /**
     * setBatchAuditList
     * 更新批量审核任务列表
     */
    public function setBatchAuditList() {
	$this->layout = false;
	$post = $_POST;

	$tag = (int) $post['Tag'];
	$dataString = $post['Data'];

	$curSelectedList = explode(',', $dataString);
	$preList = $this->Cookie->read('AuditBatchList');
	if (!$preList) {
	    $preList = array();
	}

	$batchAuditList = $preList;
	if ($tag == 1) {
	    //加入
	    $batchAuditList = array_merge($batchAuditList, $curSelectedList);
	} else {
	    //去掉
	    if ($curSelectedList) {
		foreach ($curSelectedList as $oneData) {
		    if (in_array($oneData, $batchAuditList)) {
			array_splice($batchAuditList, (int) array_search($oneData, $batchAuditList), 1);
		    }
		}
	    }
	}
	$batchAuditList = array_unique($batchAuditList);
	rsort($batchAuditList);

	$this->Cookie->write('AuditBatchList', $batchAuditList, false);
    }

}