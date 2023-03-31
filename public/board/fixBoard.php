<?PHP

include $_SERVER['DOCUMENT_ROOT']."/include/header.php";
include_once $_SERVER['DOCUMENT_ROOT']. "/classes/board.class.php";


$boardHandler = new BOARD();
$row = $boardHandler->fixBoardData();

$defaultPostNum =  $_GET['ec_no'];
$defaultSubject = $row['ec_subject'];
$defaultContent = $row['ec_content'];
$defaultImg = $row['ec_filename']
?>

<html>
<head>
    <script>
        function fixCheck(){
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
<h2>글 수정</h2>
<form name="fixForm" action="/board/bExec.php" onsubmit="return fixCheck()" method="post">
    <input type="hidden" name="mode" value="fixPost">
    <input type="hidden" name="ec_no" value="<? echo $defaultPostNum?>">
    제목 : <input type="text" name="fixSubject" id="fixSubject" value="<?= $defaultSubject ?>"><br>
    내용 : <textarea name="fixContent" id="fixContent" rows="4" cols="50"><?= $defaultContent ?></textarea>
    <br><br>
    <?php
    if($row['ec_filename']){
        ?>
        <img src="../attach/board/<?echo $row['ec_filename']?>">
        <?php
    }
    ?><br><br>
    <input type="submit" value="글 수정">
</form>
</body>
</html>

<?PHP
include $_SERVER['DOCUMENT_ROOT']."/include/footer.php";
?>
