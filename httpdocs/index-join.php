<?php
if(isset($_GET['JSESSIONID'])) {
    setcookie('JSESSIONID', $_GET['JSESSIONID']);
    header('Location: /client/BigBlueButton.html');
} else {
    header('Location: /');
}