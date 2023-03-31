<?PHP
class PAGE {
    public $query;

    public $_columns, $_tables, $_wheres, $_orderby;
    public $listTotal, $listSize, $pageNo, $offset, $pageLength;
    public $pageSize, $prevPage, $nextPage, $startPage, $endPage;
    public function __construct(){
        // 리스트에 몇개를 보여줄 것인지
        $this->listSize =  8;
        $this->pageLength = 2;
    }

    public function setListQuery($pageNo=1)
    {

        $this->pageNo = $pageNo;
        $offset = ( $this->pageNo -1 ) * $this->listSize;

        $this->query = "";
        $this->query = "select ".implode(", ", $this->_columns)." from ".implode(", ", $this->_tables);
        #gfggh
        if(count($this->_wheres)){
            $this->query .= " where ".implode(" and ", $this->_wheres);
        }
        #fghfgh
        if(count($this->_orderby)) {
            $this->query .= " order by ".implode(", ", $this->_orderby);
        }

        $this->query .= " limit {$this->listSize}";
        $this->query .= " offset {$offset}";
    }

    public function setTotalCount()
    {
        // 총 게시글 개수를 구하기
        global $dbconn;

        $totalQuery = "select count(*) totalCnt from ".implode(", ", $this->_tables);
        if(count($this->_wheres)){
            $totalQuery .= " where ".implode(" and ", $this->_wheres);
        }

        $stmt = $dbconn->prepare($totalQuery);
        $stmt->execute();
        $row = $stmt->fetch();

        $this->listTotal = $row['totalcnt'];
    }

    public function pageNationVariables()
    {
        $this->setTotalCount();
        //페이지는 몇개인지(전체 게시글 / listSize 인 8)
        $this->pageSize = ceil($this->listTotal / $this->listSize);

        // 페이지 블럭의 이전 이후 조건(이전이나 이후를 눌렀을 때 해당 블럭의 첫번째 페이지로 넘어가도록
        if($this->pageNo % 2 == 0){
            $this->prevPage = $this->pageNo - 3;
            $this->nextPage = $this->pageNo + 1;
        } else{
            $this->prevPage = $this->pageNo - 2;
            $this->nextPage = $this->pageNo + 2;
        }

        // 현재 페이지 / 2의 나머지에서 -1 하면 현재 페이지 블럭. 현재 페이지 블럭 * 페이지수(2) + 1. 1보다 작은 수가 되지 않도록
        $this->startPage = (ceil($this->pageNo / $this->pageLength) - 1) * $this->pageLength + 1;
        if($this->startPage <= 0){
            $this->startPage = 1;
        }
        // 마지막 페이지는 현재 페이지 블럭 * 2. 마지막 페이지가 페이지 총 길이보다 크면 두개가 동일한 값이 되도록
        $this->endPage = ceil($this->pageNo / $this->pageLength) * $this->pageLength;
        if ($this->endPage >= $this->pageSize){
            $this->endPage = $this->pageSize;
        }
    }

    public function getPageNavi()
    {
        $boardNo = $_GET['boardNo'];
        $this->setTotalCount();
        $this->pageNationVariables();

        $strPageNavi = "<div class='naviDiv'>";
        $strPageNavi .= "<p>";
        $strPageNavi .= " <a href=\"/board/board.php?pageNo=1&boardNo={$boardNo}\"><span>처음으로</span></a>";
        if($this->pageNo > $this->pageLength){
            $strPageNavi .= " <a href=\"/board/board.php?pageNo={$this->prevPage}&boardNo={$boardNo}\">이전</a>";
        };
        for ($page = $this->startPage; $page <= $this->endPage; $page ++){
            $strPageNavi .= " <a href=\"/board/board.php?pageNo={$page}&boardNo={$boardNo}\">{$page}</a>";
        }
        if($this->pageNo < $this->pageSize - 1){
            $strPageNavi .= " <a href=\"/board/board.php?pageNo={$this->nextPage}&boardNo={$boardNo}\">다음</a>";
        }
        $strPageNavi .= " <a href=\"board.php?pageNo={$this->pageSize}&boardNo={$boardNo}\">마지막으로</a>";
        $strPageNavi .= "</p>";
        $strPageNavi .= "</div>";

        return $strPageNavi;
    }

}
?>