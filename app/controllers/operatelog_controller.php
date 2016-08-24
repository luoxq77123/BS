<?php

App::import('Vendor', 'PHPExcel', array('file' => 'phpexcel' . DS . 'PHPExcel.php'));

function format_length($length, $unit = 'frame') {
	if ($unit == 'frame') {
		$length = floor($length / 25);
	} elseif ($unit == 'ms') {
		$length = floor($length / 1000);
	}

	$hours = floor($length / 3600);
	$minutes = floor(($length % 3600) / 60);
	$seconds = ($length % 3600) % 60;

	return sprintf("%02.00f:%02.00f:%02.00f", $hours, $minutes, $seconds);
}

if (!defined("OPERATELOG_SHOW_EX")) {
	define("OPERATELOG_SHOW_EX", true);
}

/**
 * 操作记录和统计
 */
class OperatelogController extends AppController {

    // public $uses = array();
    //操作类型
	public $operatetypes = array(
		2 => "xcut粗编提交",
		10 => "媒资粗编编目",
		1 => "xcut精编提交",
		12 => "媒资精编编目",
		3 => "bs审核",
		11 => "媒资粗编审核",
		13 => "媒资精编审核"
		);
    //操作结果
	public $operateresults = array(
		0 => '通过',
		1 => '不通过',
		2 => '打回',
		3 => '待审核'
		);
    //操作结果
	public $platform = array(
		0 => '无锡基地',
		1 => '北京基地',
		2 => '无锡neulion'
		);
	public $layout = "opl";

	public function beforeFilter() {
	/**
	 * 配置Cookie相关属性
	 */
	$this->Cookie->name = COOKIE_NAME;

	$this->userID = $this->Cookie->read('AuditUserID');
	$this->userName = $this->Cookie->read('AuditUserName');
	$this->userCode = $this->Cookie->read('AuditUserCode');
	$this->userType = $this->Cookie->read('AuditUserType');

	if (empty($this->userID)) {
		$this->redirect(array('controller' => 'users', 'action' => 'login'));
		exit;
	}

	$userInfo = array(
		'UserID' => $this->userID,
		'UserName' => $this->userName,
		'UserCode' => $this->userCode,
		'UserType' => $this->userType
		);
	$this->set(compact('userInfo'));

	$params = $this->params['url'];
	if (isset($params['url'])) {
		unset($params['url']);
	}

	$this->passedArgs = array_merge($this->passedArgs, $params);

	if (isset($this->passedArgs['operatetype']) && is_array($this->passedArgs['operatetype'])) {
		$this->passedArgs['operatetype'] = implode(',', $this->passedArgs['operatetype']);
	}

	//时间范围搜索
	$start_date = "";
	if (isset($this->passedArgs['start_date'])) {
		$start_date = $this->passedArgs['start_date'];
//            if (!$start_date || !preg_match("/^\d{4}\-\d{2}\-\d{2}$/", $start_date)) {
		if (!$start_date) {
			$start_date = "";
		}
	}

	/*
	  if (!$start_date) {
	  $start_date = date_modify(new DateTime(), "-1 month");
	  $start_date = $start_date->format("Y-m-d");
	  }
	 */

	  $end_date = "";
	  if (isset($this->passedArgs['end_date'])) {
	  	$end_date = $this->passedArgs['end_date'];
//            if (!$end_date || !preg_match("/^\d{4}\-\d{2}\-\d{2}$/", $end_date)) {
	  	if (!$end_date) {
	  		$end_date = "";
	  	}
	  }

	  $this->start_date = $start_date? : "";
	  $this->end_date = $end_date? : "";

	  $this->set("start_date", $this->start_date);
	  $this->set("end_date", $this->end_date);
	//add 20140120
	  $this->excolumn = (isset($this->passedArgs['excolumn']) && $this->passedArgs['excolumn']) ? $this->passedArgs['excolumn'] : "";
	  $this->set("excolumn", $this->excolumn);
	//add 20140126
	  $this->exchannel = (isset($this->passedArgs['exchannel']) && $this->passedArgs['exchannel']) ? $this->passedArgs['exchannel'] : "";
	  $this->set("exchannel", $this->exchannel);
	  $this->set("platform_select", '-1');
	}

	public function beforeRender() {
		parent::beforeRender();

		$this->set("operatetypes", $this->operatetypes);
		$this->set("operateresults", $this->operateresults);
		$this->set("platform", $this->platform);
	}

    //操作记录列表
	public function index() {
	//判断是否为导出
		if (isset($this->passedArgs['export']) && $this->passedArgs['export'] == 'excel') {
			$export = 'excel';
		} else {
			$export = null;
		}

		$conditions = array();

	//按标题搜索
		if (isset($this->passedArgs['programname'])) {
			$programname = trim($this->passedArgs['programname']);

			if ($programname) {
				$conditions["Operatelog.programname LIKE"] = "%" . $programname . "%";
				$this->set("programname", $programname);
			}
		}

	//按GUID搜索
		if (isset($this->passedArgs['programguid'])) {
			$programguid = trim($this->passedArgs['programguid']);

			if ($programguid) {
				$conditions["Operatelog.programguid"] = $programguid;
				$this->set("programguid", $programguid);
			}
		}

	//按操作人搜索
		if (isset($this->passedArgs['operatorname'])) {
			$operatorname = trim($this->passedArgs['operatorname']);

			if ($operatorname) {
				$conditions["Operatelog.operatorname LIKE"] = "%" . $operatorname . "%";
				$this->set("operatorname", $operatorname);
			}
		}

	    //按步骤查询
		if (isset($this->passedArgs['operatetype'])) {
			$operatetype = $this->passedArgs['operatetype'];

			if ($operatetype) {
				$conditions["Operatelog.operatetype"] = explode(',', $operatetype);
			}

			$this->set("operatetype", explode(',', $operatetype));
		}

		if ($this->start_date) {
			$conditions[] = "Operatelog.operatetime >='" . $this->start_date . "'";
		}

		if ($this->end_date) {
			$conditions[] = "Operatelog.operatetime <='" . $this->end_date . "'";
		}
		$limit = 100;
		if ($export) {
			$limit = 10000;
			$this->passedArgs['page'] = 1;
		}
		$order = 'Operatelog.operatetime DESC';
	//add 20140126 加入频道条件
		if ($this->exchannel) {
	    //查询通过用户输入的栏目名称查询
			$conditions[] = "Operatelog.exchannel LIKE '%" . $this->exchannel . "%'";
			$order = 'Operatelog.exchannel DESC, ' . $order;
		}
	//add 20140120 加入栏目条件
		if ($this->excolumn) {
	    //查询通过用户输入的栏目名称查询
			$conditions[] = "Operatelog.excolumn LIKE '%" . $this->excolumn . "%'";
			$order = 'Operatelog.excolumn DESC, ' . $order;
		}

	//add 20140126 加入平台查询
		if (isset($this->passedArgs['platform'])) {
			$platform = trim($this->passedArgs['platform']);
			if ($platform !== '-1') {
				$conditions["Operatelog.systemid"] = $platform;
				$this->set("platform_select", $platform);
			}
		}
	//排除状态值为21,22的统计
		$conditions['Operatelog.operatetype not'] = array('21', '22');

		$this->paginate = array(
			'paramType' => 'querystring',
			'conditions' => $conditions,
			'order' => $order,
			'limit' => $limit
			);

		$operatelogs = $this->paginate('Operatelog');

		if ($export == 'excel') {
			//add 20140320 
			ini_set('memory_limit', '256M');
			$headers = array(
				"programname" => array("节目名称", 15),
				"pgmlength" => array("节目时长", 12),
				"operatorname" => array("操作人员", 15),
				"operatetime" => array("完成时间", 20),
				"operatetype" => array("完成操作", 12),
                "operateresult" => array("完成状态", 12),
				"programguid" => array("GUID", 40),
				);

			if (!defined("OPERATELOG_SHOW_EX") || OPERATELOG_SHOW_EX) {
				$headers['exchannel'] = array("所属频道", 15);
				$headers['excolumn'] = array("所属栏目", 15);
			} else {
				$headers['entityid'] = array("媒资ID", 15);
			}
            $headers['systemid'] = array("平台", 12);
			$lines = array();
			foreach ($operatelogs as $operatelog) {
				$operatelog['Operatelog']['pgmlength'] = format_length($operatelog['Operatelog']['pgmlength']);
				$operatelog['Operatelog']['operatetype'] = isset($this->operatetypes[$operatelog['Operatelog']['operatetype']]) ? $this->operatetypes[$operatelog['Operatelog']['operatetype']] : $operatelog['Operatelog']['operatetype'];
                $operatelog['Operatelog']['operateresult'] = isset($this->operateresults[$operatelog['Operatelog']['operateresult']])?$this->operateresults[$operatelog['Operatelog']['operateresult']]:$operatelog['Operatelog']['operateresult'];
                $operatelog['Operatelog']['systemid'] = $this->platform[$operatelog['Operatelog']['systemid']];
                $lines[] = $operatelog['Operatelog'];
			}

			if ($this->start_date && $this->end_date) {
				$export_name = "操作记录(" . $this->start_date . "至" . $this->end_date . ").xlsx";
			} else {
				$export_name = "操作记录.xlsx";
			}

			return $this->export_excel("操作记录", $headers, $lines, $export_name);
		}

		$this->set("operatelogs", $operatelogs);
		$this->set("action", "show_log");
	}

    //用户任务统计 
	public function stats() {
		if (isset($this->passedArgs['export']) && $this->passedArgs['export'] == 'excel') {
			$export = 'excel';
		} else {
			$export = null;
		}

		$conditions = array();

	    //按操作人搜索
		if (isset($this->passedArgs['operatorname'])) {
			$operatorname = trim($this->passedArgs['operatorname']);
			if ($operatorname) {
				$conditions["Operatelog.operatorname LIKE"] = "%" . $operatorname . "%";
				$this->set("operatorname", $operatorname);
			}
		}

	    //按步骤搜索
		if (isset($this->passedArgs['operatetype'])) {
			$operatetype = $this->passedArgs['operatetype'];

			if ($operatetype) {
				$conditions["Operatelog.operatetype"] = explode(',', $operatetype);
			}

			$this->set("operatetype", explode(',', $operatetype));
		}

		if ($this->start_date) {
			$conditions[] = "Operatelog.operatetime >='" . $this->start_date . "'";
		}

		if ($this->end_date) {
			$conditions[] = "Operatelog.operatetime <='" . $this->end_date . "'";
		}
	    //排除状态值为21,22的统计
		$conditions['Operatelog.operatetype not'] = array('21', '22');

	    //add 20140127 加入平台查询
		if (isset($this->passedArgs['platform'])) {
			$platform = trim($this->passedArgs['platform']);
			if ($platform !== '-1') {
				$conditions["Operatelog.systemid"] = $platform;
				$this->set("platform_select", $platform);
			}
		}

	// $conditions[] = "((Operatelog.operatetype!=3 AND Operatelog.operateresult=0) OR Operatelog.operatetype=3)";
		$stats_users = $this->Operatelog->find('all', array(
			"conditions" => $conditions,
			"fields" => "systemid,operatorname, operatetype, operateresult, count(*) total, sum(pgmlength) total_length",
			"order" => "operatorname asc, operatetype asc",
			"group" => "systemid,operatorname, operatetype, operateresult"
			));
		$stats_user_logs = array();
		foreach ($stats_users as $stat) {

			$operatorname = $stat['Operatelog']['operatorname'];
			$operatetype = $stat['Operatelog']['operatetype'];
			$operateresult = intval($stat['Operatelog']['operateresult']);
			$operatesystemid = intval($stat['Operatelog']['systemid']);

			$key = $operatorname . "_" . $operatetype . "_" . $operatesystemid;

			if (!isset($stats_user_logs[$key])) {
				$stats_user_logs[$key] = array(
					"operatorname" => $operatorname,
					"operatetype" => $operatetype,
					"total" => 0,
					"total_length" => 0,
					"total_backed" => 0,
					"total_length_backed" => 0,
					"total_finished" => 0,
					"total_length_finished" => 0
					);
			}

	    //产量：只有通过节目才算产量
			if ($operateresult === 0) {
				$stats_user_logs[$key]['total'] += $stat[0]['total'];
				$stats_user_logs[$key]['total_length'] += $stat[0]['total_length'];
			}

	    //打回量：打回统计只统计非审核任务不通过状态总数
//            if ($operateresult === 1) {
	    //modify 20131209 打回就显示打回的
			if ($operateresult === 2) {
				$stats_user_logs[$key]['total_backed'] += $stat[0]['total'];
				$stats_user_logs[$key]['total_length_backed'] += $stat[0]['total_length'];
			}
	    //工作量：所有状态都计算工作量
			$stats_user_logs[$key]['total_finished'] += $stat[0]['total'];
			$stats_user_logs[$key]['total_length_finished'] += $stat[0]['total_length'];
	    //平台id
			$stats_user_logs[$key]['systemid'] = $stat['Operatelog']['systemid'];
		}

	//对结果排序
	//add 20140128 默认为按操作类型排序
		if(!isset($this->passedArgs['sort'])) {
			$this->passedArgs['sort'] = 6;
		}
		if (isset($this->passedArgs['sort'])) {

			$sort_type = $this->passedArgs['sort'];
			$this->set("sort", $sort_type);

	    //按完成任务数排序
			if ($sort_type == 2) {
				usort($stats_user_logs, function($item1, $item2) {
					if ($item1['total'] == $item2['total']) {
						if ($item1['total_length'] == $item2['total_length']) {
							return 0;
						}
						return $item1['total_length'] < $item2['total_length'] ? 1 : -1;
					}
					return $item1['total'] < $item2['total'] ? 1 : -1;
				});
			}

	    //按完成任务时长排序
			elseif ($sort_type == 3) {
				usort($stats_user_logs, function($item1, $item2) {
					if ($item1['total_length'] == $item2['total_length']) {
						if ($item1['total'] == $item2['total']) {
							return 0;
						}
						return $item1['total'] < $item2['total'] ? 1 : -1;
					}
					return $item1['total_length'] < $item2['total_length'] ? 1 : -1;
				});
			}

	    //按打回任务数排序
			elseif ($sort_type == 4) {
				usort($stats_user_logs, function($item1, $item2) {
					if ($item1['total_backed'] == $item2['total_backed']) {
						if ($item1['total'] == $item2['total']) {
							return 0;
						}
						return $item1['total'] < $item2['total'] ? 1 : -1;
					}
					return $item1['total_backed'] < $item2['total_backed'] ? 1 : -1;
				});
			}

	    //按打回任务时长排序
			elseif ($sort_type == 5) {
				usort($stats_user_logs, function($item1, $item2) {
					if ($item1['total_length_backed'] == $item2['total_length_backed']) {
						if ($item1['total'] == $item2['total']) {
							return 0;
						}
						return $item1['total'] < $item2['total'] ? 1 : -1;
					}
					return $item1['total_length_backed'] < $item2['total_length_backed'] ? 1 : -1;
				});
			} elseif ($sort_type == 6) {
				$stats_user_logs = $this->operatetype($stats_user_logs);
				usort($stats_user_logs, function($item1, $item2) {
					if ($item1['operatetype_order'] == $item2['operatetype_order']) {
						if ($item1['systemid'] == $item2['systemid']) {
							return 0;
						}
						return $item1['systemid'] > $item2['systemid'] ? 1 : -1;
					}
					return $item1['operatetype_order'] > $item2['operatetype_order'] ? 1 : -1;
				});
			}
		}
		if ($export == 'excel') {

			$headers = array(
				"operatorname" => array("操作人员", 15),
				"operatetype" => array("完成操作", 15),
				"total" => array("生产任务数量", 15),
				"total_length" => array("生产任务时长", 15),
				"total_backed" => array("打回任务数量", 15),
				"total_length_backed" => array("打回任务时长", 15),
				"total_finished" => array("总工作量", 15),
				"total_length_finished" => array("总工作时长", 15),
				"systemid" => array("平台", 15)
				);
			foreach ($stats_user_logs as $i => $v) {
				$stats_user_logs[$i]['total_length'] = format_length($stats_user_logs[$i]['total_length']);
				$stats_user_logs[$i]['total_length_backed'] = format_length($stats_user_logs[$i]['total_length_backed']);
				$stats_user_logs[$i]['total_length_finished'] = format_length($stats_user_logs[$i]['total_length_finished']);
				$stats_user_logs[$i]['operatetype'] = isset($this->operatetypes[$stats_user_logs[$i]['operatetype']]) ? $this->operatetypes[$stats_user_logs[$i]['operatetype']] : $stats_user_logs[$i]['operatetype'];
				$stats_user_logs[$i]['systemid'] = $this->platform[$v['systemid']];
			}
			if ($this->start_date && $this->end_date) {
				$export_name = "操作统计(" . $this->start_date . "至" . $this->end_date . ").xlsx";
			} else {
				$export_name = "操作统计.xlsx";
			}
			return $this->export_excel("操作统计", $headers, $stats_user_logs, $export_name);
		}

		$this->set("stats_users", $stats_user_logs);
		$this->set("action", "show_stats");
	}

    /**
     * 生成Excel并下载
     *
     */
    public function export_excel($title, $headers, $lines, $filename) {
    	$this->autoRender = false;

    	PHPExcel_Shared_Font::setAutoSizeMethod(PHPExcel_Shared_Font::AUTOSIZE_METHOD_EXACT);

	// 创建一个处理对象实例  
    	$objExcel = new PHPExcel();

	//*************************************  
	//设置文档基本属性  
    	$objProps = $objExcel->getProperties();
    	$objProps->setCreator("Sobey");
    	$objProps->setLastModifiedBy("Sobey");

    	$objExcel->getActiveSheet()->setTitle($title);

	//第一行为标题
	// $objExcel->getActiveSheet()->fromArray($headers, null, "A1");

    	$col = 'A';
    	foreach ($headers as $header) {

    		$objExcel->getActiveSheet()->setCellValue($col . '1', $header[0]);
    		$objExcel->getActiveSheet()->getColumnDimension($col)->setWidth($header[1]);

    		$col++;
    	}

    	$rowNumber = 2;
    	foreach ($lines as $line) {
    		$col = 'A';
    		foreach ($headers as $head_id => $head_title) {

    			if (isset($line[$head_id])) {
    				$item = $line[$head_id];
    			} else {
    				$item = "";
    			}
    			//区分log 和 stats
    			$is_stats = ($this->action !== 'stats');
    			if((($this->specialFormat($col)) && !$is_stats) || ($col === 'B' && $is_stats)) {
    				$objExcel->getActiveSheet()->setCellValue($col . $rowNumber, $this->formatHour($item));
    				$objExcel->getActiveSheet()->getStyle($col . $rowNumber)->getNumberFormat()->setFormatCode('[h]:mm:ss');
    			}else{
    				$objExcel->getActiveSheet()->setCellValue($col . $rowNumber, $item);
    			}
    			$col++;
    		}
    		$rowNumber++;
    	}

    	$objExcel->getActiveSheet()->calculateColumnWidths();

    	$objWriter = new PHPExcel_Writer_Excel2007($objExcel);

    	header("Content-Type: application/force-download");
    	header("Content-Type: application/octet-stream");
    	header("Content-Type: application/download");
    	header('Content-Disposition:inline;filename="' . iconv("utf-8", "gbk", $filename) . '"');
    	header("Content-Transfer-Encoding: binary");
    	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    	header("Pragma: no-cache");

    	$objWriter->save('php://output');
    }
    /**
     * 需要格式化的列
     * @param  [str] $key 如D 、E
     * @return bool 
     */
    function specialFormat($key) {
    	$array = array('D','F','H');
    	return in_array($key, $array);
    }
    //operatetype重新排序
    function operatetype($array) {
    	if ($array) {
    		foreach ($array as $k => &$v) {
    			switch ($v['operatetype']) {
    				case '2':
    				$v['operatetype_order'] = 1;
    				break;
    				case '10':
    				$v['operatetype_order'] = 2;
    				break;
    				case '1':
    				$v['operatetype_order'] = 3;
    				break;
    				case '12':
    				$v['operatetype_order'] = 4;
    				break;
    				case '3':
    				$v['operatetype_order'] = 5;
    				break;
    				case '11':
    				$v['operatetype_order'] = 6;
    				break;
    				default:
    				$v['operatetype_order'] = 7;
    			}
    		}
    	}
    	return $array;
    }
    /**
     * 格式化时分秒
     * @param  [str] $value [description]
     * @return [str]        [description]
     */
    function formatHour($value) {
    	$value = explode(':',$value);
    	return  (intval($value[0])/24)+(intval($value[1])/(24*60))+(intval($value[2])/(24*60*60));
    }

}