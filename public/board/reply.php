<?PHP
# include = 같은 파일 여러 번 포함 가능 / 포함할 파일이 없어도 다음 코드 실행. header.inc 파일 읽어옴
include $_SERVER['DOCUMENT_ROOT']."/include/header.php";

# include_once = 같은 파일 한 번만 포함 / 포함할 파일이 없어도 다음 코드 실행. board.class 파일 읽어옴
include_once $_SERVER['DOCUMENT_ROOT']. "/classes/board.class.php";

# $memberHandler 는 인스턴스, BOARD 는 타입을 정의하는 클래스
$boardHandler = new BOARD();

# detailBoard()함수를 호출하는 변수. 게시글을 상세조회한 값을 가짐
$row = $boardHandler->detailBoard();

?>

<br>
<br>
<br>

<html>
<head>
    <link rel="stylesheet" href="../css/write.css">
    <script>

        // 게시판 선택 value 를 가져오는 함수   writeCheck 함수에서 게시판 value 값을 가지고 있는지 확인하기 위해 사용
        function selectCheck(){
            let selectBox = document.getElementById('selectBox').value;
            return selectBox;
        }

        // 답글 필수값 확인 함수
        function writeCheck(){
            let select = selectCheck();
            let writeSub = document.getElementById('subject').value;
            let writeContent = document.getElementById('content').value;

            testDelete();

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

        function testDelete(){
            const fileInputs = document.querySelectorAll('input[type="file"]');
            const fileInputsName = document.querySelectorAll('input[name^="file"]');

            for (let i = 0; i < fileInputs.length; i++) {
                const inputName = fileInputs[i].getAttribute('name');
                const inputValue = document.getElementById(inputName).value;

                if(inputValue.length == 0){
                    fileInputsName[i].parentNode.remove();
                }
            }


        }

        function createFileInputs() {
            let container = document.getElementById("fileIncreaseDiv");
            let inputCount = container.querySelectorAll('input[type="file"]').length;

            if (inputCount < 10) {
                let input = document.createElement("input");
                input.type = "file";
                input.name = "file" + (inputCount + 1);
                input.id = "file" + (inputCount + 1);

                let deleteBtn = document.createElement("p");
                deleteBtn.type = "p";
                deleteBtn.innerText = "x";
                deleteBtn.class = "xBtn";
                deleteBtn.onclick = function() {
                    input.remove();
                    deleteBtn.remove();
                };

                let wrapper = document.createElement("div");
                wrapper.appendChild(input);
                wrapper.appendChild(deleteBtn);

                container.appendChild(wrapper);

            }
        }
    </script>
</head>
<body>
<div class="holeWriteDiv">
    <div class="writeDiv">
        <div class="write">
<h2>답글</h2>
<? if($row['parents'] && $row['ec_depth'] && !$row['ec_de_depth']){
?>
        <h3>de_depth</h3>
    <form class="form" name="writeForm" action="/board/bExec.php" onsubmit="return writeCheck()" enctype="multipart/form-data" method="post">
        <input type="hidden" name="mode" value="write">
        <input type="hidden" name="select" value="<?php echo $_GET['ecc_boardlist']?>">
        <input type="hidden" name="parents" value="<?echo $row['parents']?>">
        <input type="hidden" name="depth" value="<?echo $row['ec_depth']?>">
        <input type="hidden" name="de_depth" value="<?echo $row['ec_no']?>">
        제목<br><input type="text" name="subject" id="subject" placeholder="제목을 입력하세요"><br><br>
        내용<br><textarea name="content" id="content" rows="4" cols="50" placeholder="내용을 입력하세요"></textarea><br><br>
        <div class="holeFileIncreaseDiv">
            <p onclick="createFileInputs()" style="cursor: pointer">파일폼 추가하려면 클릭</p>
            <div id="fileIncreaseDiv" class="fileIncreaseDiv">
            </div>
        </div><br><br><br>
        <input type="submit" value="글 등록">
    </form>
<?
}   else if(!$row['ec_depth'] && $row['parents']){?>
        <h3>depth</h3>
    <form class="form" name="writeForm" action="/board/bExec.php" onsubmit="return writeCheck()" enctype="multipart/form-data" method="post">
        <input type="hidden" name="mode" value="write">
        <input type="hidden" name="select" value="<?php echo $_GET['ecc_boardlist']?>">
        <input type="hidden" name="parents" value="<?echo $row['parents']?>">
        <input type="hidden" name="depth" value="<?echo $row['ec_no']?>">
        제목<br><input type="text" name="subject" id="subject" placeholder="제목을 입력하세요"><br><br>
        내용<br><textarea name="content" id="content" rows="4" cols="50" placeholder="내용을 입력하세요"></textarea><br><br>
        <div class="holeFileIncreaseDiv">
            <p onclick="createFileInputs()" style="cursor: pointer">파일폼 추가하려면 클릭</p>
            <div id="fileIncreaseDiv" class="fileIncreaseDiv">
            </div>
        </div><br><br><br>
        <input type="submit" value="글 등록">
    </form>
<?
}   else if(!$row['parents']){?>
    <h3>parents</h3>
    <form class="form" name="writeForm" action="/board/bExec.php" onsubmit="return writeCheck()" enctype="multipart/form-data" method="post">
        <input type="hidden" name="mode" value="write">
        <input type="hidden" name="select" value="<?php echo $_GET['ecc_boardlist']?>">
        <input type="hidden" name="parents" value="<?echo $row['ec_no']?>">
        제목<br><input type="text" name="subject" id="subject" placeholder="제목을 입력하세요"><br><br>
        내용<br><textarea name="content" id="content" rows="4" cols="50" placeholder="내용을 입력하세요"></textarea><br><br>
        <div class="holeFileIncreaseDiv">
            <p onclick="createFileInputs()" style="cursor: pointer">파일폼 추가하려면 클릭</p>
            <div id="fileIncreaseDiv" class="fileIncreaseDiv">
            </div>
        </div><br><br><br>
        <input type="submit" value="글 등록">
    </form>
<?
}?>
        </div>
    </div>
</div>
</body>
</html>


<?PHP
include $_SERVER['DOCUMENT_ROOT']."/include/footer.php";
?>
