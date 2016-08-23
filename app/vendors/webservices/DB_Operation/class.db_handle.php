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
	public static function _getTimestampFormat($value){
		if (strtoupper(DB_DRIVER) == 'DB2'){
			return "timestamp(char('$value'))";
		}
		elseif (strtoupper(DB_DRIVER) == 'ORACLE'){
			return "to_timestamp('".$value."','yyyy-mm-dd hh24:mi:ss.ff')";
		}
		else {
			return "to_timestamp('".$value."','yyyy-mm-dd hh24:mi:ss.ff')";
		}
	}
	public static function _getOracleOrderSql($sql){
		if (strtoupper(DB_DRIVER) == 'DB2'){
			return $sql;
		}
		elseif (strtoupper(DB_DRIVER) == 'ORACLE'){
			return "SELECT * FROM (".
			        $sql.
		           ")ORDER BY NLSSORT(COLUMN_NAME,'NLS_SORT=SCHINESE_PINYIN_M')";
		}
		else {
			return "SELECT * FROM (".
			$sql.
		           ")ORDER BY NLSSORT(COLUMN_NAME,'NLS_SORT=SCHINESE_PINYIN_M')";
		}
	}
}