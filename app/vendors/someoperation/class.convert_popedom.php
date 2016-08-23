<?php
class ConvertPopedom{
	
	
	public static function convertPopedomToArray($popedom){
		$popedomNum = (int)$popedom;

		//转换成代表二进制数字的字符串
		$bin = decbin($popedomNum);
		$theNumArr = str_split(strrev($bin));
		$theCount = count($theNumArr);

		$popedomArray = array();
		for ($i=0; $i < $theCount; $i++){
			if ($theNumArr[$i]=='1'){
				$popedomArray[]=$i;
			}
		}
		return $popedomArray;
	}
}