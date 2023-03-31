<?PHP
include $_SERVER['DOCUMENT_ROOT']."/include/header.inc.php";
include_once $_SERVER['DOCUMENT_ROOT']. "/classes/member.class.php";

$memberHandler = new MEMBER();

if($_GET['mode'] == "register"){
    $row = $memberHandler->memberRegister();
    exit;
}

if($_GET['mode'] == "login") {
    $memberHandler->memberLogin();
    exit;
}

if($_GET['mode'] == "logOut") {
    $row = $memberHandler->memberLogOut();
    exit;
}

if($_POST['mode'] == "update") {
    $row = $memberHandler->memberUpdate();
    exit;
}

if($_POST['mode'] == "memberOut") {

    $row = $memberHandler->memberOut();
    exit;

}











//if($_POST['mode'] == "comment") {
//
//    if($_SESSION) {
//        $sql = "insert into educomment (ec_no, em_no, ecm_comment, em_name) values ('".$_POST['postNum']."','".$_SESSION['no']."','".$_POST['commentText']."','".$_SESSION['name']."')";
//
//        $stmt = $dbconn->prepare($sql);
//        $stmt->execute();
//
//        alertHref("댓글이 작성 되었습니다.", "/board/detail.php?ec_no={$_POST['postNum']}");
//        exit;
//    } else{
//        alertHref("로그인후 이용하세요", "/member/login.php");
//        exit;
//    }
//}


?>