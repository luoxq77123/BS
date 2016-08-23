<?php
App::import('Vendor', 'IPReflectionClass', array('file' => 'wshelper' . DS . 'lib' . DS . 'soap' . DS . 'IPReflectionClass.class.php'));
App::import('Vendor', 'IPReflectionCommentParser', array('file' => 'wshelper' . DS . 'lib' . DS . 'soap' . DS . 'IPReflectionCommentParser.class.php'));
App::import('Vendor', 'IPXMLSchema', array('file' => 'wshelper' . DS . 'lib' . DS . 'soap' . DS . 'IPXMLSchema.class.php'));
App::import('Vendor', 'IPReflectionMethod', array('file' => 'wshelper' . DS . 'lib' . DS . 'soap' . DS . 'IPReflectionMethod.class.php'));
App::import('Vendor', 'WSDLStruct', array('file' => 'wshelper' . DS . 'lib' . DS . 'soap' . DS . 'WSDLStruct.class.php'));
App::import('Vendor', 'WSDLException', array('file' => 'wshelper' . DS . 'lib' . DS . 'soap' . DS . 'WSDLException.class.php'));

/**
 * Class SoapComponent
 *
 * Generate WSDL and handle SOAP calls
 */
class SoapComponent extends Component
{
	var $params = array();
	function beforeRedirect(){
		 
	}
	function initialize(&$controller)
	{
		$this->params = $controller->params;
	}

	/**
	 * Get WSDL for specified Class.
	 *
	 * @param string $wsClass : class name in camel case
	 * @param string $serviceMethod : method of the controller that will handle SOAP calls
	 */
	function getWSDL($classId, $serviceMethod = 'call')
	{
		App::import('Vendor', 'webservices/'.$classId);
		$wsClass = $this->__getClassName($classId);
		$expireTime = '+1 year';
		$cachePath = $wsClass . '.wsdl';

		// Check cache if exist
		$wsdl = cache($cachePath, null, $expireTime);

		// If DEBUG > 0, compare cache modified time to class file modified time
		if ((Configure::read() > 0) && (! is_null($wsdl))) {

			$cacheFile = CACHE . $cachePath;
			if (is_file($cacheFile)) {
				$classMtime = @filemtime($this->__getClassFile($classId));
				$cacheMtime = @filemtime(CACHE . $cachePath);
				if ($classMtime > $cacheMtime) {
					$wsdl = null;
				}
			}

		}

		// Generate WSDL if not cached
		if (is_null($wsdl)) {

			$refl = new IPReflectionClass($wsClass);

			$controllerName = $this->params['controller'];
			$serviceURL = Router::url("/$controllerName/$serviceMethod", true);

			$theUrl = "http://sobey.com";
			$actionUrl = $theUrl."/".$controllerName."/".$serviceMethod;

			//			$wsdlStruct = new WSDLStruct(Router::url('/', true),
			//			                    $serviceURL . '/' . $classId,
			//			                    SOAP_RPC,
			//			                    SOAP_LITERAL);
			$wsdlStruct = new WSDLStruct($theUrl,
			$actionUrl . '/' . $classId,
			$serviceURL . '/' . $classId,
			SOAP_RPC,
			SOAP_LITERAL);
			$wsdlStruct->setService($refl);
			try {
				$wsdl = $wsdlStruct->generateDocument();
				cache($cachePath, $wsdl, $expireTime);
			} catch (WSDLException $exception) {
				if (Configure::read() > 0) {
					$exception->Display();
					exit();
				} else {
					return null;
				}
			}
		}

		return $wsdl;
	}

	/**
	 * Handle SOAP service call
	 *
	 * @param string $classId : underscore notation of the called class
	 *                          without _service ending
	 * @param string $wsdlMethod : method of the controller that will generate the WSDL
	 */
	function handle($classId, $wsdlMethod = 'wsdl')
	{
		$wsClass = $this->__getClassName($classId);
		$wsdlCacheFile = CACHE . $wsClass . '.wsdl';

		// Try to create cache file if not exists
		if (! is_file($wsdlCacheFile)) {
			$this->getWSDL($classId);
		}

		if (is_file($wsdlCacheFile)) {
			$server = new SoapServer($wsdlCacheFile);
		} else {
			$controllerName = $this->params['controller'];
			$wsdlURL = Router::url("/$controllerName/$wsdlMethod", true);
			$server = new SoapServer($wsdlURL . '/' . $classId);
		}
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

	/**
	 * Get class id for specified class
	 *
	 * @access private
	 * @return string : the class id
	 */
	function __getClassId($wsClass)
	{
		$inflector = new Inflector;
		return $inflector->underscore(substr($class, 0, -7));
	}

	/**
	 * Get class file for specified id
	 *
	 * @access private
	 * @return string : the filename
	 */
	function __getClassFile($classId)
	{
		$classDir = dirname(dirname(dirname(__FILE__))) . DS . 'vendor' . DS . 'webservices';
		return $classDir . DS . $classId . '.php';
	}
}
?>