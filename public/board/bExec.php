<?php
# include = 같은 파일 여러 번 포함 가능 / 포함할 파일이 없어도 다음 코드 실행. header.inc 파일 읽어옴
include $_SERVER['DOCUMENT_ROOT']."/include/header.inc.php";

# include_once = 같은 파일 한 번만 포함 / 포함할 파일이 없어도 다음 코드 실행. board.class 파일 읽어옴
include_once $_SERVER['DOCUMENT_ROOT']. "/classes/board.class.php";

# $memberHandler 는 인스턴스, BOARD 는 타입을 정의하는 클래스
$memberHandler = new BOARD();

# post 의 mode 가 write 일 때 실행
if($_POST['mode'] == "write") {

//    $row = $memberHandler->boardImg();
    $row = $memberHandler->boardWrite();
    exit;
}

# post 의 mode 가 fixPost 일 때 실행
if($_POST['mode'] == "fixPost") {

//    $row = $memberHandler->fileDelete();
    $row = $memberHandler->boardFix();

    exit;
}

# post 의 mode 가 postOut 일 때 실행
if($_POST['mode'] == "postOut") {

    $row = $memberHandler->replyBoardDelete();
//    $row = $memberHandler->boardDelete();

    exit;
}

# get 의 mode 가 download 일 때 실행
if($_GET['mode'] == "download") {

    $row = $memberHandler->fileDownload();
    exit;
}

# post 의 mode 가 boardCreate 일 때 실행
if($_POST['mode'] == "boardCreate"){
    $row = $memberHandler->boardCreate();
    exit;
}

?>