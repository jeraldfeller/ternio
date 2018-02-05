<?php
if (isset($_SESSION['isLoggedIn']) && $_SESSION['isLoggedIn']) {
    $userData = $_SESSION['userData'];
}else{
    Header('Location:  login.php');
}
