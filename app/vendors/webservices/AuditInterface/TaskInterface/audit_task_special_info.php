<?php
App::import('Model', 'Content');
App::import('Model', 'Tech');
App::import('Vendor', 'ChangeEncode', array('file' => 'someoperation' . DS . 'class.change_encode.php'));
class AuditTaskSpecialInfo extends Object{

	public $content;
	public $tech;

	public function __construct(){
		$this->content = new Content();
		$this->tech = new Tech();
	}

	public function getMetaDataListWithID($theParams){
		$taskID = (int)$theParams['TaskID'];
		if (!$taskID){
			return false;
		}

		$page = (int)$theParams['CurrentPage'];
		$limit = (int)$theParams['PageSize'];

		$fieldArray = 'metedata';
		$conditionArray = array('taskid'=>$taskID);
		$data = $this->content->paginate($conditionArray,$fieldArray,$limit,$page);
		if ($data){
			$thePlatForm = $data['platForm'];
			$results = $data['attributes'];
			
			$paging = array();				
			if ($limit != DEFAULT_IS_ALL_METAINFO){
				$count = $data['allCount'];				
				if ($count){
					//分页相关
					$pageCount = intval(ceil($count / $limit));
					$defaults = array('page' => 1,'limit' => $limit);
					$options = array_merge(array('page'=>1, 'limit'=>10), $defaults, array('page'=>$page));
					$paging = array(
			                'page'	    => $page,
			                'current'   => count($results),
			                'count'	    => $count,
			                'prevPage'  => ($page > 1),
			                'nextPage'  => ($count > ($page * $limit)),
			                'pageCount' => $pageCount,
			                'defaults'  => array_merge(array('limit'=>10, 'step' => 1), $defaults),
			                'options'   => $options
					);
				}
				
			}
			return array('PlatFormInfo'=>$thePlatForm,'AttributeItem'=>$results,'Paging'=>$paging);
		}
		$this->log('获取元数据：获取元数据失败');
		return false;
	}

	public function getTechAuditListWithID($theParams){
		$taskID = (int)$theParams['TaskID'];
		$fileAlias = $theParams['FileAlias'];

		$page = (int)$theParams['CurrentPage'];
		$limit = (int)$theParams['PageSize'];

		if (!$taskID){
			return false;
		}

		$conditions = array('taskid' => $taskID);
		if ($fileAlias != DEFAULT_IS_ALL_TECHINFO){
			$conditions = array_merge($conditions,array('filealias'=>$fileAlias));
		}
		$fields = array('clipclass','techauditbugdata');

		$conditions = ChangeEncode::changeEncodeToUTF8($conditions);
		$data = $this->tech->paginate($conditions,$fields,$limit,$page);
		if ($data){
			$count = $data['allCount'];
			if ($count){
				$results = $data['bugDataArr'];

				//分页相关
				$pageCount = intval(ceil($count / $limit));
				$defaults = array('page' => 1,'limit' => $limit);
				$options = array_merge(array('page'=>1, 'limit'=>10), $defaults, array('page'=>$page));
				$paging = array(
			      'page'	  => $page,
			      'current'   => count($results),
			      'count'	  => $count,
			      'prevPage'  => ($page > 1),
			      'nextPage'  => ($count > ($page * $limit)),
			      'pageCount' => $pageCount,
			      'defaults'  => array_merge(array('limit'=>10, 'step' => 1), $defaults),
			      'options'	  => $options
				);
				return array('Paging'=>$paging,'BugData'=>$results);
			}
		}
		else {
			$this->log('获取技审信息：获取技审信息失败');
		}
		return false;
	}
	public function getCodeRatesWithID($taskID){
		if (!$taskID){
			return false;
		}

		$conditionArray = array('taskid' => $taskID);
		$fileFieldArray = array('filealias');
		$order = 'filealias DESC';
		$group = array('filealias');

		$codeRates = $this->tech->find('all',array('fields' => $fileFieldArray,'conditions' => $conditionArray,'order'=>$order,'group'=>$group));
		if ($codeRates){
			$fileAliasArr = array();
			foreach ($codeRates as $fileAlias){
				$fileAliasArr[] = $fileAlias['Tech']['filealias'];
			}
			return $fileAliasArr;
		}
		$this->log('获取码率：获取任务文件码率失败');
		return false;
	}
	public function getTaskFilesWithID($theParams){
		$taskID = (int)$theParams['TaskID'];
		$fileAlias = $theParams['FileAlias'];
		if (!$taskID){
			return false;
		}

		$conditionArray = array('taskid' => $taskID,'filealias'=>$fileAlias);
		$fileFieldArray = array('taskid','filealias','filename','clipclass','channel');
        $order = 'clipclass ASC';
		
		$taskFiles = $this->tech->find('all',array('fields'=>$fileFieldArray,'conditions'=>$conditionArray,'order'=>$order));
		if ($taskFiles){
			$taskFileArray = array();
			foreach ($taskFiles as $taskFile){
				$taskFileArray[] = $taskFile['Tech'];
			}
			return $taskFileArray;
		}
		$this->log('获取文件信息：获取任务文件失败');
		return false;
	}
}