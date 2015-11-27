<?php

class IMDT_Util_Rest {

    private static $_timeout = 300;

    private static function performRequest($method, $uri, $data = null, $headers = null) {
	session_write_close();

	if ($uri[0] != '/') {
	    $uri = '/' . $uri;
	}

	$clientHttp = new Zend_Http_Client();
	$clientHttp->setConfig(array('timeout' => self::$_timeout));

	$defaultHeaders = array(
	    'userId' => IMDT_Util_Auth::getInstance()->get('login'),
	    'token' => IMDT_Util_Auth::getInstance()->get('token'),
	    'Accept-Language' => Zend_Registry::get('Zend_Locale'),
      'clientIpAddress' => isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR']
	);

	$allHeaders = array();

	foreach ($defaultHeaders as $name => $value) {
	    $allHeaders[$name] = $value;
	}

	if (count($headers) > 0) {
	    foreach ($headers as $name => $value) {
		$allHeaders[$name] = $value;
	    }
	}

	$clientHttp->setHeaders($allHeaders);

	$client = new Zend_Rest_Client(IMDT_Util_Config::getInstance()->get('api_base_url'));

	$client->setHttpClient($clientHttp);

	$response = null;

	switch ($method) {
	    case 'get':
		$response = $client->restGet($uri, $data);
		break;
	    case 'put':
		$response = $client->restPut($uri, $data);
		break;
	    case 'post':
		$response = $client->restPost($uri, $data);
		break;
	    case 'delete':
		$response = $client->restDelete($uri);
		break;
	}

	$responseBody = $response->getBody();

	session_start();

	try {
	    $parsed = self::parseResponse($uri, $responseBody);
	} catch (Exception $e) {
	    throw $e;
	}

	if ($parsed['success'] == '-1') {
	    throw new IMDT_Controller_Exception_InvalidToken($parsed['msg']);
	} elseif ($parsed['success'] == '-2') {
	    throw new IMDT_Controller_Exception_AccessDennied($parsed['msg']);
	} elseif ($parsed['success'] != 1) {
	    throw new Exception($parsed['msg']);
	}

	return $parsed;
    }

    private static function parseResponse($uri, $responseBody) {
	$extension = pathinfo($uri, PATHINFO_EXTENSION);

	try{
	    if ($extension == 'xml') {
		$obj = IMDT_Util_Xml::xmlToArray($responseBody);
	    } else {
		$extension = 'json';
		$obj = Zend_Json::decode($responseBody);
	    }
	}catch(Exception $e){
	    throw new Exception(sprintf(IMDT_Util_Translate::_('Unable to parse %s. Check log for erros. <div style="display:none">data was: %s</div>'), strtoupper($extension), htmlentities($responseBody)));
	}

	if (isset($obj['response']['msg']) && is_array($obj['response']['msg'])) {
	    $obj['response']['msg'] = implode('<br />', $obj['response']['msg']);
	}

	return $obj['response'];
    }

    private static function parseInputParameters($uri, $data) {
	if (pathinfo($uri, PATHINFO_EXTENSION) == 'xml') {
	    $xmlEncoder = new IMDT_Serializer_Adapter_Xml();
	    $stringXml = $xmlEncoder->serialize($data, array('rootNode' => 'data'));

	    return $stringXml;
	} else {
	    return Zend_Json::encode($data);
	}
    }

    public static function get($uri, $queryString = null, $headers = null) {


	   return self::performRequest('get', $uri, $queryString, $headers);
    }


    public static function put($uri, $data, $headers = null, $timeout = null) {
        if($timeout != null){
            self::$_timeout = $timeout;
        }
    	$data = self::parseInputParameters($uri, $data);
    	return self::performRequest('put', $uri, $data, $headers);
    }



    public static function post($uri, $data, $headers = null) {
    	$data = self::parseInputParameters($uri, $data);
    	return self::performRequest('post', $uri, $data, $headers);
    }



    public static function delete($uri, $headers = null) {
	   return self::performRequest('delete', $uri, null, $headers);
    }

}
