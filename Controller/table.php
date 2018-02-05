<?php
require $_SERVER['DOCUMENT_ROOT'] . '/dashboard/Model/Init.php';
require $_SERVER['DOCUMENT_ROOT'] . '/dashboard/Model/Table.php';
$table = new Table();


$action = $_GET['action'];

switch ($action){
    case 'points-board':
        $return = $table->tablePointsBoard();
        echo $return;
        break;
    case 'points-board-admin':
        $return = $table->tablePointsBoardAdmin();
        echo $return;
        break;
    case 'generated-list':
        $return = $table->tableGeneratedList();
        echo $return;
        break;
}