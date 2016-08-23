<?php
class SoapServiceWsdlController extends AppController{
	public $uses = array();
	public $components = array('PhpWsdl');

	public function call($model){
		$this->autoRender=false;
		$this->PhpWsdl->handle($model);
	}
	/**
	 * Provide WSDL for a model
	 */
	function wsdl($model)
	{
		$this->autoRender = FALSE;
		header('Content-Type: text/xml; charset=UTF-8');
		echo $this->PhpWsdl->getWSDL($model);
	}
}