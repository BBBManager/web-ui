<?php

class IMDT_View_Helper_RenderMessages extends Zend_View_Helper_HtmlElement {

    public function renderMessages() {
        return Zend_Layout::getMvcInstance()->getView()->render('partials/message.phtml');
    }

}
