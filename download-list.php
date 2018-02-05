<?php
require $_SERVER['DOCUMENT_ROOT'] . '/dashboard/Model/Init.php';
require $_SERVER['DOCUMENT_ROOT'] . '/dashboard/Model/Dashboard.php';
require $_SERVER['DOCUMENT_ROOT'] . '/dashboard/Model/session.php';
$dashboard = new Dashboard();

$list = $dashboard->getAllGeneratedList();
$textFile = 'public_keys.txt';
$fh = fopen($textFile, 'w');
$i = 1;
foreach($list as $row){
    fwrite($fh, $i.'. #'.$row['public_key'] . "\r\n");
    $i++;
}

fclose($fh);

$file_url = $textFile;
header('Content-Type: application/octet-stream');
header("Content-Transfer-Encoding: Binary");
header("Content-disposition: attachment; filename=\"" . basename($file_url) . "\"");
readfile($file_url);

?>