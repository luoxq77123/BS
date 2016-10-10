<?php

//App::import('Vendor', 'Array2Xml2Array', array('file' => 'xmlandarray' . DS . 'class.array2xml2array.php'));
class Relationship extends AppModel {

    public $name = 'Relationship';
    public $useTable = 'ET_NM_CNTVPGMREL';
    public $updateMpc = array();

//	public $primaryKey = 'taskid';

    /**
     * paginate
     * 自定义分页(元数据)
     */
    public function paginate($conditions, $fields, $limit = 10, $page = 1, $recursive = null, $extra = array()) {
	$BaseNum = ($page - 1) * $limit;
	$params = compact('conditions', 'fields'/* ,'order','group' */);
	$data = $this->find('first', $params);

	$tempData = $data['Content']['metedata'];
	if ($tempData) {
	    $tempArr = Array2Xml2Array::xml2array($tempData);

	    $platForm = $tempArr['MeteData']['PlatFormInfos']['PlatFormInfo'];
	    //$attributes = $tempArr['MeteData']['Attributes']['AttributeItem'];
	    $attributeItems = $tempArr['MeteData']['Attributes']['AttributeItem'];

	    $attributes = array();
	    $allCount = 0;
	    if ($limit == DEFAULT_IS_ALL_METAINFO) {
		$attributes = $attributeItems;
	    } else {
		$attributess = array();
		//获取配置中的设置项目
		$configAttributes = Configure::read('attributes');
		if ($configAttributes) {
		    if ($attributeItems) {
			foreach ($attributeItems as $tempItem) {
			    if (in_array($tempItem['ItemName'], $configAttributes)) {
				$id = (int) array_search($tempItem['ItemName'], $configAttributes);
				$attributess[$id] = $tempItem;
			    }
			}
		    }
		}

		ksort($attributess);
		$allAttributes = array_values($attributess);

		$allCount = count($allAttributes);
		//
		//分页返回				
		for ($num = 0; $num < $limit; $num++) {
		    $currentNum = $BaseNum + $num;
		    if ($currentNum >= $allCount) {
			break;
		    } else {
			$attributes[] = $allAttributes[$currentNum];
		    }
		}
	    }

	    $returnArr = compact('platForm', 'attributes', 'allCount');
	    return $returnArr;
	}
	return false;
    }

    /*
     * @desc 关联精粗编数据，获取列表
     * @params string 查询条件
     */

    public function GetList($sql = "select * from ET_NM_CNTVPGMREL  where OPERATESTATE = 0 and PGMTYPE = 1") {
	$list = $this->newFind($sql);

	foreach ($list as $k => $v) {
	    $list[$k]['OPERATESTATE'] = $this->GetOperatestate($v['OPERATESTATE']);
	    $list[$k]['SATUTS'] =$v['OPERATESTATE'];
	    $list[$k]['PGMTYPE'] = ($v['PGMTYPE'] == 1) ? '粗编' : '精编';
	}
	return $list;
    }

    public function GetChannel() {
	$sql = "select NAMEFORNODE,NODEID from ET_ISSUECATALOG t where PARENTNODEID = '0'";
	return $this->newFind($sql);
    }

    public function GetColumn($nodeid) {
	$sql = "select NAMEFORNODE,NODEID from ET_ISSUECATALOG t where PARENTNODEID = '" . $nodeid . "'";
	return $this->newFind($sql);
    }

    public function RelationColumn($nodeid) {
	$sql = "select RELCOLUMNGUID from ET_NM_CNTVCOLUMN t where COLUMNGUID = '" . $nodeid . "'";
	return $this->newFind($sql);
    }

    /*
     * @desc 根据状态码获取状态值
     * @return string '正在操作'....
     */

    public function GetOperatestate($code = '未操作') {
	switch ($code) {
	    case 0:
		return '未操作';
		break;
	    case 1:
		return '正在操作';
		break;
	    default:
		return '操作完成';
	}
    }

    /**
     * @des 通过关联ids获取报文
     * @param array $ids 精切的guid
     * @param string $channelname 粗切的频道名称
     * @param string $columnname 粗切的栏目名称
     * @param string $submittime 粗切的提交时间
     */
    function GetXmlFormat($ids = array(), $channelname, $columnname, $submittime) {
	
	$id_s = $ids;
	$ids = $this->AddMark($ids);
	$ids = implode(',', $ids);
	$list = $this->newFind('select * from ET_NM_CNTVPGMREL where PGMGUID in (' . $ids . ')');
	foreach ($id_s as $k => $v) {
	    foreach ($list as $kk => $vv) {
		if ($vv['PGMGUID'] == $v) {
			$theData = $this->find('first', array('fields' => array('mpcxml'), 'conditions' => array('PGMGUID' => $v)));
			$mpcArray = Array2Xml2Array::xml2array($theData['Relationship']['mpcxml']);
			foreach($mpcArray['MPC']['Content']['AddTask']['TaskInfo'] as $k => $v) {
				if($v['Scope']=='tv_SobeyExchangeProtocal'){
					foreach($v['Data']['UnifiedContentDefine']['ContentInfo']['ContentData']['EntityData']['AttributeItem'] as $key => $value){
						if($value['ItemCode']=='MAMClass'){
							$a['MAMClass'] = $value['Value'];
						}elseif($value['ItemCode']=='MAMSecondClass'){
							$a['MAMSecondClass'] = $value['Value'];
						}elseif($value['ItemCode']=='Summary'){
							$a['DescritptionofContent'] = $value['Value'];
						}elseif($value['ItemCode']=='Keywords'){
							$a['Keyword'] = $value['Value'];
						}
					}
				}
			}
		    $list_array[] = array_merge(array('ClipName' => $this->format_sepcial_chars($vv['PGMNAME']), 'ClipGUID' => $vv['PGMGUID'], 'ClipLength' => $vv['PGMLENGTH'], 'InPoint' => 0, 'OutPoint' => $vv['PGMLENGTH'] * 400000, 'Producer' => $vv['OPERATER'], 'ShowDate' => $submittime, 'PlayChannel' => $channelname, 'ColumnName' => $vv['COLUMNNAME'], 'FilePath' => $this->formatStr($vv['PGMAUDIOPATH']), 'SerialNumber' => $k + 1),$a);
		    break;
		}
	    }
	}
	return $list_array;
    }

    /**
     * @des 去除字符串尖括号里面的内容
     * @param string $str 字符串
     */
    function formatStr($str = '') {
	$audio = explode('<', $str);
	return $audio[0];
    }

    /**
     * 
     * @param 日志写入
     */
    function AddLog($param = array()) {
	$collumn = array();
	$values = array();
	foreach ($param as $k => $v) {
	    $cullomn[] = $k;
	    if ($k !== 'OPERATETIME') {
		//含有sql函数的字符串不转意
		$values [] = $this->parseValue($v);
	    } else {
		$values [] = $v;
	    }
	}
	$sql = 'insert into SMM_SDOPERATELOG (' . implode(',', $cullomn) . ')';
	$sql = $sql . ' values (' . implode(',', $values) . ')';
	$this->query($sql);
    }

    /**
     * @param array $array 数组转字符串 加上引号
     */
    function AddMark($array = array()) {
	foreach ($array as $k => $v) {
	    $array[$k] = '\'' . $v . '\'';
	}
	return $array;
    }

    /**
     *
     * 初始化数据
     * @param string $guid
     * @param array $array
     */
    public function mpcExecute($guid, $array) {
	if (!$guid) {
	    $this->log('MPC报文处理：粗编节目为空');
	    return false;
	}
	$theData = $this->find('first', array('fields' => array('mpcxml'), 'conditions' => array('PGMGUID' => $guid)));
	$mpcArray = Array2Xml2Array::xml2array($theData['Relationship']['mpcxml']);
	//echo "<pre/>";
	//echo count($mpcArray);exit;
	//print_r($mpcArray['MPC']['Content']);exit;
	//$ETMultiClips = array();
	/*foreach($mpcArray['MPC']['Content']['AddTask']['TaskInfo'] as $k => $v) {
		if($v['Scope']=='tv_SobeyExchangeProtocal'){
			foreach($v['Data']['UnifiedContentDefine']['ContentInfo']['ContentData']['EntityData']['AttributeItem'] as $key => $value){
				if($value['ItemCode']=='MAMClass'){
					$ETMultiClips['MAMClass'] = $value['Value'];
				}elseif($value['ItemCode']=='MAMSecondClass'){
					$ETMultiClips['MAMSecondClass'] = $value['Value'];
				}elseif($value['ItemCode']=='Summary'){
					$ETMultiClips['DescritptionofContent'] = $value['Value'];
				}elseif($value['ItemCode']=='Keywords'){
					$ETMultiClips['Keyword'] = $value['Value'];
				}
			}
		}
	}*/
	//echo "<pre/>";
	//print_r($ETMultiClips);exit;
		
	if ($array) {
	    //$array = array('Scope' => array('ETMultiClips' => $ETMultiClips), 'Schema' => '', 'Data' => array('AttributeItem' => $array));
		$array = array('Scope' => 'ETMultiClips', 'Schema' => '', 'Data' => array('AttributeItem' => $array));
	    $mpcArray['MPC']['Content']['AddTask']['TaskInfo'][] = $array;
	}
	//echo "<pre/>";
	//print_r($mpcArray);exit;
	$newMpcDataXml = new Xml($mpcArray, array('format' => 'tags'));
	$newMpcDataXml->options(array('cdata' => false));
	//修改后的报文 可以存入mpcxml字段中了
	$newMpcDataXmlStr = $newMpcDataXml->toString();
	//echo $newMpcDataXmlStr;exit;
	$this->updateMpc = $mpcArray;
	//提交报文
	$resultState = $this->commitMpc();
	if ($resultState) {
	    return $newMpcDataXmlStr;
	}
	return false;
    }

    protected function commitMpc() {
//	$this->log('MPC调度：columnid->' . $this->columnID . ';serverip->' . $this->serverIP . ';policyid->' . $this->policyID, LOG_DEBUG);
	//流程发起处理
	$nextMpcArray = array('MPCWebCmd' => array(
		'CommandType' => 'AddTask',
		'AddTask' => $this->updateMpc));
	$nextMpcXml = new Xml($nextMpcArray, array('format' => 'tags'));
	$nextMpcXml->options(array('cdata' => false));
	$nextMpcXmlStr = $nextMpcXml->toString();

	try {
	    header("content-type:text/html;charset=utf-8");
	    $clientSoap = new SoapClient(MPC_WS_DOMAIN . '/mpc/CallSobeyInterface.asmx?wsdl');
	    $mpcResult = $clientSoap->__soapCall('CallSobeyMpc', array('parameters' => array('mpcXml' => $nextMpcXmlStr)));
	} catch (Exception $e) {
	    $this->log('MPC发起任务：' . $e->getMessage());
	}

	//将返回结果写入文件
//	$mpcReFilePath = WEBROOT_DOMAIN . MPC_RETRUN_FILENAME . '.xml';
//	$mpcFile = fopen($mpcReFilePath, 'w');
//	fwrite($mpcFile, $mpcResult);
//	fclose($mpcFile);

	$resultState = $this->_getCommitMpcResultState($mpcResult);
	if (!$resultState) {
	    $this->log('MPC报文处理：MPC提交任务失败');
	}
	return $resultState;
    }

    private function _getCommitMpcResultState($result) {
	$data = $result->CallSobeyMpcResult;
	if ($data == 'Success') {
	    return true;
	}
	return false;
    }

    //角色权限判断
    function role_validate($user_id) {
	$sqlStrAudit = "SELECT d.USERID,d.POPEDOMTYPE,d.POPEDOMNAME
                              FROM SMM_USERPOPEDOM d
                              WHERE d.USERID = " . $user_id . "
                                AND d.POPEDOMNAME LIKE '" . USER_RELATION_LOCK . "%'                  
                       UNION
                       SELECT n.USERID,s.POPEDOMTYPE,s.POPEDOMNAME  
                              FROM SMM_ROLEPOPEDOM s
                              INNER JOIN SMM_USER_ROLE n ON n.ROLEID = s.ROLEID
                              WHERE n.USERID = " . $user_id . "
                                AND s.POPEDOMNAME LIKE '" . USER_RELATION_LOCK . "%'";
	return $this->newFind($sqlStrAudit);
    }
    	//转义XML特殊字符
	protected function format_sepcial_chars($str) {
	$str = str_replace('&', '&amp;', $str);
	$str = str_replace('<', '&lt;', $str);
	$str = str_replace('>', '&gt;', $str);
	$str = str_replace("'", '&apos;', $str);
	    $str = str_replace('"', '&quot;', $str);
	    return $str;
    }

}