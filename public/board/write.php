<?PHP
# include = 같은 파일 여러 번 포함 가능 / 포함할 파일이 없어도 다음 코드 실행. header.inc 파일 읽어옴
include $_SERVER['DOCUMENT_ROOT']."/include/header.php";

# include_once = 같은 파일 한 번만 포함 / 포함할 파일이 없어도 다음 코드 실행. board.class 파일 읽어옴
include_once $_SERVER['DOCUMENT_ROOT']. "/classes/board.class.php";

# $memberHandler 는 인스턴스, BOARD 는 타입을 정의하는 클래스
$boardHandler = new BOARD();

# fixBoardData()함수를 호출하는 변수. 수정 페이지에 접근시 게시글의 기존 데이터를 불러옴
$row = $boardHandler->fixBoardData();

# boardList()함수를 호출하는 변수.  게시판 전체 리스트를 불러옴
$rows = $boardHandler->boardList();

# detailFiles();함수를 호출하는 변수. 첨부파일 값을 불러옴
$fileRows = $boardHandler->detailFiles();

$defaultPostNum =  $_GET['ec_no'];
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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script type="text/javascript">
        let arr = [];

        // 게시판 선택 value 를 가져오는 함수   writeCheck 함수에서 게시판 value 값을 가지고 있는지 확인하기 위해 사용
        function selectCheck(){
            let selectBox = document.getElementById('selectBox').value;
            return selectBox;
        }

        // 게시글 필수 작성 요소 체크 함수
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

        // 파일 삭제버튼 활성화 함수
        function deleteCheckBtn (clickId){
            // clickId = delete_OO     -> 삭제 버튼 id
            // cancelId = cancel_OO    -> 취소 버튼 id
            // filenameId = OO         -> id(db에 담겨있는 ea_no)

            document.getElementById(clickId).style.display="none"   // 삭제 버튼 비활성화

            let cancelId = "cancel_" + clickId.split("_")[1];   // 취소버튼의 id 값

            document.getElementById(cancelId).style.display = "flex";   // 취소 버튼 활성화

            let filenameId = clickId.split("_")[1];     // cancel_을 제외한 id값

            document.getElementById('FileName_' + filenameId).style.textDecoration = "line-through";    // 삭제를 눌렀을 때 파일 이름이 담긴 태그의 텍스트에만 줄 긋기

            arr.push(filenameId);     // 빈 배열 arr에 ea_no 값을 push
            document.getElementById('deleteId').value = arr;    // deleteId 라는 input hidden 태그의 value 에 arr 값을 부여
            return arr;
        }

        // 파일 취소버튼 활성화 함수
        function deleteCancelBtn(cancelId){
            document.getElementById(cancelId).style.display = "none";   // 취소 버튼 비활성화

            let clickId = "delete_" + cancelId.split("_")[1];   // 삭제 버튼의 id 값

            document.getElementById(clickId).style.display = "flex";    // 삭제 버튼 활성화

            let filenameId = clickId.split("_")[1];     // delete_를 제외한 id 값

            document.getElementById('FileName_' + filenameId).style.textDecoration = "none";    // 취소를 눌렀을 때 파일 이름이 담긴 태그의 텍스트에 그여있던 줄 삭제

            arr.pop(filenameId);    // push 되었던 배열 arr 에 값 pop 으로 삭제
            document.getElementById('deleteId').value = arr;    // deleteId 라는 input hidden 태그의 value 에 arr 값을 부여
            return arr;
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
<!--        <input type="file" name="file[]" multiple='multiple' ><br><br><br><br>-->
        <div class="holeFileIncreaseDiv">
            <p onclick="createFileInputs()" style="cursor: pointer">파일폼 추가하려면 클릭</p>
        <div id="fileIncreaseDiv" class="fileIncreaseDiv">
        </div>
        </div><br><br><br>
        <input type="submit" value="글 등록">
    </form>
    <?php
} else{
?>
    <h2 class="headerTitle">게시글 수정</h2>
    <form class="form" name="fixForm" action="/board/bExec.php" onsubmit="return writeCheck()" enctype="multipart/form-data" method="post">
        <input type="hidden" name="mode" value="fixPost">
        <input type="hidden" name="ec_no" value="<? echo $defaultPostNum?>">
        <input type="hidden" name="deleteId" id="deleteId" value="">
        <input type="hidden" name="parents" value="<?php echo $row['parents']?>">
        <input type="hidden" name="boardlist" value="<?php echo $row['ecc_boardlist']?>">
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
            <div class="holeFileIncreaseDiv">
                <p onclick="createFileInputs()" style="cursor: pointer">파일폼 추가하려면 클릭</p>
                <div id="fileIncreaseDiv" class="fileIncreaseDiv">
                </div>
            </div><br>
        <div class="fileName"><br>
            <p>기존 업로드 파일</p>
        <?php
        if($fileRows[0]['ea_filename']){
            ?>
            <?php
            foreach ($fileRows as $item)
            {
                ?>
                <div class="fileDiv" id="<?php echo $item['ea_no']?>">
                    <p id="FileName_<?php echo $item['ea_no']?>"><?php echo $item['ea_filename']?></p>
                    <div style="display: flex">
                        <p class="deleteFileBtn" id="delete_<?php echo $item['ea_no']?>" onclick="deleteCheckBtn(this.id)">삭제</p>
                        <p id="cancel_<?php echo $item['ea_no']?>" style="display: none; margin-left: 20px; cursor: pointer" class="deleteCancelBtn" onclick="deleteCancelBtn(this.id)">취소</p>
                    </div>
                </div>
                <?
            }
            ?>
            <?php
        }
        ?></div>
        </div><br><br>

        <input type="submit" value="글 수정">
    </form><br><br><br>
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


























