<?php
App::import('Vendor', 'Array2Xml2Array', array('file' => 'xmlandarray' . DS . 'class.array2xml2array.php'));
class Content extends AppModel {

	public $name = 'Content';
	public $useTable = 'et_nmpgmauditlist';
	public $primaryKey = 'taskid';

	/**
	 * paginate
	 * 自定义分页(元数据)
	 */
	public function paginate($conditions,$fields,$limit=10, $page = 1,$recursive = null, $extra = array()){
		$BaseNum = ($page - 1) * $limit;
		$params = compact('conditions','fields'/*,'order','group'*/);
		$data = $this->find('first',$params);

		$tempData = $data['Content']['metedata'];
		if ($tempData){
			$tempArr = Array2Xml2Array::xml2array($tempData);

			$platForm = $tempArr['MeteData']['PlatFormInfos']['PlatFormInfo'];
			//$attributes = $tempArr['MeteData']['Attributes']['AttributeItem'];
			$attributeItems = $tempArr['MeteData']['Attributes']['AttributeItem'];

			$attributes = array();
			$allCount = 0;
			if ($limit == DEFAULT_IS_ALL_METAINFO){
				$attributes = $attributeItems;
			}
			else {	
				$attributess = array();				
				//获取配置中的设置项目
				$configAttributes= Configure::read('attributes');
				if ($configAttributes){
					if ($attributeItems){
						foreach ($attributeItems as $tempItem) {
							if (in_array($tempItem['ItemName'],$configAttributes)){
								$id = (int)array_search($tempItem['ItemName'], $configAttributes);
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
				for ($num = 0;$num < $limit; $num++){
					$currentNum = $BaseNum + $num;
					if ($currentNum >= $allCount){
						break;
					}
					else {
						$attributes[] = $allAttributes[$currentNum];
					}
				}
			}

			$returnArr = compact('platForm','attributes','allCount');
			return $returnArr;
		}
		return false;
	}
}