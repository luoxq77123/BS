<?php
App::import('Vendor', 'Array2Xml2Array', array('file' => 'xmlandarray' . DS . 'class.array2xml2array.php'));
App::import('Vendor', 'NumericArray', array('file' => 'someoperation' . DS . 'class.numeric_array.php'));
class Tech extends AppModel{

	public $name = 'Tech';
	public $useTable = 'et_nmpgmauditfilelist';
	//public $useTable = 'smm_userinfo';

	/**
	 * paginate
	 * 自定义分页
	 * @param mixed $conditions
	 * @param mixed $fields
	 * @param mixed $limit
	 * @param mixed $page
	 * @param mixed $recursive
	 * @param mixed $extra
	 */
	public function paginate($conditions,$fields,$limit=10, $page = 1, $recursive = null, $extra = array()){
		$BaseNum = ($page - 1) * $limit;
		$params = compact('fields','conditions');
		$data = $this->find('all',$params);

		if ($data){
			$allBugArr = array();
			foreach ($data as $tempData){
				$theClass = (int)$tempData['Tech']['clipclass'];
				$tempBug = $tempData['Tech']['techauditbugdata'];

				$bugArr = Array2Xml2Array::xml2array($tempBug);
				if ($bugArr){
					$theBugD = $bugArr['BugItems'];
					if ($theBugD){
						$theBug = $theBugD['BugItem'];							
						if ($theBug){
							$theBug = NumericArray::toNumericArray($theBug);

							foreach ($theBug as $tempArr){
								$tempArr['ClipClass'] = $theClass;
								$allBugArr[] = $tempArr;
							}
						}
					}
				}
			}

			//统计总数
			$allBugArr = NumericArray::toNumericArray($allBugArr);

			$allCount = count($allBugArr);

			//分页返回的Bug
			$bugDataArr = array();
			for ($num = 0;$num < $limit; $num++){
				$currentNum = $BaseNum + $num;
				if ($currentNum >= $allCount){
					break;
				}
				else {
					$bugDataArr[] = $allBugArr[$currentNum];
				}
			}
			$returnArr = compact('allCount','bugDataArr');
			return $returnArr;
		}
		else {
			return false;
		}
	}

	/**
	 * paginateCount
	 * 获取数据总数量
	 * @param mixed $fields
	 * @param mixed $conditions
	 * @param mixed $recursive
	 * @param mixed $extra
	 */
	public function paginateCount($conditions,$fields,$recursive = null, $extra = array()){

		$params = compact('fields','conditions');
		$data = $this->find('all',$params);

		if ($data){
			$allBugArr = array();
			foreach ($data as $tempbugData){
				$bugOne = $tempbugData['Tech']['techauditbugdata'];

				$arr = Array2Xml2Array::xml2array($bugOne);

				$bugArr = $arr['BugItems']['BugItem'];
				$bugArr = NumericArray::toNumericArray($bugArr);

				foreach ($bugArr as $value) {
					$allBugArr[] = $value;
				}
			}

			if (!isset($allBugArr[0]) && $allBugArr){
				$allBugArr = array($allBugArr);
			}
			$count = count($allBugArr);
			return $count;
		}
		else {
			return 0;
		}
	}
}