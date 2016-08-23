<?php
class NumericArray{

	public static function toNumericArray($valueIn){
		$valueOut = $valueIn;
		if (is_array($valueIn)){
			if (!isset($valueIn[0]) && $valueIn){
				$valueOut = array($valueIn);
			}
		}
		else {
			if(is_string($valueIn)){
				if ($valueIn || $valueIn == '0'){
					$valueOut = array($valueIn);
				}
			}
			if (is_integer($valueIn)){
				if ($valueIn || $valueIn == 0){
					$valueOut = array($valueIn);
				}
			}
		}
		return $valueOut;
	}
}