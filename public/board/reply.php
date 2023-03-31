<?PHP
include $_SERVER['DOCUMENT_ROOT']."/include/header.php";
?>
<?php
include_once $_SERVER['DOCUMENT_ROOT']. "/classes/board.class.php";

$boardHandler = new BOARD();
$row = $boardHandler->detailBoard();
?>

<br>
<br>
<br>

<html>
<head>
    <script>
        function writeCheck(){
            let writeSub = document.getElementById('subject').value;
            let writeContent = document.getElementById('content').value;
            if (writeSub.length == 0){
                alert("제목을 입력하세요")
                return false;
            }else if (writeContent.length == 0){
                alert("내용을 입력하세요");
                return false;
            } else {
                return true;
            }
        }
    </script>
</head>
<body>
<h2>답글</h2>
<? if($row['parents'] && $row['ec_depth'] && !$row['ec_de_depth']){
?>
        <h3>de_depth</h3>
    <form name="writeForm" action="/board/bExec.php" onsubmit="return writeCheck()" method="post">
        <input type="hidden" name="mode" value="write">
        <input type="hidden" name="parents" value="<?echo $row['parents']?>">
        <input type="hidden" name="depth" value="<?echo $row['ec_depth']?>">
        <input type="hidden" name="de_depth" value="<?echo $row['ec_no']?>">
        제목 : <input type="text" name="subject" id="subject" placeholder="제목을 입력하세요"><br>
        내용 : <textarea name="content" id="content" rows="4" cols="50" placeholder="내용을 입력하세요"></textarea>
        <input type="submit" value="글 등록">
    </form>
<?
}   else if(!$row['ec_depth'] && $row['parents']){?>
        <h3>depth</h3>
    <form name="writeForm" action="/board/bExec.php" onsubmit="return writeCheck()" method="post">
        <input type="hidden" name="mode" value="write">
        <input type="hidden" name="parents" value="<?echo $row['parents']?>">
        <input type="hidden" name="depth" value="<?echo $row['ec_no']?>">
        제목 : <input type="text" name="subject" id="subject" placeholder="제목을 입력하세요"><br>
        내용 : <textarea name="content" id="content" rows="4" cols="50" placeholder="내용을 입력하세요"></textarea>
        <input type="submit" value="글 등록">
    </form>
<?
}   else if(!$row['parents']){?>
    <h3>parents</h3>
    <form name="writeForm" action="/board/bExec.php" onsubmit="return writeCheck()" method="post">
        <input type="hidden" name="mode" value="write">
        <input type="hidden" name="parents" value="<?echo $row['ec_no']?>">
        제목 : <input type="text" name="subject" id="subject" placeholder="제목을 입력하세요"><br>
        내용 : <textarea name="content" id="content" rows="4" cols="50" placeholder="내용을 입력하세요"></textarea>
        <input type="submit" value="글 등록">
    </form>
<?
}?>
</body>
</html>


<?PHP
include $_SERVER['DOCUMENT_ROOT']."/include/footer.php";
?>
