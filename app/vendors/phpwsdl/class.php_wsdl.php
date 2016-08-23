<?php

/**
 * 根据指定类生成SOAP WSDL文件
 *
 * @version 0.2
 * @author Jerry lxb429@foxmail.com
 * @example PhpWsdl::genWSDL("ClassName", "http://localhost/soap")
 */

class PhpWsdl
{
	/**
	 * 匹配函数参数说明
	 * 格式：传入参数 @param string $name doc
	 *      返回    @return string $name doc
	 *
	 */
	public static $ParseKeywordsRx='/^(\s*\*\s*\@([^\s|\n]+)([^\n]*))$/m';

	/**
	 * 匹配函数说明注释
	 *
	 */
	public static $ParseDocsRx='/^[^\*|\n]*\*[ |\t]+([^\*|\s|\@|\/|\n][^\n]*)?$/m';

	/**
	 * 生成WSDL方法
	 *
	 * @param string $className 提供Soap处理的类名
	 * @param string $location Soap server 地址
	 * @param string $namespace 名称空间默认为 $localtion
	 * @param string $style binding style, document or rpc
	 * @param bool $encoded 是否需要编码
	 * @return string 返回与在的WSDL xml字符串
	 */
	public static function genWSDL($className, $location, $namespace=null,$methodNames=array(),
	$style="document", $encoded=false)
	{
		if (!$namespace) $namespace = $location;

		$elementTypes = '';
		$messageMethods = '';
		$portTypeOperations = '';
		$bindingOperations = '';

		$style = in_array($style, array("document", "rpc")) ? $style : "document";
		$use   = $encoded ? "encoded" : "literal";

		$class = new ReflectionClass($className);

		$methods = $class->getMethods(ReflectionMethod::IS_PUBLIC);
		foreach($methods as $methodKey => $methodValue) {
			if (!in_array($methodValue->name, $methodNames)){
				continue;
			}

			//跳过构造和析构函数
			if (substr($methodValue->name, 0, 2) == "__" || substr($methodValue->name, 0, 2) == "_" || $methodValue->name == $className)
			continue;

			//分析注释，获取参数类型和说明
			$param_types = array();
			$return_types = array();
			if (preg_match_all(PhpWsdl::$ParseKeywordsRx, $methodValue->getDocComment(), $matches)) {

				if (isset($matches[2]) && isset($matches[3])) {

					for ($i=0; $i < count($matches[2]); $i++) {
						$value = trim($matches[2][$i]);

						if (isset($matches[3][$i])) {

							$var = explode(" ", trim($matches[3][$i]));
							if (count($var) >= 2) {

								$var_name = substr(trim($var[1]), 1);
								$var_value = array(
                                                'name'=>$var_name,
                                                'type'=>PhpWsdl::getType($var[0]),
                                                'doc'=>implode(" ", array_slice($var, 2)),
								);

								if ($value == 'param') {
									$param_types[$var_name] = $var_value;
								}
								elseif ($value == 'return') {
									$return_types[$var_name] = $var_value;
								}
							}
						}
					}
				}
			}

			$doc_comment = null;
			if(preg_match_all(PhpWsdl::$ParseDocsRx, $methodValue->getDocComment(), $matches)) {
				$doc_comment = $matches[1][0];
			}

			$messageMethodParts  = '';
			if ($style == "document") {

				$paramTypesElements  = '';
				$resurnTypesElements = '';

				//传入参数
				foreach ($param_types as $param) {
					$paramTypesElements .= '<s:element minOccurs="0" maxOccurs="1" name="'.$param['name'].'" type="'.$param['type'].'" />';
				}

				//返回参数
				foreach ($return_types as $param) {
					$resurnTypesElements .= '<s:element minOccurs="0" maxOccurs="1" name="'.$param['name'].'" type="'.$param['type'].'" />';
				}

				$elementTypes .= '<s:element name="'.$methodValue->name.'">
          <s:complexType>
            <s:sequence>
              '.$paramTypesElements.'
            </s:sequence>
          </s:complexType>
        </s:element>
        <s:element name="'.$methodValue->name.'Response">
          <s:complexType>
            <s:sequence>
              '.$resurnTypesElements.'
            </s:sequence>
          </s:complexType>
        </s:element>';

				$messageMethods .= '<wsdl:message name="'.$methodValue->name.'SoapIn">
            <wsdl:part name="parameters" element="tns:'.$methodValue->name.'" />
        </wsdl:message>
        <wsdl:message name="'.$methodValue->name.'SoapOut">
        <wsdl:part name="parameters" element="tns:'.$methodValue->name.'Response"/>
        </wsdl:message>';

			}
			else { //$style == "rpc"
				$params = $methodValue->getParameters();
				foreach($params as $paramKey => $paramValue) {

					$type = isset($param_types[$paramValue->name]) ? $param_types[$paramValue->name]['type'] : PhpWsdl::getType();

					if (isset($param_types[$paramValue->name]) && isset($param_types[$paramValue->name]['doc'])) {
						$messageMethodParts .= '<wsdl:documentation xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/">'.$param_types[$paramValue->name]['doc'].'</wsdl:documentation>';
					}

					$messageMethodParts .= '<wsdl:part name="'.$paramValue->name.'" type="'.$type.'" />';
				}

				$return_name = "result";
				$return_type = array_pop($return_types);

				if ($return_type) {
					$return_name = $return_type['name'];
					$return_type = $return_type['type'];
				}
				else {
					$return_type = PhpWsdl::getType();
				}

				$messageMethods .= '<wsdl:message name="'.$methodValue->name.'SoapIn">
          '.$messageMethodParts.'
        </wsdl:message>
        <wsdl:message name="'.$methodValue->name.'SoapOut">
        <wsdl:part name="'.$return_name.'" type="'.$return_type.'"/>
        </wsdl:message>';
			}

			$portTypeOperations .= '<wsdl:operation name="'.$methodValue->name.'">
          <wsdl:documentation xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/">'.$doc_comment.'</wsdl:documentation>
          <wsdl:input message="tns:'.$methodValue->name.'SoapIn"/>
          <wsdl:output message="tns:'.$methodValue->name.'SoapOut"/>
        </wsdl:operation>';

			$bindingOperations .= '<wsdl:operation name="'.$methodValue->name.'">
          <soap:operation style="'.$style.'" soapAction="'.$namespace.'/'.$methodValue->name.'"/>
          <wsdl:input>
            <soap:body use="'.$use.'" '.($encoded ? 'namespace="'.$namespace.'" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"' : '').' />
          </wsdl:input>
          <wsdl:output>
            <soap:body use="'.$use.'" '.($encoded ? 'namespace="'.$namespace.'" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"' : '').' />
          </wsdl:output>
        </wsdl:operation>';
		}

		$s = '<?xml version ="1.0" encoding="utf-8"?>
        <wsdl:definitions xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/"
         xmlns:tm="http://microsoft.com/wsdl/mime/textMatching/"
         xmlns:soapenc="http://schemas.xmlsoap.org/soap/encoding/" 
         xmlns:mime="http://schemas.xmlsoap.org/wsdl/mime/" 
         xmlns:s="http://www.w3.org/2001/XMLSchema"
         xmlns:tns="'.$namespace.'"
         xmlns:http="http://schemas.xmlsoap.org/wsdl/http/" 
         targetNamespace="'.$namespace.'" 
         xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/">
        <wsdl:types>
         <s:schema xmlns="http://www.w3.org/2001/XMLSchema" attributeFormDefault="unqualified" 
            elementFormDefault="unqualified" targetNamespace="'.$namespace.'">
        '.$elementTypes.'
         </s:schema>
        </wsdl:types>
        '.$messageMethods.'
        <wsdl:portType name="'.$className.'Soap">
        '.$portTypeOperations.'
        </wsdl:portType>
        <wsdl:binding name="'.$className.'Soap" type="tns:'.$className.'Soap">
        <soap:binding transport="http://schemas.xmlsoap.org/soap/http" style="'.$style.'" />
        '.$bindingOperations.'
        </wsdl:binding>
        <wsdl:service name="'.$className.'">
        <wsdl:port name="'.$className.'Soap" binding="tns:'.$className.'Soap">
          <soap:address location="'.$location.'"/>
        </wsdl:port>
        </wsdl:service>
        </wsdl:definitions>';

		return $s;
	}

	/**
	 * php 类型换 XSD 类型
	 *
	 * @param string $type php type
	 * @return string xsd type
	 */
	public static function getType($type=null)
	{
		switch (strtolower($type)) {
			case 'string':
			case 'str':
				return 's:string';
			case 'long':
				return 's:long';
			case 'int':
			case 'integer':
				return 's:int';
			case 'float':
				return 's:float';
			case 'double':
				return 's:double';
			case 'boolean':
			case 'bool':
				return 's:boolean';
			case 'array':
				return 'soap-enc:Array';
			case 'object':
				return 's:struct';
			case 'mixed':
				return 's:anyType';
			case 'void':
				return '';
			default:
				return 's:string';
		}
	}
}