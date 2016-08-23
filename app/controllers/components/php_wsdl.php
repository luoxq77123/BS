<?php

/**
 * 根据指定类生成SOAP WSDL文件
 *
 */
App::import('Vendor', 'PhpWsdl', array('file' => 'phpwsdl' . DS . 'class.php_wsdl.php'));

class PhpWsdlComponent extends Component
{
	public  $params = array();
	function beforeRedirect(){
		 
	}
	function initialize(&$controller)
	{
		$this->params = $controller->params;
	}
	/**
	 * 外部调用生成WSDL
	 *
	 * @param string $classId : class file name in camel case
	 * @param string $serviceMethod : method of the controller that will handle SOAP calls
	 */
	public function getWSDL($classId, $serviceMethod="call"){
			
		App::import('Vendor', 'webservices/'.$classId);
		$className = $this->__getClassName($classId);

		$controllerName = $this->params['controller'];

		$location = Router::url("/$controllerName/$serviceMethod/$classId", true);
		$namespace = "http://www.sobey.com";
		return PhpWsdl::genWSDL($className, $location,$namespace,array('getTaskWithWorkFlowID','getTaskWithPgmGUID'));
	}
	/**
	 * Handle SOAP service call
	 *
	 * @param string $classId : underscore notation of the called class
	 *                          without _service ending
	 * @param string $wsdlMethod : method of the controller that will generate the WSDL
	 */
	public function handle($classId, $wsdlMethod = 'wsdl')
	{
		$wsClass = $this->__getClassName($classId);

		$controllerName = $this->params['controller'];
		$wsdlURL = Router::url("/$controllerName/$wsdlMethod", true);
		$server = new SoapServer($wsdlURL . '/' . $classId);

		App::import('Vendor', 'webservices/'.$classId);
		$server->setClass($wsClass);
		$server->handle();
	}
	/**
	 * Get class for specified class id
	 *
	 * @access private
	 * @return string : the class id
	 */
	function __getClassName($classId)
	{
		$inflector = new Inflector;
		return ($inflector->camelize($classId));
	}
}