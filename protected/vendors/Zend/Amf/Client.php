<?php

class Zend_Amf_Client {    
    private $endPoint;
    private $encoding = Zend_Amf_Constants::AMF0_OBJECT_ENCODING;

    public function __construct($endPoint) {
        $this->endPoint = $endPoint;
    }
    
    public function sendRequest($servicePath, $data) {

        /*if($this->encoding & Zend_Amf_Constants::AMF0_TYPEDOBJECT) {
            $message = new Zend_Amf_Value_Messaging_RemotingMessage();
            $message->body = $data;
            $service = explode('.', $servicePath);
            $method = array_pop($service);
            $service = implode('.', $service);
            $message->operation = $method; 
            $message->source = $service;

            $data = $message;
        }*/
        
        $amfResponse = new Zend_Amf_Response();
        $body = new Zend_Amf_Value_MessageBody($servicePath, null, $data);
        $amfResponse->addAmfBody($body);
        $rawData = $amfResponse->finalize()->getResponse();
        
        $ch = curl_init($this->endPoint);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/x-amf'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $rawData);
        curl_setopt($ch, CURLOPT_COOKIESESSION, true);
        curl_setopt($ch, CURLOPT_COOKIEJAR, 'amf_cookies.txt');
        curl_setopt($ch, CURLOPT_COOKIEFILE, 'amf_cookies.txt');
        
        $requestStr = curl_exec($ch);
        curl_close($ch);
        
        $request = new Zend_Amf_Request;
        $request->initialize($requestStr);
        $result = $request->getAmfBodies();
        return $result[0]->getData();
    }
    
    public function setEncoding($encoding) {
        $this->encoding = $encoding;
    }
}

?>
