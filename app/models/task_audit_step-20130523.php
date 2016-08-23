<?php
App::import('Vendor', 'ChangeEncode', array('file' => 'someoperation' . DS . 'class.change_encode.php'));
class TaskAuditStep extends AppModel {
	public $name = 'TaskAuditStep';
	public $useTable = 'et_nmpgmauditliststep';
	//public $primaryKey = 'workflowid';



	/**
	 * The sets of the primary keys field for this model.
	 *
	 * @var array
	 * @access public
	 */
	public $newPrimaryKey = array('taskid','auditlevel');

	/**
	 * The primary's data that this model to ser or get
	 *
	 * @var array
	 * @access public
	 */
	public $newPrimaryData = array();
	public $newData = array();
	public $newUpdateData = array();

	public function newSet($data = null){
		$tmPrimaryData = array();
		$tmUpdateData = array();
		foreach ($data as $oneKey=>$oneValue){
			if (in_array($oneKey, $this->newPrimaryKey)){
				$tmPrimaryData[$oneKey] = $oneValue;
			}
			else {
				$tmUpdateData[$oneKey] = $oneValue;
			}
		}
		$this->newData = $data;
		$this->newPrimaryData = $tmPrimaryData;
		$this->newUpdateData = $tmUpdateData;
		return $data;
	}
	/**
	 * new Saves model data to the Oracle database
	 *
	 * @param array $data Data to save.
	 */
	public function newSave(){
		
		$state = false;
		$theData = $this->newData;
		$theUpdateData = $this->newUpdateData;

		$isExist = $this->newExists();
		if ($isExist){
			$setString = "";
			$isFirst = true;
			foreach ($theUpdateData as $key=>$value){
				$space = ",";
				if ($isFirst){
					$isFirst = false;
					$space = "";
				}
				$setValueStr = $value;
				if ($key == 'auditorname' || $key == 'contentauditnote'){
					$setValueStr = "'".$value."'";
				}
				elseif ($key == 'taskauditdate'){
					//$setValueStr = "to_timestamp('".$value."','yyyy-mm-dd hh24:mi:ss.ff')";
					$setValueStr= "timestamp(char('$value'))";
				}

				$setString = $setString.$space.$key."=".$setValueStr;
			}

			$sqlStr = "update et_nmpgmauditliststep
					              set ".$setString."					                  
					              where taskid=".$theData['taskid']." and  auditlevel=".$theData['auditlevel'];
			$state = $this->newSetData(ChangeEncode::changeEncodeFromUTF8($sqlStr));
		}
		else {
			$keyString = "";
			$valueString = "";
			$isFirst = true;
			foreach ($theData as $key=>$value){
				$space = ",";
				if ($isFirst){
					$isFirst = false;
					$space = "";
				}
				$keyString = $keyString.$space.$key;

				$valueStr=$value;
				if ($key == 'auditorname' || $key == 'contentauditnote'){
					$valueStr = "'".$value."'";
				}
				elseif ($key == 'taskauditdate'){
					//$valueStr = "to_timestamp('".$value."','yyyy-mm-dd hh24:mi:ss.ff')";
					$valueStr = "timestamp(char('$value'))";
				}
				$valueString = $valueString.$space.$valueStr;
			}
			$sqlStr = "insert into
					           et_nmpgmauditliststep(".$keyString.")  
					           values(".$valueString.")";
			$state = $this->newSetData(ChangeEncode::changeEncodeFromUTF8($sqlStr));
		}
		return $state;
	}

	/**
	 * Returns true if a record with the currently set PrimaryKey-Value exists.
	 * @return boolean True if such a record exists
	 * @access public
	 */
	public function newExists(){
		$conditions = $this->newPrimaryData;
		$query = array('conditions' => $conditions, 'recursive' => -1, 'callbacks' => false);
		return ($this->find('count', $query) > 0);
	}
}