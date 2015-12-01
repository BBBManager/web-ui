<?
class IMDT_Controller_Helper_Params extends Zend_Controller_Action_Helper_Abstract {
protected $_bodyParams = array();

public function init() {
$request = $this->getRequest();
$contextSwitchFormat = $this->getRequest()->getParam('format', null);

if ($contextSwitchFormat != null) {
switch ($contextSwitchFormat) {
case 'json':
$contentType = 'application/json';
break;
case 'xml':
$contentType = 'application/xml';
break;
default:
$contentType = 'application/json';
break;
}
} else {
$contentType = $request->getHeader('Content-Type');
}

$rawBody = $request->getRawBody();
if (!$rawBody) {
return;
}
switch (true) {
case (strstr($contentType, 'application/json')):
$this->setBodyParams(Zend_Json::decode($rawBody));
break;
case (strstr($contentType, 'application/xml')):
$rParams = IMDT_Util_Xml::xmlToArray($rawBody);
$this->setBodyParams($rParams);
break;
default:
if ($request->isPut()) {
parse_str($rawBody, $params);
$this->setBodyParams($params);
}
break;
}
}

public function setBodyParams(array $params) {
$this->_bodyParams = $params;
return $this;
}

public function getBodyParams() {
return $this->_bodyParams;
}

public function getBodyParam($name) {
if ($this->hasBodyParam($name)) {
return $this->_bodyParams[$name];
}
return null;
}

public function hasBodyParam($name) {
if (isset($this->_bodyParams[$name])) {
return true;
}
return false;
}

public function hasBodyParams() {
if (!empty($this->_bodyParams)) {
return true;
}
return false;
}

public function getSubmitParams() {
if ($this->hasBodyParams()) {
return $this->getBodyParams();
}
return $this->getRequest()->getPost();
}

public function direct() {
return $this->getSubmitParams();
}

}