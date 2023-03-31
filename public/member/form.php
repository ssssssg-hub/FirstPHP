<?PHP
include $_SERVER['DOCUMENT_ROOT']."/include/header.php";
?>
<?php
//$defaultNum = $_SESSION['no'];
//$sql = "select * from edumember where em_no='{$defaultNum}'";
//
//$stmt = $dbconn->prepare($sql);
//$stmt->execute();
//
//$row = $stmt->fetch();

include_once $_SERVER['DOCUMENT_ROOT']. "/classes/member.class.php";

$memberHandler = new MEMBER();

$row = $memberHandler->verifyMember();
$defaultName = $row['em_name'];
$defaultId = $row['em_id'];
$defaultPassword = $row['em_pwd'];
$defaultEmail = $row['em_email'];
$defaultPhone = $row['em_phone'];
$defaultAddress = $row['em_address'];

?>

<html>
<head>
    <link rel="stylesheet" href="/css/form.css">
<script>

    function registerCheck(){
        let inputName = document.getElementById('name').value;
        let inputId = document.getElementById('id').value;
        let inputPassword = document.getElementById('password').value;
        let inputPasswordCheck = document.getElementById('passwordCheck').value;
        let inputEmail = document.getElementById('email').value;
        let inputPhone = document.getElementById('phone').value;
        let inputAddress = document.getElementById('address').value;

        if(inputName.length == 0){
            alert("이름을 입력하세요")
            return false;
        } else if(inputId.length == 0) {
            alert("ID를 입력하세요")
            return false;
        } else if(inputPassword.length == 0) {
            alert("패스워드를 입력하세요")
            return false;
        } else if(inputPassword !== inputPasswordCheck) {
            alert("패스워드를 확인하세요")
            return false;
        }else if(inputEmail.length == 0) {
            alert("email을 입력하세요")
            return false;
        }else {
            return true;
        }
    }

    function memberUpdateCheck(){
        let updateInputName = document.getElementById('updateName').value;
        let updateInputId = document.getElementById('updateId').value;
        let updateInputPassword = document.getElementById('updatePassword').value;
        let updateInputPasswordCheck = document.getElementById('updatePasswordCheck').value;
        let updateInputEmail = document.getElementById('updateEmail').value;
        let updateInputPhone = document.getElementById('updatePhone').value;
        let updateInputAddress = document.getElementById('updateAddress').value;

        if(updateInputName.length == 0){
            alert("이름을 입력하세요")
            return false;
        } else if(updateInputPassword.length == 0) {
            alert("패스워드를 입력하세요")
            return false;
        } else if(updateInputPassword !== updateInputPasswordCheck) {
            alert("패스워드를 확인하세요")
            return false;
        }else if(updateInputEmail.length == 0) {
            alert("email을 입력하세요")
            return false;
        }else {
            return true;
        }
    }

</script>
</head>
<body>
<?
if($_SESSION){
?>
        <div class="holeMemberFormDiv">
            <div class="memberFormDiv">
                <h1>회원수정</h1>
                <form class="memberForm" name="updateForm" action="/member/exec.php" onsubmit="return memberUpdateCheck()" method="post">
                    <input type="hidden" name="mode" value="update"/>
                    name<br>
                    <input type="text"id="updateName" name="updateName" value="<?= $defaultName ?>"><br>
                    id<br>
                    <input type="text" id="updateId" name="updateId" value="<?= $defaultId ?>" disabled><br>
                    password<br>
                    <input type="password" id="updatePassword" name="updatePassword" ><br>
                    passwordCheck<br>
                    <input type="password" id="updatePasswordCheck" name="updatePasswordCheck" ><br>
                    email<br>
                    <input type="text" id="updateEmail" name="updateEmail" value="<?= $defaultEmail ?>"><br>
                    phone<br>
                    <input type="text" id="updatePhone" name="updatePhone" value="<?= $defaultPhone ?>"><br>
                    address<br>
                    <input type="text" id="updateAddress" name="updateAddress" value="<?= $defaultAddress ?>"><br><br>
                    <input type="submit" value="회원수정">
                </form>
            </div>
        </div>
    <?
}else {
?>
        <div class="holeMemberFormDiv">
            <div class="memberFormDiv">
                <h1>회원가입</h1>
                <form class="memberForm" name="firstForm" action="/member/exec.php?mode=register" onsubmit="return registerCheck()" method="get">
                    <input type="hidden" name="mode" value="register"/>
                    name<br>
                    <input type="text"id="name" name="name"><br>
                    id<br>
                    <input type="text" id="id" name="id"><br>
                    password<br>
                    <input type="password" id="password" name="password"><br>
                    passwordCheck<br>
                    <input type="password" id="passwordCheck" name="passwordCheck"><br>
                    email<br>
                    <input type="text" id="email" name="email"><br>
                    phone<br>
                    <input type="text" id="phone" name="phone"><br>
                    address<br>
                    <input type="text" id="address" name="address"><br><br>
                    <input type="submit" value="회원가입">
                </form>
            </div>
        </div>
    <?
}
?>
</body>
</html>

