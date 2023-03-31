<?PHP
include $_SERVER['DOCUMENT_ROOT']."/include/header.php";

include_once $_SERVER['DOCUMENT_ROOT']. "/classes/board.class.php";

$boardHandler = new BOARD();
$rows = $boardHandler->getBoardList();

$row = $boardHandler->boardList();

$sql = "select ecc_title from educommuclassifi where ecc_no = {$_GET['boardNo']} ";
$stmt = $dbconn->prepare($sql);
$stmt->execute();
$title = $stmt->fetch();
?>
<html>
<head>
    <link rel="stylesheet" href="../css/board.css">
    <script>
        function postOut() {
            if(!confirm("게시글을 삭제하시겠습니까?")){
                alert("취소되었습니다.")
                return false;
            }
            return true;
        }
        function createCheck(){
            let writeBoardName = document.getElementById('boardName').value;
            if(writeBoardName.length == 0){
                alert("게시판 제목을 작성하세요")
                return false
            } else return true;
        }
    </script>
</head>
<body>
<div class="holeDiv">
    <div class="holeCategoryDiv">
        <div class="categoryDiv">
            <form id="boardCreateForm" action="bExec.php" onsubmit="return createCheck()" method="post">
                <input type="hidden" name="mode" value="boardCreate">
                <input type="text" id="boardName" name="boardName">
                <input type="submit" value="게시판생성">
            </form>
            <?php
            foreach ($row as $item)
            {
            ?>
                <button onclick="location.href='/board/board.php?pageNo=1&boardNo=<?php echo $item['ecc_no']?>'"><?php echo $item['ecc_title']?></button>
            <?php
            }?>
        </div>
    </div>
    <div class="holeBoardDiv">
<div class="holeTitleDiv">
<div class="titleDiv">
<h2 class="title"><?php echo $title['ecc_title']?></h2><br><br>
    <?php
    if($_SESSION['no']){
    ?>
    <button class="writeBtn" onclick="location.href='/board/write.php'">게시글 작성</button><br><br><br>
    <?}?>
</div>
</div>


<div class="holeBoardBox">
    <div class="boardBox">
<p>
    <!-- 게시글 카드 foreach문으로 보여주기-->
    <?php
    foreach ($rows as $value)
    {
    ?>
    <?php if($value['ec_de_depth']){
    ?>
<div class="replyMark">
    &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
    <td>&nbsp; └ &nbsp;</td><br>
    &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
    <td>&nbsp;  &nbsp;</td><br>
    &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
    <td>&nbsp;  &nbsp;</td>
    <div class="board" onclick="location.href='/board/detail.php?ec_no=<?php echo $value['ec_no']?>'" method="get">
        <div class="boardCon">
            <h3>제목 : <?php echo $value['ec_subject']?></h3><br>
            <td>작성자 : <?php echo $value['em_id']?></td>
        </div>
        <div>
            <td><?php
                $str = substr($value['ec_regdt'], 0, 10);
                echo $str;
                ?></td>
        </div>
    </div>
</div>
<?php
}   else if(!$value['ec_de_depth'] && $value['ec_depth']){?>
    <div class="replyMark">
        &nbsp; &nbsp; &nbsp;
        <td>&nbsp; └ &nbsp;</td><br>
        &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
        <td>&nbsp;  &nbsp;</td>
        <div class="board" onclick="location.href='/board/detail.php?ec_no=<?php echo $value['ec_no']?>'" method="get">
            <div class="boardCon">
                <h3>제목 : <?php echo $value['ec_subject']?></h3><br>
                <td>작성자 : <?php echo $value['em_id']?></td>
            </div>
            <div>
                <td><?php
                    $str = substr($value['ec_regdt'], 0, 10);
                    echo $str;
                    ?></td>
            </div>
        </div>
    </div>
    <?php
}   else if(!$value['ec_depth'] && $value['parents']){?>
    <div class="replyMark">
        <td>&nbsp; └ &nbsp;</td>
        <div class="board" onclick="location.href='/board/detail.php?ec_no=<?php echo $value['ec_no']?>'" method="get">
            <div class="boardCon">
                <h3>제목 : <?php echo $value['ec_subject']?></h3><br>
                <td>작성자 : <?php echo $value['em_id']?></td>
            </div>
            <div>
                <td><?php
                    $str = substr($value['ec_regdt'], 0, 10);
                    echo $str;
                    ?></td>
            </div>
        </div>
    </div>
    <?php
}   else if(!$value['parents']){?>
    <div class="replyMark" ">
        <div class="board" onclick="location.href='/board/detail.php?ec_no=<?php echo $value['ec_no']?>'" method="get">
            <div class="boardCon">
                <h3>제목 : <?php echo $value['ec_subject']?></h3><br>
                <td>작성자 : <?php echo $value['em_id']?></td>
                <br>
                <?php
                if($value['ea_filename']){
                    ?>
                    <td class="uploadedFileTd">첨부파일</td>
                    <?php
                }
                ?>
            </div>
            <div>
                <td><?php
                    $str = substr($value['ec_regdt'], 0, 10);
                    echo $str;
                    ?></td>
            </div>
        </div>
    </div>
    <?php
}?>
<?php
}
?>
</p>
</div>
</div>
<?php
echo $boardHandler->getPageNavi();
?>
</div>
</div>
</body>
</html>


<?PHP
include $_SERVER['DOCUMENT_ROOT']."/include/footer.php";
?>

