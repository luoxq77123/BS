<?php
/**
 * 
 * xml与array转换
 * @author CHEN
 *
 */
App::import('Vendor', 'Array2Xml2Array', array('file' => 'xmlandarray' . DS . 'class.array2xml2array.php'));
class XmlArrayComponent extends Component{
    
	function beforeRedirect(){
    	
    }
    /**
     * 
     * xml转换成数组
     * @param unknown_type $data
     */
    public function xml2array($data){
    	return Array2Xml2Array::xml2array($data);
    }
    /**
     * 
     * 数组转化成xml
     * @param unknown_type $data
     * @param unknown_type $root
     */
    public function array2xml($data,$root){
    	$toXml = new Array2Xml2Array($root);
    	return $toXml->array2Xml($data);
    }
}