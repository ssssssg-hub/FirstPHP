<?PHP
include $_SERVER['DOCUMENT_ROOT']."/include/header.php";
?>

<?php
include_once $_SERVER['DOCUMENT_ROOT']. "/classes/board.class.php";

$boardHandler = new BOARD();
$rows = $boardHandler->detailBoard();

$row = $rows[0];
$fileRows = $rows[1];

$defaultNum = $_SESSION['no'];
$defaultPostNum = $_GET['ec_no'];

?>


<html>
<head>
    <link rel="stylesheet" href="/css/detail.css">
    <script>
    function commentCheck(){
        let comment = document.getElementById('commentText').value;

        if(comment.length == 0){
            alert("댓글을 입력하세요")
            return false;
        } else{
            return true;
        }
    }

    function deleteCheck(){
        if(!confirm("삭제하시겠습니까?")){
            return false;
        } else {
            return true;
        }
    }
    function downloadCheck(){
        if(!confirm("첨부파일을 다운로드 하시겠습니까?")){
            return false;
        } else {
            location.href='bExec.php?file=<?php echo $row['ea_filename']?>&target_Dir=attach/board&mode=download&ec_no=<?php echo $row['ec_no']?>';
        }
    }

    </script>
</head>
<body>
<div class="holeDetailDiv">
    <div class="detailDiv">
        <div class="detail">
            <div class="detailTitle">
                <h3>제목: <?= $row['ec_subject']?></h3>
            </div>
            <div class="infoDiv" style="display: flex; justify-content: space-between">
                <div class="nameDiv">
                <td>작성자: <?= $row['em_name']?></td>
                <br>
                <td>Email: <?= $row['em_email']?></td>
                </div>
                <div class="postCareBtnDiv">
                    <?php
                    if($defaultNum == $row['em_no']){
                        ?>
                        <form action="/board/write.php?ec_no=<?php echo $defaultPostNum?>'" method="get">
                            <input type="hidden" name="postNum" value="<?php echo $defaultPostNum?>">
                            <input style="cursor:pointer; margin: 5px 5px 0px 5px;border: 1px solid whitesmoke;background-color: whitesmoke;height: 20px;" type="submit"  value="게시글 수정">
                        </form>
                        <form action="/board/bExec.php"onsubmit="deleteCheck()" method="post">
                            <input type="hidden" name="mode" value="postOut">
                            <input type="hidden" name="postNum" value="<?= $defaultPostNum?>">
                            <input style=" cursor:pointer;margin: 0px 5px 0px 5px;border: 1px solid whitesmoke;background-color: whitesmoke;height: 20px;" type="submit"  value="게시글 삭제">
                        </form>
                        <?php
                    }
                    ?>
                </div>
            </div>
            <br><br>
            <div class="detailContent">
                <td>내용: <?= $row['ec_content']?></td>
            </div>
        <?php
        if($fileRows[0]['ea_filename']){
            ?>
                <?php
                    foreach ($fileRows as $item)
                    {
                ?>
                        <div class="downloadFileDiv" style="font-size: 12px;" onclick="downloadCheck()" method="get">
                            <p>첨부파일 : <span style="text-decoration: underline; cursor: pointer;"><?php echo $item['ea_filename']?></span></p>
                            <input type="hidden" name="mode" value="download">
                        </div>
                <?php
                    }
                ?>
            <?php
        }
        ?>
        </div>
    </div>
</div>




<!---->
<!---->
<!---->
<!--<br>-->
<!--<hr>-->
<!--<br>-->
<!--<h3>댓글</h3>-->
<!--<div style="display: inline-flex;">-->
<!--<td>--><?php //= $_SESSION['id']?><!-- </td>-->
<!--<form action="/member/exec.php" onsubmit="return commentCheck()" method="post">-->
<!--    <input type="hidden" name="mode" value="comment">-->
<!--    <input type="hidden" name="postNum" value="--><?php //= $defaultPostNum?><!--">-->
<!--    <textarea placeholder="댓글을 입력하세요" name="commentText" id="commentText"></textarea>-->
<!--    <input type="submit" value="댓글 달기">-->
<!--</form>-->
<!--</div>-->
<!--</body>-->
<!--</html>-->
<?php
//if($_SESSION['id'] && !$row['ec_de_depth']){
//    ?>
<!--    <button onclick="location.href='/board/reply.php?ec_no=<?php //echo $row['ec_no']?>//'">답글 달기</button>-->
<?php
//}
//?>
<?PHP
include $_SERVER['DOCUMENT_ROOT']."/include/footer.php";
?>
