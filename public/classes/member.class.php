<?PHP
    class MEMBER
    {

        public $_SESSION, $_GET, $_POST;

        public $userNo;

        public function __construct()
        {
            $this->userNo = $_SESSION['no'];

        }

        public function memberRegister()
        {
            global $dbconn;

            $sql = "insert into edumember (em_name, em_id, em_pwd, em_email, em_phone, em_address) values ('".$_GET['name']."','".$_GET['id']."', '".$_GET['password']."','".$_GET['email']."','".$_GET['phone']."','".$_GET['address']."')";
            $stmt = $dbconn->prepare($sql);
            $stmt->execute();

            $row = $stmt->fetch();
            alertHref("회원가입이 완료 되었습니다.", "login.php");
            return $row;
        }

        public function memberLogin()
        {
            global $dbconn;

            $returnUrl = urldecode($_GET['returnUrl']);
            if(!strlen($_GET['returnUrl'])) $returnUrl = "/";

            $sql ="select * from edumember where em_id='{$_GET['id']}' and em_pwd = '{$_GET['password']}'";

            $stmt = $dbconn->prepare($sql);
            $stmt->execute();
            $row = $stmt->fetch();

            #아이디 혹은 비밀번호 불일치 처리
            if(!$row){
                alertHref("id 혹은 비밀번호를 확인하세요.", "goBack");
                return;
            }

            #로그인성공
            $_SESSION["name"] = $row['em_name'];
            $_SESSION["id"] = $row['em_id'];
            $_SESSION["no"] = $row['em_no'];

            alertHref("'{$row['em_name']}'님 환영합니다.", $returnUrl);

            return;
        }

        public function memberLogOut()
        {
            session_destroy();
            alertHref("로그아웃 되었습니다.", "/");
        }

        public function memberUpdate()
        {
            global $dbconn;

            $userUpdateName = $_POST['updateName'];
            $userUpdatePwd = $_POST['updatePassword'];
            $userUpdateEmail = $_POST['updateEmail'];
            $userUpdatePhone = $_POST['updatePhone'];
            $userUpdateAddress = $_POST['updateAddress'];

            $sql ="update edumember set em_pwd = '{$userUpdatePwd}', em_name = '{$userUpdateName}', em_email = '{$userUpdateEmail}', em_Phone = '{$userUpdatePhone}', em_address = '{$userUpdateAddress}' where em_no='{$this->userNo}'";

            $stmt = $dbconn->prepare($sql);
            $stmt->execute();
            $row = $stmt->fetch();

            alertHref("회원정보가 수정되었습니다.", "/");

            return $row;
        }

        public function memberOut()
        {
            global $dbconn;
            $sql = "delete from edumember where em_no = '{$this->userNo}'";

            $stmt = $dbconn->prepare($sql);
            $stmt->execute();
            $row = $stmt->fetch();

            session_destroy();

            alertHref("회원탈퇴 되었습니다.", "/");
            return $row;
        }

        public function verifyMember()
        {
            global $dbconn;
            $sql = "select * from edumember where em_no='{$this->userNo}'";

            $stmt = $dbconn->prepare($sql);
            $stmt->execute();

            $row = $stmt->fetch();
            return $row;
        }
    }
//?>