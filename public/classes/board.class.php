<?php
include_once dirname(__FILE__)."/page.class.php";
class BOARD extends PAGE {
    public $insertId;

    public function getBoardList()
    {
        global $dbconn, $_GET;
        $pageNo = $_GET['pageNo'];
        $boardNo = $_GET['boardNo'];

        $this->_columns  = array("ec.ec_no","ec.em_no","ec_subject","ec_regdt", "em.em_id", "em.em_name", "parents","ec_depth","ec_de_depth",  "MAX(ea.ea_filename) as ea_filename", "ecc_boardlist");
        $this->_tables  = array("educommunity ec left join eduattach ea on ec.ec_no = ea.ec_no left join edumember em on ec.em_no = em.em_no where ecc_boardlist = '{$boardNo}' GROUP BY ec.ec_no, ec.em_no, ec_subject, ec_regdt, em.em_id, em.em_name, parents, ec_depth, ec_de_depth, ecc_boardlist");
        $this->_orderby = array("coalesce(parents,ec.ec_no) desc, coalesce(ec_depth,ec.ec_no) asc, coalesce(ec_de_depth,ec.ec_no) asc, ec_regdt asc");
        $this->setListQuery($pageNo);

//        pre($this->query);

        $stmt = $dbconn->prepare($this->query);
        $stmt->execute();
        $rows = $stmt->fetchAll();

        return $rows;
    }
    public function boardWrite()
    {
            global $dbconn;

            $parents = $_POST['parents'] ? $_POST['parents'] : "NULL";
            $depth = $_POST['depth'] ? $_POST['depth'] : "NULL";
            $de_depth = $_POST['de_depth'] ? $_POST['de_depth'] : "NULL";

            $sql = "insert into educommunity (ec_subject, ec_content, em_no, em_id, parents, ec_depth, ec_de_depth, ecc_boardlist) values ('" . $_POST['subject'] . "','" . $_POST['content'] . "','" . $_SESSION['no'] . "', '" . $_SESSION['id'] . "', {$parents}, {$depth}, {$de_depth}, '" . $_POST['select'] . "')";
            $stmt = $dbconn->prepare($sql);
            $stmt->execute();
            $row = $stmt->fetch();

            $this->insertId = $dbconn->lastInsertId();
            pre($this->insertId);

            $ecc_sql = "insert into educommuclassifi (ec_no, ecc_boardno) values ({$this->insertId}, {$_POST['select']})";
            $stmt = $dbconn->prepare($ecc_sql);
            $stmt->execute();
            $row = $stmt->fetch();

            if(strlen($_FILES['file']['name'][0]) > 0){
                $this->boardImg();
            }

            alertHref("작성되었습니다.", "/board/board.php?pageNo=1&boardNo=1");

            exit;

    }

    public function boardImg()
    {
        global $dbconn;

        $dir = $_SERVER['DOCUMENT_ROOT']."/attach/board/";

        foreach ($_FILES['file']['name'] as $key => $name) {
            $file_name = $_FILES['file']['name'][$key];
            $file_tmp = $_FILES['file']['tmp_name'][$key];
            $file_error = $_FILES['file']['error'][$key];

            $imgNameArray = explode(".", $file_name);
            $imgType = end($imgNameArray);
            $imgName = substr($file_name, 0, -strlen($imgType)-1);

            $translatedName = md5(time().$imgName);
            $translatedName .= '.' . $imgType;

            if ($file_error == 1 || $file_error == 2) {
                alertHref("이미지의 크기가 너무 큽니다.");
                exit;
            } else if ($file_error != 4 && $file_error > 3) {
                alertHref("이미지 업로드에 실패했습니다.");
                exit;
            } else{
                move_uploaded_file($file_tmp, $dir.$translatedName); #파일 업로드
                @chmod($dir.$translatedName, 0777);

                $sql = "insert into eduattach (ec_no, ea_filename, ea_uploadfile) values ($this->insertId,'$name', '$translatedName')";
                $stmt = $dbconn->prepare($sql);
                $stmt->execute();
                $row = $stmt->fetch();
            }
        }
    }


    public function boardFix()
    {
        global $dbconn;

        $postNum = $_POST['ec_no'];

        $fixSubject = $_POST['fixSubject'];
        $fixContent = $_POST['fixContent'];
        $fixboardlist = $_POST['select'];

        pre($_FILES);
        pre($_POST);
//        if($_FILES) {
//            $this->boardImg();
//            $name = $this->boardImg()[0];
//            $translatedName = $this->boardImg()[1];
//            $fileSql = "select ec_filename, ec_uploadfile from eduattach where ec_no='{$postNum}'";
//            $stmt = $dbconn->prepare($fileSql);
//            $stmt->execute();
//            $row = $stmt->fetch();
//
//            $fileName = $name ? $name : $row['ec_filename'];
//            $upload = $_FILES['file']['name'] ? $translatedName : $row['ec_uploadfile'];
//
//            $fileSql = "update eduattach set ea_filename = '{$fileName}', ea_uploadfile = '{$upload}', ec_no = '{$postNum}'";
//            pre($fileSql);
//            $stmt = $dbconn->prepare($fileSql);
//            $stmt->execute();
//            $fileRow = $stmt->fetch();
//        } else {
//            $file = $_FILES['file'];
//            $name = $file["name"];
//            $imgNameArray = explode(".", $name);
//            $imgType = end($imgNameArray);
//            $imgName = substr($name, 0, -strlen($imgType)-1);
//            $dir = $_SERVER['DOCUMENT_ROOT']."/attach/board/";
//
//            $translatedName = md5(time().$imgName);
//            $translatedName .= '.' . $imgType;
//
//            if ($file['error'] == 1 || $file['error'] == 2) {
//                alertHref("이미지의 크기가 너무 큽니다.");
//                exit;
//            } else if ($file['error'] != 4 && $file['error'] > 3) {
//                alertHref("이미지 업로드에 실패했습니다.");
//                exit;
//            } else{
//                move_uploaded_file($file['tmp_name'], $dir.$translatedName); #파일 업로드
//                @chmod($dir.$translatedName, 0777);
//
//                $sql = "insert into eduattach (ec_no, ea_filename, ea_uploadfile) values ('$postNum','$name', '$translatedName')";
//                $stmt = $dbconn->prepare($sql);
//                $stmt->execute();
//                $row = $stmt->fetch();
//
//                return([$name, $translatedName]);
//            }
//        }
//
//        $sql ="update educommunity set ec_subject = '{$fixSubject}', ec_content = '{$fixContent}', ecc_boardlist = '{$fixboardlist}' where ec_no = '{$postNum}' and em_no = '{$_SESSION['no']}' ";
//
//        $stmt = $dbconn->prepare($sql);
//        $stmt->execute();
//        $row = $stmt->fetch();
//
//        alertHref("게시글이 수정되었습니다.", "/board/board.php?pageNo=1");
//        return $row;
    }
    public function fixBoardData()
    {
        global $dbconn;
        $defaultPostNum =  $_GET['postNum'];
        $sql = "select * from educommunity where ec_no='{$defaultPostNum}'";

        $stmt = $dbconn->prepare($sql);
        $stmt->execute();

        $row = $stmt->fetch();
        return $row;
    }

    public function boardDelete()
    {
        global $dbconn;
        $parents = $_POST['parents'];
        $depth = $_POST['depth'];
        $de_depth = $_POST['de_depth'];
        $ec_no = $_POST['postNum'];

        $sql = "delete from educommunity where ec_no = {$ec_no}";

        $stmt = $dbconn->prepare($sql);
        $stmt->execute();

        alertHref("게시글이 삭제 되었습니다.", "/board/board.php?pageNo=1&boardNo=1");
    }

    public function detailBoard()
    {
        global $dbconn;
        $defaultPostNum = $_GET['ec_no'];
        $sql = "select ec.ec_no,ec.em_no,ec_subject,ec_regdt, em.em_id, em.em_name, ec.ec_content, em.em_email, parents, ec_depth, ec_de_depth from educommunity ec left join edumember em on ec.em_no = em.em_no  where ec.ec_no='{$defaultPostNum}'";
        $stmt = $dbconn->prepare($sql);
        $stmt -> execute();
        $row = $stmt->fetch();

        $fileSql = "select * from eduattach where ec_no = '{$defaultPostNum}'";
        $stmt = $dbconn->prepare($fileSql);
        $stmt -> execute();
        $rows = $stmt->fetchAll();
        return [$row, $rows];
    }


    public function fileDownload()
    {
        global $dbconn;

        $sql = "select ea_filename, ea_uploadfile from eduattach where ec_no={$_GET['ec_no']}";
        $stmt = $dbconn->prepare($sql);
        $stmt -> execute();

        $row = $stmt->fetch();

        $SfileName = $row['ea_filename'];
        $uploadFile = $row['ea_uploadfile'];
        $target_Dir = $_GET["target_Dir"];
        $file = $_SERVER['DOCUMENT_ROOT']. "/" . $target_Dir . "/" . $uploadFile;
        pre($row);
        pre($_GET);

//            $fileContents = file_get_contents($file);
//            echo $fileContents;

        header("Content-type: application/octet-stream");
        header("Content-Length: " . filesize($file));
        header("Content-Disposition: attachment; filename=$SfileName");
        header("Content-Transfer-Encoding: binary");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: public");
        header("Expires: 0");
        $fp = fopen($file, "rb");
        fpassthru($fp);
        fclose($fp);
    }


    public function boardCreate()
    {
        global $dbconn;

        pre($_POST);
        $sql = "insert into educommuclassifi (ecc_title) values ('" . $_POST['boardName'] . "')";
        pre($sql);
        $stmt = $dbconn->prepare($sql);
        $stmt -> execute();
        $row = $stmt->fetch();

        alertHref("게시판이 생성 되었습니다.", "/board/board.php?pageNo=1&boardNo=1");
        return $row;
    }

    public function boardList()
    {
        global $dbconn;

        $sql = "select * from educommuclassifi";
        $stmt = $dbconn->prepare($sql);
        $stmt -> execute();
        $rows = $stmt->fetchAll();
        return $rows;
    }
}

?>




<!--파일 다운로드 실패한 코드-->
<!--global $dbconn;-->
<!---->
<!---->
<!--$filename = $_GET["file"];-->
<!--$target_Dir = $_GET["target_Dir"];-->
<!--$file = $_SERVER['DOCUMENT_ROOT'] . $target_Dir . "/" . $filename;-->
<!---->
<!---->
<!--$sql = "select ec_filename, ec_uploadfile from educommunity where ec_filename = '{$filename}'";-->
<!--$stmt = $dbconn->prepare($sql);-->
<!--$stmt -> execute();-->
<!---->
<!--$row = $stmt->fetch();-->
<!--//-->
<!--//        pre($row);-->
<!--//        pre($filename);-->
<!--//        pre($target_Dir);-->
<!--//        pre($file);-->
<!--//        pre("$_SERVER[DOCUMENT_ROOT]attach/board/{$filename}");-->
<!--$filepath = "$_SERVER[DOCUMENT_ROOT]attach/board/{$filename}";-->
<!--if(file_exists($filepath)){-->
<!---->
<!--chmod($filepath, 0777);-->
<!--//            echo "파일 있음";-->
<!--//            exit;-->
<!--}-->
<!--$filesize = filesize($file);-->
<!---->
<!--$file_Sdata = file_get_contents($row['ec_filename']);-->
<!--$file_Cdata = file_get_contents("$_SERVER[DOCUMENT_ROOT]attach/board/{$filename}");-->
<!---->
<!--pre($filesize);-->
<!--//        pre($file_Sdata);-->
<!--//        pre($file_Cdata);-->
<!---->
<!--//        if ($file_Sdata==$file_Cdata){-->
<!--//            echo "true";-->
<!--//        } else{-->
<!--//            echo "false";-->
<!--//        }-->
<!---->
<!---->
<!--//        header("Content-type: application/octet-stream");-->
<!--//        header("Content-Length: " . filesize($file));-->
<!--//        header("Content-Disposition: attachment; filename=$filename");-->
<!--//        header("Content-Transfer-Encoding: binary");-->
<!--//        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");-->
<!--//        header("Pragma: public");-->
<!--//        header("Expires: 0");-->
<!--//-->
<!--//        echo $file_data;-->
<!---->
<!---->
<!---->
<!--//        if (is_file($file)) {-->
<!--//        if ($file_Cdata) {-->
<!--//-->
<!--//            header("Content-type: application/octet-stream");-->
<!--//            header("Content-Length: " . $filesize);-->
<!--//            header("Content-Disposition: attachment; filename=$filename");-->
<!--//            header("Content-Transfer-Encoding: binary");-->
<!--//            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");-->
<!--//            header("Pragma: public");-->
<!--//            header("Expires: 0");-->
<!--//            $fileContents = file_get_contents($filepath);-->
<!--//            $fp = fopen($file, "rb");-->
<!--//            fpassthru($fp);-->
<!--//            fclose($fp);-->
<!--//        } else {-->
<!--//            echo "해당 파일이 없습니다.";-->
<!--//        }-->
