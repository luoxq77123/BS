<?php
class ViewOperationHelper extends Helper{

	public function formatTimeLength($length){
		$theLength = (int)$length;

		$frame = $theLength%TASK_FRAME_NUM;
		$seconds = $theLength/TASK_FRAME_NUM;

		$frameStr = ($frame<10) ? ('0'.$frame) : $frame;
		$secondsStr = gmstrftime('%H:%M:%S',$seconds);
		$theFormat = $secondsStr.':'.$frameStr;
		return $theFormat;
	}
//	//截取含中文字符的字符串
//	public function subStringFormat($sting,$start,$lengh){
//		$subStrLen = mb_strlen($sting,'UTF-8');
//		$subString = '';
//		if ($subStrLen > $lengh){
//			$subString = mb_substr($sting, $start,$lengh,'UTF-8');
//			$subString = $subString.'...';
//		}
//		else {
//			$subString = $sting;
//		}
//		return $subString;
//	}
	/**
	 * 
	 * utf8格式下的中文字符截断
	 * @param unknown_type $sourcestr 是要处理的字符串
	 * @param unknown_type $cutlength 为截取的长度(即字数)
	 * @param unknown_type $addstr 超过长度时在尾处加上的字符
	 */
	public  static function subStringFormat($sourcestr, $cutlength, $addstr='...'){
		$returnstr='';
		$i=0;
		$n=0;
		$str_length=strlen($sourcestr);//字符串的字节数
		while (($n<$cutlength) and ($i<=$str_length)){
			$temp_str=substr($sourcestr,$i,1);
			$ascnum=Ord($temp_str);//得到字符串中第$i位字符的ascii码
			if ($ascnum>=224){ //如果ASCII位高与224，
				$returnstr=$returnstr.substr($sourcestr,$i,3); //根据UTF-8编码规范，将3个连续的字符计为单个字符
				$i=$i+3; //实际Byte计为3
				$n++; //字串长度计1
			}elseif ($ascnum>=192){ //如果ASCII位高与192，
				$returnstr=$returnstr.substr($sourcestr,$i,2); //根据UTF-8编码规范，将2个连续的字符计为单个字符
				$i=$i+2; //实际Byte计为2
				$n++; //字串长度计1
			}elseif ($ascnum>=65 && $ascnum<=90){ //如果是大写字母，
				$returnstr=$returnstr.substr($sourcestr,$i,1);
				$i=$i+1; //实际的Byte数仍计1个
				$n++; //但考虑整体美观，大写字母计成一个高位字符
			}else{ //其他情况下，包括小写字母和半角标点符号，
				$returnstr=$returnstr.substr($sourcestr,$i,1);
				$i=$i+1; //实际的Byte数计1个
				$n=$n+0.5; //小写字母和半角标点等与半个高位字符宽...
			}
		}
		if ($str_length>$cutlength){
			$returnstr = $returnstr . $addstr;//超过长度时在尾处加上的字符
		}
		return $returnstr;
	}
	public function  getPicPath($filePath){
		if (HTTP_LOCATION=='null'){
			return  $filePath;
		}
		else {
			return str_replace('\\', '/', substr_replace($filePath, HTTP_LOCATION, 0,2));
		}
	}
}