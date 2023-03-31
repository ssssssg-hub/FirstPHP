<?PHP
include $_SERVER['DOCUMENT_ROOT']."/include/header.php";
include_once $_SERVER['DOCUMENT_ROOT']. "/classes/board.class.php";


$boardHandler = new BOARD();
$row = $boardHandler->fixBoardData();
$rows = $boardHandler->boardList();

$defaultPostNum =  $_GET['postNum'];
$defaultSubject = $row['ec_subject'];
$defaultContent = $row['ec_content'];
$defaultImg = $row['ec_filename'];
?>

<br>
<br>
<br>

<html>
<head>
    <link rel="stylesheet" href="../css/write.css">
    <script>
        let fileInput = document.getElementsByClassName('file').item(0);

        function selectCheck(){
            let selectBox = document.getElementById('selectBox').value;
            return selectBox;
        }

        function writeCheck(){
            let select = selectCheck();
            let writeSub = document.getElementById('subject').value;
            let writeContent = document.getElementById('content').value;
            if (select == 0){
                alert("게시판을 선택하세요");
                return false;
            }else if (writeSub.length == 0){
                alert("제목을 입력하세요")
                return false;
            }else if(writeContent.length == 0){
                alert("내용을 입력하세요");
                return false;
            } else {
                return true;
            }
        }
    </script>
</head>
<body>
<div class="holeWriteDiv">
    <div class="writeDiv">
        <div class="write">
<?php
if(!$row){
    ?>
    <h2>게시글 작성</h2>
    <form class="form" name="writeForm" action="/board/bExec.php" onsubmit="return writeCheck()" enctype="multipart/form-data" method="post">
        <input type="hidden" name="mode" value="write">
        <select id="selectBox" name="select" onchange="selectCheck();">
            <?php
            foreach ($rows as $item)
                {
            ?>
                    <option value="<?php echo $item['ecc_no']?>"><?php echo $item['ecc_title']?></option>
            <?
                }
            ?>
        </select><br><br>
        제목<br><input type="text" name="subject" id="subject" placeholder="제목을 입력하세요"><br><br>
        내용<br><textarea name="content" id="content" rows="4" cols="50" placeholder="내용을 입력하세요"></textarea><br><br>
        <input type="file" name="file[]" multiple='multiple' ><br><br><br><br>
        <input type="submit" value="글 등록">
    </form>
    <?php
} else{
?>
    <h2>게시글 수정</h2>
    <hr>
    <form class="form" name="fixForm" action="/board/bExec.php" onsubmit="return fixCheck()" enctype="multipart/form-data" method="post">
        <input type="hidden" name="mode" value="fixPost">
        <input type="hidden" name="ec_no" value="<? echo $defaultPostNum?>">
        <select id="selectBox" name="select" onchange="selectCheck();">
            <?php
            foreach ($rows as $item)
            {
                ?>
                <option value="<?php echo $item['ecc_no']?>"><?php echo $item['ecc_title']?></option>
                <?
            }
            ?>
        </select><br><br>
        제목<br><input type="text" name="fixSubject" id="fixSubject" value="<?= $defaultSubject ?>"><br><br><br>
        내용<br><textarea name="fixContent" id="fixContent" rows="4" cols="50"><?= $defaultContent ?></textarea><br><br>
        <div class="fileForm">
        <input type="file" name="file[]" multiple='multiple'>
        <p class="fileName">
        <?php
        if($row['ec_filename']){
            ?>
            기존첨부파일 : <?echo $defaultImg?>
            <?php
        }
        ?></p>
        </div><br><br>
        <input type="submit" value="글 수정">
    </form>
<?php
}
?>
        </div>
    </div>
</div>
</body>
</html>


<?PHP
include $_SERVER['DOCUMENT_ROOT']."/include/footer.php";
?>


























