<?PHP
include $_SERVER['DOCUMENT_ROOT']."/include/header.php";

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>마이페이지</title>
    <link rel="stylesheet" href="../css/mypage.css">
    <script>
        // 각 카테고리 버튼을 클릭할 때마다 넘어오는 파라미터값을 가져와서 담는 변수. 최종적으로 category라는 키의 파라미터값은 paramsValue에 담김
        const params = new URLSearchParams(window.location.search);
        const paramsValue = params.get('category');

        // DOMContentLoaded 가 되었을때 mypage-header라는 id를 가진 태그에 paramsValue 값을 넣음. 즉, 카테고리명이 헤더에 뜨게해줌
        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById('mypage-header').innerHTML = paramsValue;
        });

        // iframe 의 사이즈 조절을 해줌. 기본적으로 iframe 사이즈는 작아서 width 와 height 사이즈를 100%로 줘서 부모 html 의 사이즈와 동일해지게 했음. iframe 이 뭔지 모르겠다면 찾아보시길
        window.addEventListener('DOMContentLoaded', () => {
            const iframe = document.querySelector('iframe');
            iframe.style.width = '100%';
            iframe.style.height = '100%';
        });
    </script>
</head>
<body>
<h2 class="mypage-header" id="mypage-header"></h2>
<div class="mypage-container">
    <div class="category-container">
        <!-- 버튼마다 onclick 함수를 달았는데 경로는 너가 생성하는 폴더로 바꿔야함. ?뒤에는 파라미터 값이 오는데 이 값을 가지고 어떤 카테고리 페이지에 있는지 구분할 수 있게됨       -->
        <button class="category-item" onclick="location.href='/member/mypage.php?category=카테고리1'">카테고리1</button>
        <button class="category-item" onclick="location.href='/member/mypage.php?category=카테고리2'">카테고리2</button>
        <button class="category-item" onclick="location.href='/member/mypage.php?category=회원정보수정'">회원정보수정</button>
    </div>
    <div class="category-content" id="category-content">
    <script>

        // 카테고리가 어떤 거냐에 따라서 카테고리 밑에 따라올 내용을 부여해줌. innerHTML, iframe 찾아보시길. src 경로는 각 카테고리별로 너가 파일을 만들어서 그 경로를 넣어줘야함
        if (paramsValue === '카테고리1') {
            document.getElementById('category-content').innerHTML = '<iframe src="category1.html" style="border: none;"></iframe>';
        } else if (paramsValue === '카테고리2') {
            document.getElementById('category-content').innerHTML = '<iframe src="category2.html" style="border: none;"></iframe>';
        } else if (paramsValue === '회원정보수정') {
            document.getElementById('category-content').innerHTML = '<iframe src="myinfo.html" style="border: none;"></iframe>';
        } else {
            // 그 외의 경우에는 에러 처리 등을 작성
            document.write('<h3>잘못된 접근입니다.</h3>');
        }
    </script>
    </div>
</div>
</body>
</html>




<!--
.mypage-header {
display: flex;
justify-content: center;
margin-bottom: 50px;
}

.category-container {
display: flex;
justify-content: space-between;
align-items: center;
margin-top: 20px;
}

.category-item {
cursor: pointer;
width: 100%;
max-width: 1500px;
height: 50px;
border: none;
background-color: white;
border-bottom: 1px solid lightgray;
margin: 0px 10px 30px 10px;
}

.category-item:hover{
background-color: lawngreen;
}
-->