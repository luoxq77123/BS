<?php

App::import('Vendor', 'DbHandle', array('file' => 'webservices' . DS . 'DB_Operation' . DS . 'class.db_handle.php'));

class RelationshipController extends AppController {

    public $name = 'Relationship';
    public $uses = array('Relationship');

    const order = ' order by OPERATESTATE asc,PGMSUBMITTIME desc,OPERATER desc,CHANNELNAME desc,COLUMNGUID desc';

    /**
     * 一系列值
     */
    public $userAuditPopedom;

    //public $userColumn=array();

    /**
     * beforeFilter
     */
    public function beforeFilter() {
	parent::beforeFilter();
	//判断权限
	$this->_isAllow($this->userID);
	if (isset($this->params['url']['result_status'])) {
	    $this->set(array('result_status' => urldecode($this->params['url']['result_status'])));
	}
    }

    public function index() {
	$post = $_POST;
	$findingArr = array();
	$defaultArr = array('channelname' => '', 'begintime' => '', 'columnname' => '', 'endtime' => '', 'PgmName' => '', 'pgmtype' => '', 'operater' => '', 'operatestate' => '');

	//获取频道
	$channel_list = $this->Relationship->GetChannel();
	$findingArr['channel_list'] = $channel_list;
	//分页相关
	$page = isset($this->params['named']['page']) ? (integer) $this->params['named']['page'] : 1;
	$limit = THE_LIST_SIZE;
	$operatestate = '1 = 1';
//	$sql = "select ET_NM_CNTVPGMREL.*,rownum rn from ET_NM_CNTVPGMREL  where " . $operatestate . " and rownum<=" . $page * $limit;
	$sql_count = "select * from ET_NM_CNTVPGMREL  where " . $operatestate;
	$sql_condition = $this->get_where($operatestate, $sql_count);
	$sql = "select * from (select t.*,rownum rn from (select * from ET_NM_CNTVPGMREL  where " . $sql_condition['sql'] . ") t) where rn between " . ((($page - 1) * $limit) + 1) . " and " . $page * $limit;
	$findingArr['post_keyword'] = array_merge($defaultArr, $sql_condition['find_array']);
	$findingArr['PgmName'] = $findingArr['post_keyword']['PgmName'];
	$relationship_list = $this->Relationship->GetList($sql);
	$allCount = count($this->Relationship->GetList($sql_condition['sql_count']));
	$pageCount = intval(ceil($allCount / $limit));
	$defaults = array('limit' => $limit, 'page' => 1);
	$options = array_merge(array('page' => 1, 'limit' => 12), $defaults, array('page' => $page));
	$paging = array(
	    'page' => $page,
	    'current' => $limit,
	    'count' => $allCount,
	    'prevPage' => ($page > 1),
	    'nextPage' => ($allCount > ($page * $limit)),
	    'pageCount' => $pageCount,
	    'defaults' => array_merge(array('limit' => 1, 'step' => 1), $defaults),
	    'options' => $options
	);
	$layoutParams = array('layoutMode' => LAYOUT_MODE_ALL, 'model' => '', 'page' => $page);
	//权限检查是否显示解锁图标
	$unlock = (bool) $this->Relationship->role_validate($this->userID);
	//MLY.cp 判断需要
	$relationship = true;
	$this->params['paging']['Relationship'] = $paging;
	$this->set(compact('layoutParams', 'findingArr', 'selectedList', 'pgmAuditList', 'relationship', 'relationship_list', 'unlock'));
	$this->render('index');
    }

    protected function get_where($sql, $sql_count = '') {
	$params = $this->params;
	//节目名称
	$pgmname = isset($params['data']['PgmName']) ? $params['data']['PgmName'] : (isset($params['named']['PgmName']) ? $params['named']['PgmName'] : '');
	//频道名称
	$channelname = isset($params['data']['channelname']) ? $params['data']['channelname'] : (isset($params['named']['channelname']) ? $params['named']['channelname'] : '');
	//栏目名称 
	$columnname = isset($params['data']['columnname']) ? $params['data']['columnname'] : (isset($params['named']['columnname']) ? $params['named']['columnname'] : '');

	$begintime = isset($params['data']['begintime']) ? $params['data']['begintime'] : (isset($params['named']['begintime']) ? $params['named']['begintime'] : '');

	$endtime = isset($params['data']['endtime']) ? $params['data']['endtime'] : (isset($params['named']['endtime']) ? $params['named']['endtime'] : '');
	//节目类型
	$pgmtype = isset($params['data']['pgmtype']) ? $params['data']['pgmtype'] : (isset($params['named']['pgmtype']) ? $params['named']['pgmtype'] : 1);
	//编辑人员
	$operater = isset($params['data']['operater']) ? $params['data']['operater'] : (isset($params['named']['operater']) ? $params['named']['operater'] : '');
	//节目状态
	$operatestate = isset($params['named']['operatestate']) ? $params['named']['operatestate'] : '!=10';

	if ($pgmname && $pgmname !== '请输入节目名称') {
	    $sql = $sql . " and PGMNAME LIKE '%" . trim($pgmname) . "%'";
	    $sql_count = $sql_count . " and PGMNAME LIKE '%" . trim($pgmname) . "%'";
	    $find_array['PgmName'] = $pgmname;
	}
	if ($channelname) {
	    $sql = $sql . " and CHANNELNAME = '" . $channelname . "'";
	    $sql_count = $sql_count . " and CHANNELNAME = '" . $channelname . "'";
	    $find_array['channelname'] = $channelname;
	}
	if ($columnname) {
	    $sql = $sql . " and COLUMNNAME = '" . $columnname . "'";
	    $sql_count = $sql_count . " and COLUMNNAME = '" . $columnname . "'";
	    $find_array['columnname'] = $columnname;
	}
	if ($begintime) {
	    $h_m_s = (strlen($begintime) > 10) ? '' : '00:00:00';
	    $sql = $sql . " and PGMSUBMITTIME >= to_timestamp('" . $begintime . " " . $h_m_s . "','yyyy-mm-dd hh24:mi:ss')";
	    $sql_count = $sql_count . " and PGMSUBMITTIME >= to_timestamp('" . $begintime . " " . $h_m_s . "','yyyy-mm-dd hh24:mi:ss')";
	    $find_array['begintime'] = $begintime;
	}
	if ($endtime) {
	    $h_m_s = (strlen($endtime) > 10) ? '' : '23:59:59';
	    $sql = $sql . " and PGMSUBMITTIME <= to_timestamp('" . $endtime . " " . $h_m_s . "','yyyy-mm-dd hh24:mi:ss')";
	    $sql_count = $sql_count . " and PGMSUBMITTIME <= to_timestamp('" . $endtime . " " . $h_m_s . "','yyyy-mm-dd hh24:mi:ss')";
	    $find_array['endtime'] = $endtime;
	}
	if ($pgmtype || $pgmtype == '0') {
	    $sql = $sql . " and PGMTYPE = '" . $pgmtype . "'";
	    $sql_count = $sql_count . " and PGMTYPE = '" . $pgmtype . "'";
	    $find_array['pgmtype'] = $pgmtype;
	}
	if ($operater) {
	    $sql = $sql . " and OPERATER = '" . $operater . "'";
	    $sql_count = $sql_count . " and OPERATER = '" . $operater . "'";
	    $find_array['operater'] = $operater;
	}
	if (($operatestate || $operatestate == '0') && $operatestate !== '!=10') {
	    $sql = $sql . " and OPERATESTATE = '" . $operatestate . "'";
	    $sql_count = $sql_count . " and OPERATESTATE = '" . $operatestate . "'";
	    $find_array['operatestate'] = $operatestate;
	}
	if ($operatestate === '!=10') {
	    $sql = $sql . " and OPERATESTATE " . $operatestate . "";
	    $sql_count = $sql_count . " and OPERATESTATE " . $operatestate . "";
	}
	return array('sql' => $sql . self::order, 'sql_count' => $sql_count, 'find_array' => $find_array);
    }

    /*
     * @desc 解锁请求
     */

    public function unlock() {
	$this->autoRender = false;
	$ids = array_keys($_POST['id']);

	$now = date('Y-m-d H:i:s', time());
	//todo 编辑权限
	if (1 === 1) {
	    foreach ($ids as $k => $v) {
		//获取当前正在操作粗编信息
		$sql_info = "select * from ET_NM_CNTVPGMREL where PGMGUID = '" . $v . "'";
		$relationship_info = $this->Relationship->newFind($sql_info);
		//写日志
		$array = array('PROGRAMNAME' => $relationship_info[0]['PGMNAME'],
		    'PROGRAMGUID' => $relationship_info[0]['PGMGUID'],
		    'ENTITYID' => '',
		    'PGMLENGTH' => $relationship_info[0]['PGMLENGTH'],
		    'OPERATORNAME' => $this->userCode,
		    'OPERATETYPE' => 21,
		    'OPERATERESULT' => 0,
		    'OPERATETIME' => "to_timestamp('" . $now . "','yyyy-mm-dd hh24:mi:ss')",
		    'EXCHANNEL' => $relationship_info[0]['CHANNELNAME'],
		    'EXCOLUMN' => $relationship_info[0]['COLUMNNAME'],
		);
		$sql = "update ET_NM_CNTVPGMREL set OPERATESTATE = 0 where OPERATESTATE = 1 and PGMGUID ='" . $v . "'";
		$this->Relationship->newSetData($sql);
		$this->Relationship->AddLog($array);
	    }
	}
	$this->redirect(array('action' => 'index','begintime'=>date('Y-m-d H:i:s', time() - 60 * 60 * 24),'endtime'=> date('Y-m-d H:i:s')));
    }

    /**
     * @desc 关联操作页
     */
    public function operater() {
	$post = $_POST;
	$guid = $this->params['named']['GUID'];
	$is_fresh = (isset($this->params['named']['type']) && $this->params['named']['type'] === 'refresh') ? true : false;

	//如果是点的刷新操作就先把操作状态打开 add 0922
//	if ($is_fresh) {
//	    $sql_update_status = "update ET_NM_CNTVPGMREL set OPERATESTATE = 0 where PGMGUID ='" . $guid . "' and OPERATESTATE = 1";
//	    $this->Relationship->newSetData($sql_update_status);
//	}
	//获取当前粗编信息
	$relationship_info = $this->is_validate($guid);
	//状态改变
	$sql_update_status = "update ET_NM_CNTVPGMREL set OPERATESTATE = 1 where PGMGUID ='" . $guid . "' and OPERATESTATE = 0";
	$this->Relationship->newSetData($sql_update_status);
	//获取频道
	$channel_list = $this->Relationship->GetChannel();
	$findingArr['channel_list'] = $channel_list;
	$findingArr['post_keyword'] = array('operater' => '', 'begintime' => '', 'endtime' => '', 'columnname' => '', 'channelname' => '', 'PgmName' => '');
	//备选区域查询
	if ($post && !$this->params['data']['submit_type'] && !$is_fresh) {
	    $sql = "select * from ET_NM_CNTVPGMREL  where 1 = 1";
	    //获取查询sql的条件以及对模版赋值
	    $sql_condition = $this->get_where($sql);
	    $reserv_list = $this->Relationship->GetList($sql_condition['sql']);
	    $findingArr['post_keyword'] = array_merge($findingArr['post_keyword'], $sql_condition['find_array']);
	}
	$now = date('Y-m-d H:i:s', time());
	
	if (isset($this->params['data']['submit_type']) && $this->params['data']['submit_type'] === '1') {
	    //add 20140110 fix 一个人正在关联，另外一个人解锁再关联一次
	    if($relationship_info[0]['OPERATESTATE'] == 2) {
		$this->redirect(array('action' => 'index?result_status=' . urlencode('该节目已被其他编辑关联完成')));
	    }
	    $ids = array();
	    if (isset($post['id']))
		$ids = array_keys($post['id']);
	    //写日志
	    $array = array('PROGRAMNAME' => $relationship_info[0]['PGMNAME'],
		'PROGRAMGUID' => $relationship_info[0]['PGMGUID'],
		'ENTITYID' => '',
		'PGMLENGTH' => $relationship_info[0]['PGMLENGTH'],
		'OPERATORNAME' => $this->userCode,
		'OPERATETYPE' => 22,
		'OPERATERESULT' => 0,
		'OPERATETIME' => "to_timestamp('" . $now . "','yyyy-mm-dd hh24:mi:ss')",
		'EXCHANNEL' => $relationship_info[0]['CHANNELNAME'],
		'EXCOLUMN' => $relationship_info[0]['COLUMNNAME'],
	    );

	    //组装为xml格式
	    $result = $this->mpc_format($ids, $relationship_info[0]['PGMGUID'], $relationship_info[0]['CHANNELNAME'], $relationship_info[0]['COLUMNNAME'], $relationship_info[0]['PGMSUBMITTIME']);
	    if ($result) {
		//更新精编的状态为已完成 2
		foreach ($ids as $k => $v) {
		    $ids_sql_update = "update ET_NM_CNTVPGMREL set OPERATESTATE = 2 where PGMGUID ='" . $v . "'";
		    $this->Relationship->newSetData($ids_sql_update);
		}
		//写日志
		$this->Relationship->AddLog($array);
//              修改该粗编节目的状态
		$sql_update = "update ET_NM_CNTVPGMREL set OPERATESTATE = 2 where PGMGUID ='" . $guid . "'";
		$this->Relationship->newSetData($sql_update);
//	    更新对应Mpc数据
		$blobStyle = DbHandle::_getDbSetBlobStyle();
		$sqlStr = "update ET_NM_CNTVPGMREL t set MPCXML=" . $blobStyle . " where t.PGMGUID='" . $guid . "'";
		$reMpcData = $this->Relationship->newSetBlob($sqlStr, $result);
		$this->Session->setFlash('关联成功',false);
		$this->redirect(array('action' => 'index','begintime'=>date('Y-m-d H:i:s', time() - 60 * 60 * 24),'endtime'=> date('Y-m-d H:i:s')));
	    } else {
		$this->Session->setFlash('关联失败',false);
		$this->redirect(array('action' => 'index','begintime'=>date('Y-m-d H:i:s', time() - 60 * 60 * 24),'endtime'=> date('Y-m-d H:i:s')));
	    }
	}
	$now_begin = date('Y-m-d H:i:s', time() - 60 * 60 * 24);
	$now_end = $now;
	//初始化查询条件的时间 默认为最近一天的
	if (!$findingArr['post_keyword']['begintime'] && !$findingArr['post_keyword']['endtime']) {
	    $findingArr['post_keyword']['begintime'] = $now_begin;
	    $findingArr['post_keyword']['endtime'] = $now_end;
	}

	//add 0927 关联刷新保留之前选中的状态
	$relationship_list = $this->get_relaiton_column_datas($relationship_info[0]['COLUMNGUID'], $is_fresh);

	$layoutParams = array('layoutMode' => LAYOUT_MODE_ALL, 'model' => '', 'page' => '');
	//MLY.cp 判断需要
	$no_searchbox = true;
	$relationship = true;
	$is_operator = true;
	$this->set(compact('is_operator', 'relationship_info', 'guid', 'no_searchbox', 'relationship', 'layoutParams', 'relationship_operation', 'relationship_list', 'reserv_list', 'findingArr'));
	//ajax查询
	if ($this->isAjax()) {
	    if ($is_fresh) {
		$this->render('refresh_result');
	    } else {
		$this->render('search_result');
	    }
	} else {
	    $this->render('operater');
	}
    }

    /**
     * @desc 关联操作刷新时需要
     * @return type
     */
    function get_except_json() {
	$this->layout = false;
	$this->autoRender = false;
	$relationship_list = $this->get_relaiton_column_datas($this->params['named']['GUID'], true);
	//add 1010 这里是把刷新数来的数据传递给前端，让前端去判断是否有重复的，如果有重复的就删除调重复的数据
	$pgmguids = array();
	foreach ($relationship_list as $v) {
	    $pgmguids[] = $v['PGMGUID'];
	}
	echo json_encode($pgmguids);
    }

    protected function get_relaiton_column_datas($guid, $is_fresh) {

	$now_begin = date('Y-m-d H:i:s', time() - 60 * 60 * 24);
	$now_end = date('Y-m-d H:i:s', time());
	//获取当前COLUMNGUID
	$relcolumn_sql = "select RELCOLUMNGUID from ET_NM_CNTVCOLUMN where COLUMNGUID = '" . $guid . "'";
	$except_guids = "and 1=1";
	if ($is_fresh) {
	    //查找除勾选以外的关联区域信息
	    $ids = array();
	    $guids = "";
	    if (isset($_POST['id'])) {
		$ids = array_keys($_POST['id']);
		$ids = $this->Relationship->AddMark($ids);
		$except_guids = "and PGMGUID not in (" . implode(',', $ids) . ")";
	    }
	}
	$sql = "select * from ET_NM_CNTVPGMREL where PGMTYPE = 0 " . $except_guids . " and COLUMNGUID in(" . $relcolumn_sql . ") and PGMSUBMITTIME < to_timestamp('" . $now_end . "','yyyy-mm-dd hh24:mi:ss') and PGMSUBMITTIME > to_timestamp('" . $now_begin . "','yyyy-mm-dd hh24:mi:ss') and OPERATESTATE = 0 order by PGMSUBMITTIME desc";
//	$sql = "select * from ET_NM_CNTVPGMREL where PGMTYPE = 0 order by PGMSUBMITTIME desc";
	return $this->Relationship->newFind($sql);
    }

    //是否可以操作该节目
    protected function is_validate($guid) {
	//获取当前粗编信息
	$sql = "select * from ET_NM_CNTVPGMREL where PGMGUID = '" . $guid . "'";
	$relationship_info = $this->Relationship->newFind($sql);
	if (($relationship_info[0]['OPERATESTATE'] != 0 && !$_POST) || $relationship_info[0]['PGMTYPE'] != 1) {
	    //todo 如果是精编或者不是未操作应该返回erroe页面
	    $this->redirect(array('controller' => 'relationship', 'action' => 'index?result_status=' . urlencode('该节目正在操作或已完成操作')));
	}
	return $relationship_info;
    }

    //关联页面点击返回后的修改状态 
    function get_back() {
	$this->autoRender = false;
	$guid = $_REQUEST['guid'];
	//状态改变
	$sql_update_status = "update ET_NM_CNTVPGMREL set OPERATESTATE = 0 where PGMGUID ='" . $guid . "'";
	$this->Relationship->newSetData($sql_update_status);
	return true;
    }

    /*
     * @params array 关联后的精编节目
     * @params string 粗编节目guid
     */

    //关联节目后的MPC报文发送及逻辑编写
    protected function mpc_format($ids, $guid, $channelname, $columnname, $submittime) {
	$this->Relationship = new Relationship;
	$array_attribute = array();
	if ($ids) {
	    $array_attribute = $this->Relationship->GetXmlFormat($ids, $channelname, $columnname, $submittime);
	}
	return $this->Relationship->mpcExecute($guid, $array_attribute);
    }

    /*
     * @desc 播放页面
     * 
     */

    function player() {
	$this->layout = false;
	$this->Relationship = new Relationship;
	$guid = $this->params['named']['GUID'];
	//获取当前节目信息
	$sql = "select PGMVIEDOPATH,PGMAUDIOPATH,PGMNAME from ET_NM_CNTVPGMREL  where PGMGUID = '" . $guid . "'";
	$relationship_info = $this->Relationship->newFind($sql);
	//查找音频声道
	$audio = $relationship_info[0]['PGMAUDIOPATH'];
	$audio = $this->getAudioChannel($audio);
	$pgmname = $relationship_info[0]['PGMNAME'];
	$play_info = array();
	$paly_info[] = array(
	    'filename' => $relationship_info[0]['PGMVIEDOPATH'],
	    'clipclass' => '0',
	    'channel' => '0',
	);
	foreach ($audio['audio'] as $k => $v) {
	    $paly_info[] = array(
		'filename' => $audio['audio_path'],
		'clipclass' => $v,
		'channel' => $v,
	    );
	}
	$taskFile = json_encode($paly_info);
	$this->set(compact('taskFile', 'pgmname'));
	$this->render();
    }

    function getAudioChannel($audio = '<>') {
	$audio = explode('<', $audio);
	$audio_path = $audio[0];
	$audio = str_replace('>', '', $audio[1]);
	return array(
	    'audio_path' => $audio_path,
	    'audio' => explode(',', $audio)
	);
    }

    function set_relation_column() {
	if ($_POST) {
	    $nodeid = $this->params['data']['columnname'];
	    $relationnodeid = array_keys($this->params['form']['ids']);
	    //删除数据
	    $delete_sql = "delete from ET_NM_CNTVCOLUMN where COLUMNGUID = '" . $nodeid . "'";
	    $this->Relationship->query($delete_sql);
	    //添加数据
	    foreach ($relationnodeid as $v) {
		$add_sql = "insert into ET_NM_CNTVCOLUMN (COLUMNGUID,RELCOLUMNGUID) values ('" . $nodeid . "','" . $v . "')";
		$this->Relationship->query($add_sql);
	    }
	    $success = true;
	    $this->set(compact('success'));
	}
	//是否需要对前一页面的节目解锁
	if (isset($this->params['named']['guid'])) {
	    //状态改变
	    $sql_update_status = "update ET_NM_CNTVPGMREL set OPERATESTATE = 0 where PGMGUID ='" . $this->params['named']['guid'] . "'";
	    $this->Relationship->newSetData($sql_update_status);
	}
	$relationship_operation = true;
	$findingArr = array('channelname' => '', 'begintime' => '', 'columnname' => '', 'endtime' => '', 'PgmName' => '', 'pgmtype' => '', 'operater' => '', 'operatestate' => '');
	//获取频道
	$channel_list = $this->Relationship->GetChannel();
	$relationship = true;
	$this->set(compact('channel_list', 'relationship', 'relationship_operation'));
	$this->render();
    }

    function get_column() {
	$this->autoRender = false;

	$nodeid = $this->params['form']['nodeid'];
	//获取频道
	$column_list = $this->Relationship->GetColumn($nodeid);
	$output = '<option value="">请选择栏目</option>';
	foreach ($column_list as $v) {
	    $output .= '<option value="' . $v['NODEID'] . '">' . $v['NAMEFORNODE'] . '</option>';
	}
	echo $output;
    }

    function get_all_column() {
	$nodeid = $this->params['form']['nodeid'];
	$this->layout = false;

	//获取频道
	$channel_list = $this->Relationship->GetChannel();
	foreach ($channel_list as $k => $v) {
	    $channel_list[$k]['children_column'] = $this->Relationship->GetColumn($v['NODEID']);
	}
	//查询哪些已经被关联了
	$relation_column = $this->Relationship->RelationColumn($nodeid);
	$new_relation_column = array();
	//format $relation_column
	foreach ($relation_column as $v) {
	    $new_relation_column[] = $v['RELCOLUMNGUID'];
	}
	$this->set(compact('channel_list', 'new_relation_column'));
	$this->render();
    }

    /**
     * @des 关联库预览按时间排序，让服务端来排序方便。客户端排麻烦
     * 
     */
    function relation_preview() {
	$ids = array_keys($_POST['id']);
	$ids = $this->Relationship->AddMark($ids);
	$sql = "select * from ET_NM_CNTVPGMREL  where PGMGUID in (" . implode(',', $ids) . ")	";
	$relationship_info = $this->Relationship->newFind($sql);
	var_dump($relationship_info);
	exit;
    }

}