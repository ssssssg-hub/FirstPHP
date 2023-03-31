<?php
include $_SERVER['DOCUMENT_ROOT']."/include/header.inc.php";
include_once $_SERVER['DOCUMENT_ROOT']. "/classes/board.class.php";
$memberHandler = new BOARD();

if($_POST['mode'] == "write") {
//    $row = $memberHandler->boardImg();
    $row = $memberHandler->boardWrite();
    exit;
}

if($_POST['mode'] == "fixPost") {

//    pre($_POST);
    $row = $memberHandler->boardFix();
    exit;
}

if($_POST['mode'] == "postOut") {
    $row = $memberHandler->boardDelete();
    exit;
}

if($_GET['mode'] == "download") {

//    $filename = $_GET['file'];
//    $target_Dir = $_GET['target_Dir'];
//    $file = $_SERVER['DOCUMENT_ROOT'] . $target_Dir . "/" . $filename;
//    pre($_GET);
//    pre($file);
//    pre($_GET["file"]);

//    $fileContents = file_get_contents($file);
//            echo $fileContents;

//    header("Content-type: application/octet-stream");
//    header("Content-Length: " . filesize($file));
//    header("Content-Disposition: attachment; filename=$filename");
//    header("Content-Transfer-Encoding: binary");
//    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
//    header("Pragma: public");
//    header("Expires: 0");
//
//    $fp = fopen($file, "rb");
//    fpassthru($fp);
//    fclose($fp);
    $row = $memberHandler->fileDownload();
}

if($_POST['mode'] == "boardCreate"){
    $row = $memberHandler->boardCreate();
    exit;
}
?>