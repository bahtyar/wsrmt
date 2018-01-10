<?php

require_once('DB_Connect.php');

class DB_Functions 
{

    private $con;

    //API key
    protected $API = 'AIzaSyAY1lULinTPFhCaqQ01s-ZkjuokhLrhqVI';
    //put your code here
    // constructor
    function __construct() {

        $database = new Db();;
        $db = $database->dbConn();
        $this->con = $db;
        if(session_status()!=2){session_start();}
        
    }

    public function validateAPI($api){

        if($this->API !== $api){
            return false;
        }else{

            return true;
        }
    }

    public function runQuery($sql)
    {
        $stmt = $this->con->prepare($sql);
        return $stmt;
    }

    public function register($username, $email, $password, $nama, $jabatan, $telpon) {
        try
        {
            // $uuid = uniqid('', true); (ini variabel nomor unik)
            $new_password = password_hash($password, PASSWORD_DEFAULT);           
            $result = $this->con->prepare("INSERT INTO user( username, email, password, nama, jabatan, telpon, created_at) VALUES(:username, :email, :password, :nama, :jabatan, :telpon, NOW())");

            // $result->bindparam(":uuid", $uuid);
            $result->bindparam(":username", $username);
            $result->bindparam(":email", $email);
            $result->bindparam(":password", $new_password);
            $result->bindparam(":nama", $nama);
            $result->bindparam(":jabatan", $jabatan);
            $result->bindparam(":telpon", $telpon);

            $result->execute();
            
            return $result;
        }

        catch(PDOException $e)
        {
            echo $e->getMessage();
        }
    } 

    public function doLogin($uname,$umail,$upass)
    {
        try
        {
            $stmt = $this->con->prepare("SELECT * FROM user WHERE username=:uname OR email=:umail ");
            $stmt->execute(array(':uname'=>$uname, ':umail'=>$umail));
            $userRow=$stmt->fetch(PDO::FETCH_ASSOC);
            if($stmt->rowCount() == 1)
            {
                if(password_verify($upass, $userRow['password']))
                {

                    $_SESSION['user_session'] = $userRow['user_id'];

                    return true;
                }
                else
                {
                    return false;
                }
            }
        }
        catch(PDOException $e)
        {
            echo $e->getMessage();
        }
    }


    public function is_loggedin()
    {
        if(isset($_SESSION['user_session']))
        {
            return true;
        }

        else
        {
            return false;
        }
    }

    public function loginservice(){
        $uname = "bimbim";
        $upass = "123456";

        $server = "http://localhost:8080/phploginwebservice/service/index_GET.php/";
        $API = 'AIzaSyAY1lULinTPFhCaqQ01s-ZkjuokhLrhqVI';
        $url  = $server."?tag=login&API={$API}&uname={$uname}&password={$upass}";
        $json = file_get_contents($url); 

        $obj = json_decode($json);
        $_SESSION['user_session'] =$obj->user->id;
        return true;

    }



    public function redirect($url)
    {
        header("Location: $url");
    }

    public function logout()
    {
        session_destroy();
        unset($_SESSION['user_session']);
        return true;
    }        

    /**
     * Check user is existed or not
     */
    public function isUserExisted($email) {
        $result = $this->con->prepare("SELECT email from user WHERE email =:email");
        $result->execute(array(':email'=>$email));
        $userRow=$result->fetch(PDO::FETCH_ASSOC);

        if($result->rowCount() == 1)
        {
            // user existed 
            return true;
        } else {
            // user not existed
            return false;
        }
    }


}

?>
