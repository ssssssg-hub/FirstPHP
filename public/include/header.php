<?PHP
# include = 같은 파일 여러 번 포함 가능 / 포함할 파일이 없어도 다음 코드 실행. header.inc 파일 읽어옴
include $_SERVER['DOCUMENT_ROOT']."/include/header.inc.php";

# returnUrl 변수는 GET 요청의 'returnUrl' 값을 url 디코딩한 값
$returnUrl = urldecode($_GET['returnUrl']);
?>

<html>
<header>
    <link href="../css/header.css" rel="stylesheet" >
    <script>

    // 로그아웃 실행 함수
    function memberLogOutFunction() {

        if(!confirm("로그아웃을 하시겠습니까?")){
            alert("취소되었습니다.")
            return false;
        } else {
            return location.href='/member/exec.php?mode=logOut';
        }
    }

    // 회원탈퇴 실행 함수
    function memberOutFunction() {
        if(!confirm("탈퇴 하시겠습니까?")){
            alert("취소되었습니다.")
            return false;
        } else {
            return location.href='/member/exec.php?mode=memberOut';
        }

    }
    </script>
</header>
<body>
<?
    if($_SESSION){
?>
            <div class="headerDiv">
                <div class="boardBtn">
                <button onclick="location.href='board/board.php?pageNo=1&boardNo=1'">board</button>
                </div>
                <div class="userDiv">
                <p class="headerName"><span style="font-weight: bold; font-size: large;color: beige"><? echo $_SESSION["name"]?></span>님 안녕하세요.</p>
                <button onclick="memberLogOutFunction()">로그아웃</button>
                <button onclick="location.href='/member/form.php'">회원수정</button>
                <button onclick="memberOutFunction()">회원탈퇴</button>
                </div>
            </div>
<?
    }else {
?>
        <button onclick="location.href='/member/login.php?returnUrl=<?=urlencode($_SERVER['REQUEST_URI'])?>'">login</button>
<?
    }
?>
</body>