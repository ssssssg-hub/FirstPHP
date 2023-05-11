<?PHP
# include = 같은 파일 여러 번 포함 가능 / 포함할 파일이 없어도 다음 코드 실행. header.inc 파일 읽어옴
include $_SERVER['DOCUMENT_ROOT']."/include/header.php";

# include_once = 같은 파일 한 번만 포함 / 포함할 파일이 없어도 다음 코드 실행. board.class 파일 읽어옴
include_once $_SERVER['DOCUMENT_ROOT']. "/classes/board.class.php";

# $memberHandler 는 인스턴스, BOARD 는 타입을 정의하는 클래스
$boardHandler = new BOARD();

# detailBoard()함수를 호출하는 변수. 게시글을 상세조회한 값을 가짐`
$row = $boardHandler->detailBoard();

# detailFiles()함수를 호출하는 변수. 해당 게시글의 파일 정보들을 조회한 값을 가짐
$fileRows = $boardHandler->detailFiles();

$defaultNum = $_SESSION['no'];
$defaultPostNum = $_GET['ec_no'];
?>


<html>
<head>
    <link rel="stylesheet" href="/css/detail.css">
    <script>

    // 댓글 체크 함수
    function commentCheck(){
        let comment = document.getElementById('commentText').value;

        if(comment.length == 0){
            alert("댓글을 입력하세요")
            return false;
        } else{
            return true;
        }
    }

    // 게시글 삭제 실행 함수
    function deleteCheck(){
        if(!confirm("삭제하시겠습니까?")){
            return false;
        } else {
            return true;
        }
    }

    // 파일 다운로드 실행 함수    e 는 filename 이라는 name 을 가진 input 의 value 를 구하기 위해 this 로 가져온 값
    function downloadCheck(e){
        if(!confirm("첨부파일을 다운로드 하시겠습니까?")){
            return false;
        } else {
            let filename = e.querySelector('input[name="filename"]').value;
            location.href=`bExec.php?file=${filename}&target_Dir=attach/board&mode=download&ec_no=<?php echo $row['ec_no']?>`;
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
                <div >
                </div>
            </div>
            <br><br>
            <div class="detailContent">
                <td>내용: <?= $row['ec_content']?></td>
            </div>
            <div class="postCareDiv">
                <div>
                <?php
                if($fileRows[0]['ea_filename']){
                    ?>
                        <?php
                            foreach ($fileRows as $item)
                            {
                        ?>
                                <div class="downloadFileDiv" style="font-size: 12px;" onclick="downloadCheck(this)" method="get">
                                    <p>첨부파일 : <span style="text-decoration: underline; cursor: pointer;"><?php echo $item['ea_filename']?></span></p>
                                    <input type="hidden" name="mode" value="download">
                                    <input type="hidden" id="filename" name="filename" value="<?php echo $item['ea_filename']?>">
                                </div>
                        <?php
                            }
                        ?>
                    <?php
                }
                ?>
                </div>
                <div class="postCareBtnDiv">
                    <?php
                    if($defaultNum == $row['em_no']){
                        ?>
                        <?php
                        if(!$row['ec_de_depth']){
                        ?>
                            <button style="cursor:pointer; margin: 5px 5px 10px 5px;border: 1px solid whitesmoke;background-color: whitesmoke;height: 20px; width: 85px" onclick="location.href='reply.php?ec_no=<?php echo $row['ec_no']?>&ecc_boardlist=<?php echo $row['ecc_boardlist']?>'">답글 달기</button>
                        <?php
                        }
                        ?>
                        <form action="/board/write.php?ec_no=<?php echo $defaultPostNum?>" method="get">
                            <input type="hidden" name="ec_no" value="<?php echo $defaultPostNum?>">
                            <input style="cursor:pointer; margin: 5px 5px 0px 5px;border: 1px solid whitesmoke;background-color: whitesmoke;height: 20px;" type="submit"  value="게시글 수정">
                        </form>
                        <form action="/board/bExec.php"onsubmit="deleteCheck()" method="post">
                            <input type="hidden" name="mode" value="postOut">
                            <input type="hidden" name="parents" value="<?php echo $row['parents']?>">
                            <input type="hidden" name="ec_depth" value="<?php echo $row['ec_depth']?>">
                            <input type="hidden" name="ec_de_depth" value="<?php echo $row['ec_de_depth']?>">
                            <input type="hidden" name="postNum" value="<?= $defaultPostNum?>">
                            <input style=" cursor:pointer;margin: 0px 5px 0px 5px;border: 1px solid whitesmoke;background-color: whitesmoke;height: 20px;" type="submit"  value="게시글 삭제">
                        </form>
                        <?php
                    }
                    ?>
                </div>
            </div>
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
