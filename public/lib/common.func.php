<?PHP
    function pre($arr)
    {
        echo "<pre>";
        print_r($arr);
        echo "</pre>";
    }

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