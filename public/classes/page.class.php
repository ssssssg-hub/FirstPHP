<?PHP
class PAGE {
    public $query;

    public $_columns, $_tables, $_wheres, $_orderby, $_joins, $_groupby;
    public $listTotal, $listSize, $pageNo, $offset, $pageLength;
    public $pageSize, $prevPage, $nextPage, $startPage, $endPage;

    /*
     * 클래스에서 처음으로 호출되는 함수(생성자)
     * */
    public function __construct(){
        # 리스트에 몇개를 보여줄 것인지
        $this->listSize =  8;
        # 페이지의 길이
        $this->pageLength = 2;
    }


    /*
     * 페이지의 리스트를 만드는 함수
     * */
    public function setListQuery($pageNo)
    {

        $this->pageNo = $pageNo;

        # 페이지 offset((현재 페이지 -1)* 리스트에 보여줄 게시글 갯수)
        $offset = ( $this->pageNo -1 ) * $this->listSize;

        $this->query = "";
        # 게시글 전체를 불러오는 쿼리문(implode()함수를 사용해 ","를 구분자로 하여 문자열을 배열로 변환해줌)
        $this->query = "select ".implode(", ", $this->_columns)." from ".implode(", ", $this->_tables);
        # 쿼리문에 join 구절을 사용할 경우 추가
        if(count($this->_joins)){
            $this->query .= " left join ".implode(" and ", $this->_joins);
        }
        # 쿼리문에 where 구절을 사용할 경우 추가
        if(count($this->_wheres)){
            $this->query .= " where ".implode(" and ", $this->_wheres);
        }
        # 쿼리문에 group by 구절을 사용할 경우 추가
        if(count($this->_groupby)) {
            $this->query .= " group by ".implode(", ", $this->_groupby);
        }
        # 쿼리문에 order by 구절을 사용할 경우 추가
        if(count($this->_orderby)) {
            $this->query .= " order by ".implode(", ", $this->_orderby);
        }
        # limit(한 페이지에 보여줄 갯수) = $this->listSize
        $this->query .= " limit {$this->listSize}";
        $this->query .= " offset {$offset}";
    }

    /*
     * 게시글 전체 개수를 구하는 함수
     * */
    public function setTotalCount()
    {
        global $dbconn;

        # select count(*)로 educommunity의 전체 게시글 갯수를 구하는 쿼리문
        $totalQuery = "select count(*) totalCnt from ".implode(", ", $this->_tables);
        if(count($this->_wheres)){
            $totalQuery .= " where ".implode(" and ", $this->_wheres);
        }

        $stmt = $dbconn->prepare($totalQuery);
        $stmt->execute();
        $row = $stmt->fetch();

        # $this->listTotal 이라는 변수가 게시글 전체 갯수를 담고있음
        $this->listTotal = $row['totalcnt'];
    }

    /*
     * 페이징 처리 로직 함수
     * */
    public function pageNationVariables()
    {
        # 전체 페이지 갯수
        $this->setTotalCount();

        # 페이지는 몇개인지 (전체 게시글 / listSize 인 8)의 값을 올림
        $this->pageSize = ceil($this->listTotal / $this->listSize);

        # 페이지 블럭의 이전 이후 조건
        # (짝수페이지는 이전을 눌렀을때 -2, 다음을 눌렀을때 +1)
        # (홀수페이지는 이전을 눌렀을떄 -1, 다음을 눌렀을떄 +2)
        if($this->pageNo % 2 == 0){
            $this->prevPage = $this->pageNo - 2;
            $this->nextPage = $this->pageNo + 1;
        } else{
            $this->prevPage = $this->pageNo - 1;
            $this->nextPage = $this->pageNo + 2;
        }

        # 현재 페이지 / 2의 나머지에서 -1 하면 현재 페이지 블럭. 현재 페이지 블럭 * 페이지수(2) + 1. 1보다 작은 수가 되지 않도록
        $this->startPage = (ceil($this->pageNo / $this->pageLength) - 1) * $this->pageLength + 1;
        if($this->startPage <= 0){
            $this->startPage = 1;
        }

        # 마지막 페이지는 현재 페이지 블럭 * 2. 마지막 페이지가 페이지 총 길이보다 크면 두개가 동일한 값이 되도록
        $this->endPage = ceil($this->pageNo / $this->pageLength) * $this->pageLength;
        if ($this->endPage >= $this->pageSize){
            $this->endPage = $this->pageSize;
        }
    }

    /*
     * 페이징 처리한 것을 html에 호출하기 위한 함수
     * */
    public function getPageNavi()
    {
        $boardNo = $_GET['boardNo'];
        $this->setTotalCount();
        $this->pageNationVariables();

        # 페이징처리 html 문자열
        $strPageNavi = "<div class='naviDiv'>";
        $strPageNavi .= "<p>";
        $strPageNavi .= " <a href=\"/board/board.php?pageNo=1&boardNo={$boardNo}\"><span>처음으로</span></a>";

        # 현재 페이지가 전체 페이지 수보다 같거나 작을때 a 태그 활성화
        if($this->pageNo >= $this->pageSize){
            $strPageNavi .= " <a href=\"/board/board.php?pageNo={$this->prevPage}&boardNo={$boardNo}\">이전</a>";
        };

        # 시작 페이지 숫자부터 마지막 페이지 숫자까지 1씩 증가시키면서 해당 페이지 a태그 활성화
        for ($page = $this->startPage; $page <= $this->endPage; $page ++){
            $strPageNavi .= " <a href=\"/board/board.php?pageNo={$page}&boardNo={$boardNo}\">{$page}</a>";
        }

        # 현재페이지가 전체 페이지 수보다 작을때 a 태그 활성화
        if($this->pageNo < $this->pageSize){
            $strPageNavi .= " <a href=\"/board/board.php?pageNo={$this->nextPage}&boardNo={$boardNo}\">다음</a>";
        }
        $strPageNavi .= " <a href=\"board.php?pageNo={$this->pageSize}&boardNo={$boardNo}\">마지막으로</a>";
        $strPageNavi .= "</p>";
        $strPageNavi .= "</div>";

        return $strPageNavi;
    }

}
?>