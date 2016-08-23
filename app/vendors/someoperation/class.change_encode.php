<?php
class ChangeEncode extends Object{

	/**
	 *
	 * 对UTF8数据进行转换
	 * @param mix $data
	 * @param string $tag 转换方式标志值。默认db，表示针对数据库数据进行操作。若不是，设置为normal
	 * @param string $charset 若$tag为normal,设置该值，应为输出编码方式
	 */
	public static function changeEncodeFromUTF8($data,$tag='db',$charset=null){
		if ($tag=='db'){
			if (strtoupper(DB_CHARSET) != 'UTF-8') {
				return ChangeEncode::changeStyle($data, "UTF-8", DB_CHARSET);
			}
			return $data;
		}
		else {
			return ChangeEncode::changeStyle($data, "UTF-8", $charset);
		}
	}
	/**
	 *
	 * 将数据转换成UTF8
	 * @param mix $data
	 * @param string $tag
	 * @param string $charset 若$tag为normal,设置该值，应为进入编码方式
	 */
	public static function changeEncodeToUTF8($data,$tag='db',$charset=null){
		if ($tag=='db'){			
			if (strtoupper(DB_CHARSET) != 'UTF-8') {
				
				return ChangeEncode::changeStyle($data, DB_CHARSET, "UTF-8");
			}
			return $data;
		}
		else {
			return ChangeEncode::changeStyle($data,$charset, "UTF-8");
		}
	}
	private static function changeStyle($data,$in_charset,$out_charset){
		if (is_array($data)){
			return ChangeEncode::array_iconv($data,$in_charset,$out_charset);
		}
		return  mb_convert_encoding($data, $out_charset, $in_charset);
	}
	//转换数组的编码方式
	private static function array_iconv($arr,$in_charset,$out_charset){
		return eval('return '.iconv($in_charset,$out_charset,var_export($arr,true).';'));
	}
}