<?PHP

    /*
     * <pre>태그에 echo print_r()을 사용할 수 있게 해주는 함수
     * <pre>태그는 미리 정의된 형식의 텍스트를 정의할 때 사용한다.
     * 독특한 서식의 텍스트나 컴퓨터 코드 등을 HTML 문서에 그대로 표현할 수 있다.
     * */
    function pre($arr)
    {
        echo "<pre>";
        print_r($arr);
        echo "</pre>";
    }

    /*
     * alert와 location.href를 동시에 동작할 수 있게 해주는 함수
     * */
    function alertHref($msg="", $href="")
    {
        echo "<script>";
        if (strlen($msg)) {
            echo "alert(\"{$msg}\");";
        }

        if ($href == "goBack") {
            echo "history.go(-1);";
        } else if ($href == "reload") {
            echo "location.reload();";
        } else if (strlen($href)) {
            echo "location.href=\"{$href}\";";
        }
        echo "</script>";
    }
?>