<?php
class BitOperation{

	/**
	 * 无符号32位右移
	 * @param mixed $x 要进行操作的数字，如果是字符串，必须是十进制形式
	 * @param string $bits 右移位数
	 * @return mixed 结果，如果超出整型范围将返回浮点数
	 */
	public static function shr32($x, $bits){
		// 位移量超出范围的两种情况
		if($bits <= 0){
			return $x;
		}
		if($bits >= 32){
			return 0;
		}
		//转换成代表二进制数字的字符串
		$bin = decbin($x);
		$l = strlen($bin);
		//字符串长度超出则截取底32位，长度不够，则填充高位为0到32位
		if($l > 32){
			$bin = substr($bin, $l - 32, 32);
		}elseif($l < 32){
			$bin = str_pad($bin, 32, '0', STR_PAD_LEFT);
		}
		//取出要移动的位数，并在左边填充0
		return bindec(str_pad(substr($bin, 0, 32 - $bits), 32, '0', STR_PAD_LEFT));
	}
	/**
	 * 无符号32位左移
	 * @param mixed $x 要进行操作的数字，如果是字符串，必须是十进制形式
	 * @param string $bits 左移位数
	 * @return mixed 结果，如果超出整型范围将返回浮点数
	 */
	public static function shl32 ($x, $bits){
		// 位移量超出范围的两种情况
		if($bits <= 0){
			return $x;
		}
		if($bits >= 32){
			return 0;
		}
		//转换成代表二进制数字的字符串
		$bin = decbin($x);
		$l = strlen($bin);
		//字符串长度超出则截取底32位，长度不够，则填充高位为0到32位
		if($l > 32){
			$bin = substr($bin, $l - 32, 32);
		}elseif($l < 32){
			$bin = str_pad($bin, 32, '0', STR_PAD_LEFT);
		}
		//取出要移动的位数，并在右边填充0
		return bindec(str_pad(substr($bin, $bits), 32, '0', STR_PAD_RIGHT));
	}
}
