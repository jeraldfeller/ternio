<?php
require $_SERVER['DOCUMENT_ROOT'] . '/dashboard/Model/Init.php';
require $_SERVER['DOCUMENT_ROOT'] . '/dashboard/Model/Users.php';
if(isset($_GET['userId']) && isset($_GET['token'])){
    $users = new Users();
    $data = array('userId' => $_GET['userId'], 'token' => $_GET['token']);
    $result = $users->confirmRegistrationFunction($data);
    if($result == true){
        Header('location: login.php');
    }else{
        Header('location: error.html');
    }
}else{
    Header('location: error.html');
}