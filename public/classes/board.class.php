<?php
include_once dirname(__FILE__)."/page.class.php";
class BOARD extends PAGE {
    public $insertId, $fixboardlist;

    /*
     * 게시판 전체 리스트를 가져오는 함수
     * page.class.php의 setListQuery 함수에서 쿼리문이 조합되고 여기서는 들어갈 문자열이 변수로 담겨있음
     * */
    public function getBoardList()
    {
        # PDO 사용하는 전역 변수 $dbconn = new PDO("pgsql:host=117.52.153.104;dbname=edu", "edu", "zjajtmfoq");
        global $dbconn, $_GET;

        $pageNo = $_GET['pageNo'];
        $boardNo = $_GET['boardNo'];

        # 각 변수는 컬럼, 테이블, 조인 등 필요한 쿼리문을 문자열로 쪼개서 변수에 담고 있음
        $this->_columns  = array("ec.ec_no","ec.em_no","ec_subject","ec_regdt", "em.em_id", "em.em_name", "parents","ec_depth","ec_de_depth",  "MAX(ea.ea_filename) as ea_filename", "ecc_boardlist");
        $this->_tables  = array("educommunity ec");
        $this->_joins = array("eduattach ea on ec.ec_no = ea.ec_no left join edumember em on ec.em_no = em.em_no");
        $this->_wheres = array("ecc_boardlist = '{$boardNo}'");
        $this->_groupby = array("ec.ec_no, ec.em_no, ec_subject, ec_regdt, em.em_id, em.em_name, parents, ec_depth, ec_de_depth, ecc_boardlist");
        $this->_orderby = array("coalesce(parents,ec.ec_no) desc, coalesce(ec_depth,ec.ec_no) asc, coalesce(ec_de_depth,ec.ec_no) asc, ec_regdt asc");
        $this->setListQuery($pageNo);

        # PDO 사용하면 활용할 수 있는 준비구문(prepare statement). SQL 인젝션 공격을 막을 수 있고 애플리케이션의 성능이 향상됨
        $stmt = $dbconn->prepare($this->query);

        # PDO Statement 객체가 가진 쿼리를 실행
        $stmt->execute();

        # POD Statement 객체가 실행한 쿼리의 결과값 가져오기
        # fetch()은 결과를 한개씩 반환함. 결과를 전부 출력하려면 반복문 사용해야함.
        # fetchAll()은 결과를 배열로 한번에 전부 반환.
        $rows = $stmt->fetchAll();

        return $rows;
    }

    /*
     * 게시글 작성할 때 호출되는 함수
     * educommunity 테이블과 educommuclassifi 테이블에 각각 insert문을 실행시켜서 값을 삽입하고 있음.
     * */
    public function boardWrite()
    {
            global $dbconn;

            # parents, depth, de_depth 값은 post로 넘어온 값이 있을 수도 없을 수도 있기 때문에
            # 만약 없으면 null이라는 값이 들어가야 하기에 삼항연산자를 사용해서 post 값이 있을땐 그 값을, 없을땐 null 값을 부여
            $parents = $_POST['parents'] ? $_POST['parents'] : "NULL";
            $depth = $_POST['depth'] ? $_POST['depth'] : "NULL";
            $de_depth = $_POST['de_depth'] ? $_POST['de_depth'] : "NULL";

            $sql = "insert into educommunity (ec_subject, ec_content, em_no, em_id, parents, ec_depth, ec_de_depth, ecc_boardlist) values ('" . $_POST['subject'] . "','" . $_POST['content'] . "','" . $_SESSION['no'] . "', '" . $_SESSION['id'] . "', {$parents}, {$depth}, {$de_depth}, '" . $_POST['select'] . "')";
            $stmt = $dbconn->prepare($sql);
            $stmt->execute();
            $row = $stmt->fetch();

            # lastInsertId()는 가장 최근에 insert된 값의 pk값(고유한 값)을 가져올 수 있는 함수.
            # 이것을 사용해서 educommuclassifi 테이블에도 방금 educommunity 테이블에 insert 했던 pk값을 찾아서 부여할 수 있음
            $this->insertId = $dbconn->lastInsertId();

            $ecc_sql = "insert into educommuclassifi (ec_no, ecc_boardno) values ({$this->insertId}, {$_POST['select']})";
            $stmt = $dbconn->prepare($ecc_sql);
            $stmt->execute();
            $row = $stmt->fetch();

            # $_FILES 가 만약 존재할때 -> 이 조건을 첫번째 값인 $_FILES['file1']의 ['name'] 값의 문자열 길이가 0보다 클때로 조건을 부여했음
            # 이 경우에 boardImg()라는 함수를 실행시킴. 첨부파일이 없다면 해당 if 문은 실행되지 않음
            if(strlen($_FILES['file1']['name']) > 0){
                $this->boardImg();
            }

            alertHref("작성되었습니다.", "/board/board.php?pageNo=1&boardNo=1");

            exit;

    }

    /*
     * 첨부파일을 업로드할 때 호출되는 함수
     * */
    public function boardImg()
    {
        global $dbconn;

        # 파일 경로를 담은 변수. $_SERVER['DOCUMENT_ROOT']가 C:/data/WWWROOT/edu/public를 담고 있음
        # 경로를 적을때 윈도우에서 역슬래쉬, 리눅스나 다른 환경에서는 슬래쉬
        $dir = $_SERVER['DOCUMENT_ROOT']."attach/board/";

            # 파일을 여러개 첨부한 경우 해당 파일의 정보를 다 담을수 있도록 foreach 반복문 사용
            foreach ($_FILES as $key => $file) {
                $file_name = $file['name'];
                $file_tmp = $file['tmp_name'];
                $file_error = $file['error'];

                # [0] => cat [1] => jfif .을 기준으로 explode 한 배열
                $imgNameArray = explode(".", $file_name);

                # $imgNameArray 의 마지막 값. 즉 해당 파일의 type 값이 변수에 담김
                $imgType = end($imgNameArray);

                # substr = 문자열의 일부분을 추출하는 함수로 substr( string, start [, length ] )와 같은 형태의 문법
                # 마지막 length 에 음수 값을 주면 뒤에서부터 문자열을 자르게 됨.
                # $imgName 에는 filename 에서 $imgType 값을 제외한 모든 문자열이 담기게 됨. 즉, 파일의 이름이 담기는 것.
                $imgName = substr($file_name, 0, -strlen($imgType)-1);

                # md5 = 문자열에서 해시값을 생성하는 함수로 문자열을 변환한 값일 뿐 문자열이 같은 값을 변환하면 같은 해시값을 만들기 때문에 암호화 용도로는 맞지 않음
                # 같은 문자열일때 다른 값을 담기 위해 앞에 time()값을 붙여 사용했음. time() = 1970년 1월 1일 0시 0분 0초부터 세기 시작한 시간을 숫자로 표기한 값
                # $translatedName 에는 해시값 + $imgType 문자열을 담고 있음
                $translatedName = md5(time().$imgName);
                $translatedName .= '.' . $imgType;

                # this->insertId는 게시글 작성시 해당 글의 id 값을 가져오는 것이고 $_POST['ec_no']는 게시글 수정시의 ec_no를 가져오는 것
                # 게시글 작성시와 수정시 받아오는 id값(ec_no)값이 다른 형태이나 하나의 로직으로 처리하기 때문에 삼항연산자를 사용
                $id = $this->insertId ? $this->insertId : $_POST['ec_no'];

                /*
                 * file_error 메시지
                 * 0 = 파일이 성공적으로 업로드
                 * 1 = 업로드된 파일이 php.ini 의 upload_max_filesize 지시문을 초과.
                 * 2 = 업로드된 파일이 HTML 양식에 지정된 max_file_size 지시문을 초과.
                 * 3 = 업로드된 파일이 부분적으로만 업로드됨.
                 * 4 = 업로드된 파일이 없음.
                 * 6 = 임시 폴더가 없음.
                 * 7 = 디스크에 파일을 쓰지 못했음.
                 * 8 = php 확장으로 인해 파일 업로드가 중지되었음
                 * */
                # 4번을 제외한 해당되는 파일 에러 코드가 있을 시 동작하지 않고, 그 외에만 업로드가 동작하도록 조건문 설정
                # 4번은 이미 boardImg 함수를 실행하기 전 파일이 있을 시에만 실행되도록 설정해두었기 때문에 조건에서 제외
                if ($file_error == 1 || $file_error == 2) {
                    alertHref("이미지의 크기가 너무 큽니다.");
                    exit;
                } else if ($file_error != 4 && $file_error > 3) {
                    alertHref("이미지 업로드에 실패했습니다.");
                    exit;
                } else{
                    # $_FILES['file']['tmp_name'] == $file_tmp 에 서버가 업로드 받은 파일이 있는데
                    # move_uploaded_file()을 실행시켜 $file_tmp 위치에 있는 파일을 $dir.$translatedName 에 저장시킴
                    move_uploaded_file($file_tmp, $dir.$translatedName);

                    # 파일 권한 부여. 0777 = 자신 및 모든 소유자에게 읽기, 쓰기, 실행 모든 권한 추가. 그 외 0755, 0700, 0644 등 있음
                    # @는 실행중에 에러가 나더라도 무시해버린다는 뜻
                    @chmod($dir.$translatedName, 0777);

                    $sql = "insert into eduattach (ec_no, ea_filename, ea_uploadfile) values ($id,'$file_name', '$translatedName')";
                    $stmt = $dbconn->prepare($sql);
                    $stmt->execute();
                    $stmt->fetch();
                }
            }
        }



    /*
     * 게시글을 수정할 때 호출되는 함수.
     * 첨부파일 삭제 - 첨부파일 등록 - 게시글 수정의 순서로 진행
     * */
    public function boardFix()
    {
        global $dbconn;

        # $_POST['deleteId'] 가 만약 존재할때 -> 이 조건을 $_POST['deleteId'] 값의 문자열 길이가 0보다 클때로 조건을 부여했움
        # 이 경우에 fileDelete()라는 함수를 실행시킴. 삭제할 파일 id 가 없다면 해당 if 문은 실행되지 않음
        if(strlen($_POST['deleteId']) > 0){
            $this->fileDelete();
        }

        # $_FILES 가 만약 존재할때 -> 이 조건을 첫번째 값인 $_FILES['file1']의 ['name'] 값의 문자열 길이가 0보다 클때로 조건을 부여햤움
        # 이 경우에 boardImg()라는 함수를 실행시킴. 첨부파일이 없다면 해당 if 문은 실행되지 않음
        if(strlen($_FILES['file1']['name']) > 0){
            $this->boardImg();
        }

        $postNum = $_POST['ec_no'];

        $fixSubject = $_POST['fixSubject'];
        $fixContent = $_POST['fixContent'];
        $fixboardlist = $_POST['select'];

        # 게시글을 수정하는 쿼리문
        $sql ="update educommunity set ec_subject = '{$fixSubject}', ec_content = '{$fixContent}', ecc_boardlist = '{$fixboardlist}' where ec_no = '{$postNum}' and em_no = '{$_SESSION['no']}' ";

        $stmt = $dbconn->prepare($sql);
        $stmt->execute();

        # 수정한 게시판 value = $fixboardlist 와 기존 게시판 value = $_POST['ecc_boardlist'] 의 값이 다르면으로 조건을 부여했음
        # 이 경우에 fixBoardList()라는 함수를 실행시킴. 게시판 value 가 변경되지 않았다면 해당 if 문을 실행되지 않음.
        if($fixboardlist != $_POST['ecc_boardlist']){
            $this->fixBoardList();
        }

        alertHref("게시글이 수정되었습니다.", "/board/board.php?pageNo=1&boardNo=1");

    }

    /*
     * 첨부파일을 삭제할 때 실행되는 함수
     * */
    public function fileDelete()
    {
        global $dbconn;

        # $deleteId 값은 1 이나 2 혹은 3 같은 단수가 될 수도 1,2,3 같은 복수가 될 수도 있음
        $deleteId = $_POST['deleteId'];

        # delete 할 때 여러 값을 삭제시킬 때 컬럼명 in (값) 의 형태를 사용
        $sql = "delete from eduattach where ea_no in ($deleteId)";
        $stmt = $dbconn->prepare($sql);
        $stmt->execute();

        return $stmt->fetch();
    }

    /*
     * 게시글 수정 페이지 진입시 게시글의 기존 데이터를 불러오는 함수
     * */
    public function fixBoardData()
    {
        global $dbconn;
        $defaultPostNum =  $_GET['ec_no'];
        $sql = "select * from educommunity where ec_no='{$defaultPostNum}'";

        $stmt = $dbconn->prepare($sql);
        $stmt->execute();

        return $stmt->fetch();
    }

    /*
     * 게시글의 게시판이 이동 되었을 때 동작하는 함수
     * */
    public function fixBoardList()
    {
        global $dbconn;

        # 해당 게시글의 게시판이 이동 되었을 때 답글까지 옮겨주는 쿼리문
        $boardListSql = "update educommunity set ecc_boardlist = {$_POST['select']} where ecc_boardlist = {$_POST['boardlist']} and parents = {$_POST['ec_no']}";

        $stmt = $dbconn->prepare($boardListSql);
        $stmt->execute();

    }

    /*
     * 게시글을 삭제할 때 호출되는 함수
     * */
    public function boardDelete()
    {
        global $dbconn;

        $ec_no = $_POST['postNum'];

        # educommunity t테이블의 ec_no 값은 eduattach 의 FK 값이고,
        # educommuclassifi 테이블의 ecc_no 값은 educommunity 의 FK 값이다.
        # 이런 경우 다른 테이블에서 사용 중이기 때문에 함부로 변경하거나 삭제할 수 없기 때문에
        # 테이블간의 FK 관계를 끊고 새로 연결하면서 ON DELETE CASCADE 옵션을 적용해서
        # 부모 테이블에서 row 를 삭제하면 자식 테이블의 row 도 같이 삭제되어 일관성을 유지할 수 있게 했다.
        $sql = "delete from educommunity where ec_no = {$ec_no} or parents = {$ec_no}";

        $stmt = $dbconn->prepare($sql);
        $stmt->execute();

        alertHref("게시글이 삭제 되었습니다.", "/board/board.php?pageNo=1&boardNo=1");
    }

    /*
     * 답글을 삭제할 때 호출되는 함수
     * */
    public function replyBoardDelete()
    {
        global $dbconn;

        $ec_no = $_POST['postNum'];
        $parent = $_POST['parents'];
        $ec_depth = $_POST['ec_depth'];
        $ec_de_depth = $_POST['ec_de_depth'];

        if(strlen($ec_de_depth)>0){
            $sql = "delete from educommunity where ec_no = {$ec_no}";

            $stmt = $dbconn->prepare($sql);
            $stmt->execute();
        }
        if($ec_depth && strlen($ec_de_depth) == 0){
            $sql = "delete from educommunity where ec_no = {$ec_no} or ec_de_depth = {$ec_no}";

            $stmt = $dbconn->prepare($sql);
            $stmt->execute();
        }
        if($parent && strlen($ec_depth) == 0){
            $sql = "delete from educommunity where ec_no = {$ec_no} or ec_depth = {$ec_no}";

            $stmt = $dbconn->prepare($sql);
            $stmt->execute();
        }
        if(strlen($parent) == 0){
            $sql = "delete from educommunity where ec_no = {$ec_no} or parents ={$ec_no}" ;

            $stmt = $dbconn->prepare($sql);
            $stmt->execute();
        }


        alertHref("답글이 삭제 되었습니다.", "/board/board.php?pageNo=1&boardNo=1");
    }

    /*
     * 게시글을 상세조회할 때 호출되는 함수
     * */
    public function detailBoard()
    {
        global $dbconn;

        $defaultPostNum = $_GET['ec_no'];

        $sql = "select ec.ec_no,ec.em_no,ec_subject,ec_regdt, em.em_id, em.em_name, ec.ec_content, em.em_email, parents, ec_depth, ec_de_depth, ecc_boardlist from educommunity ec left join edumember em on ec.em_no = em.em_no where ec.ec_no='{$defaultPostNum}'";
        $stmt = $dbconn->prepare($sql);
        $stmt -> execute();

        return $stmt->fetch();

    }

    /*
     * 게시글 상세조회시 해당 게시글의 파일 정보들을 조회할 때 호출되는 함수
     * */
    public function detailFiles()
    {
        global $dbconn;

        $defaultGetNum = $_GET['ec_no'];

        $sql = "select * from eduattach where ec_no = '{$defaultGetNum}'";
        $stmt = $dbconn->prepare($sql);
        $stmt ->execute();

        return $stmt->fetchAll();
    }


    /*
     * 파일 다운로드 동작하는 함수
     * */
    public function fileDownload()
    {
        global $dbconn;

        # get 요청으로 보낸 값과 동일한 파일 row 를 찾는 쿼리문
        $sql = "select ea_filename, ea_uploadfile from eduattach where ec_no={$_GET['ec_no']} and ea_filename = '{$_GET['file']}'";
        $stmt = $dbconn->prepare($sql);
        $stmt->execute();

        $row = $stmt->fetch();

        $SfileName = $row['ea_filename'];
        $uploadFile = $row['ea_uploadfile'];
        $target_Dir = $_GET["target_Dir"];
        $file = $_SERVER['DOCUMENT_ROOT'] . $target_Dir . "/" . $uploadFile;

        header("Content-type: application/octet-stream"); // 헤더 설정
        header("Content-Length: " . filesize($file)); // 파일의 전체 졍로로 파일 사이즈 구해옴
        header("Content-Disposition: attachment; filename=$SfileName"); // 다운로드 되는 파일의 이름을 지정
        header("Content-Transfer-Encoding: binary"); // http 헤더를 이진으로 설정. 이진 데이터에는 텍스트, 이미지, 오디오 및 비디오를 포함한 모든 유형의 데이터가 포함될 수 있음
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); // Cache-Control http 헤더 설정
        header("Pragma: public"); // pragma http 헤더를 공용으로 설정
        header("Expires: 0"); // 만료 http 헤더를 0으로 설정

        # 파일 포인터 오픈
        # mode 읽기모드는 r , 쓰기모드는 w
        # rb = 바이너리 형식으로 읽겠다. rt = 텍스트 형식으로 읽겠다. wr = 바이너리 형식으로 쓰겠다. wt = 텍스트 형식으로 쓰겠다.
        $fp = fopen($file, "rb");

        # fread() 후 echo 하는것과 동일    fread() = 파일 내용 읽기 함수
        fpassthru($fp);
        fclose($fp); // 파일 포인터 종료
    }

    /*
     * 게시판을 생성할 때 동작하는 함수
     * */
    public function boardCreate()
    {
        global $dbconn;

        $sql = "insert into educommuclassifi (ecc_title) values ('" . $_POST['boardName'] . "')";

        $stmt = $dbconn->prepare($sql);
        $stmt -> execute();
        $row = $stmt->fetch();

        alertHref("게시판이 생성 되었습니다.", "/board/board.php?pageNo=1&boardNo=1");
        return $row;
    }

    /*
     * 게시판 전체 리스트를 보여주는 함수
     * */
    public function boardList()
    {
        global $dbconn;

        $sql = "select * from educommuclassifi";

        $stmt = $dbconn->prepare($sql);
        $stmt -> execute();

        return $stmt->fetchAll();
    }


    /*
     * 게시판 이름을 가져오는 함수
     * */
    public function boardNo()
    {
        global $dbconn;

        $boardNo = $_GET['boardNo'];

        $sql = "select ecc_title from educommuclassifi where ecc_no = $boardNo";

        $stmt = $dbconn->prepare($sql);
        $stmt -> execute();

        return $stmt->fetch();
    }
}

?>

