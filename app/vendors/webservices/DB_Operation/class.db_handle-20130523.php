<?php
class DbHandle extends Object{


	/**
	 *
	 * Enter description here ...
	 * @param string $sqlField
	 */
	public static function _changeDbFieldToChar($sqlField){
		if (strtoupper(DB_DRIVER) == 'DB2'){
			return "CHAR(".$sqlField.")";
		}
		elseif (strtoupper(DB_DRIVER) == 'ORACLE'){
			return "TO_CHAR(".$sqlField.")";
		}
		else {
			return "TO_CHAR(".$sqlField.")";
		}
	}
	public static function _getSearchDateFormat(){
		if (strtoupper(DB_DRIVER) == 'DB2'){
			return 'Y-m-d H:i:s';
		}
		elseif (strtoupper(DB_DRIVER) == 'ORACLE'){
			return 'Y/m/d';
		}
		else {
			return 'Y/m/d';
		}
	}
	public static function _getDbSetBlobStyle(){
		if (strtoupper(DB_DRIVER) == 'DB2'){
			return '?';
		}
		elseif (strtoupper(DB_DRIVER) == 'ORACLE'){
			return ':data';
		}
		else {
			return ':data';
		}
	}
}