<?php 
class SoapServicesController extends AppController
{
    public $uses = array();
    public $components = array('Soap');

    /**
     * Handle SOAP calls
     */
    function call($model)
    {
        $this->autoRender = FALSE;
        $this->Soap->handle($model);
    }

    /**
     * Provide WSDL for a model
     */
    function wsdl($model)
    {
        $this->autoRender = FALSE;
        header('Content-Type: text/xml; charset=UTF-8');
        echo $this->Soap->getWSDL($model, 'call');
    }
    
	function test() {
		$this->autoRender = FALSE;
 		$client = new SoapClient(WS_DOMAIN."/soapServices/wsdl/temp");
 		$result = $client->login('<note><item>aaa</item></note>');
 		print_r($result);
	}	
}
?>