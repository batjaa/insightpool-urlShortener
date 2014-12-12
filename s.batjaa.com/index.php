<?php
class Connection
{
    protected $link;
    private $server, $db, $username, $password;
    
    public function __construct($server, $db, $username, $password)
    {
        $this->server = $server;
        $this->db = $db;
        $this->username = $username;
        $this->password = $password;
        $this->connect();
    }
    
    private function connect()
    {
        try {
        	$this->link = new PDO("mysql:host=$this->server;dbname=$this->db", $this->username, $this->password);
		    $this->link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch(PDOException $e) {
		    echo "Connection failed: " . $e->getMessage();
		}
    }

    public function getConnection(){
    	return $this->link;
    }
    
    public function __sleep()
    {
        return array('dsn', 'username', 'password');
    }
    
    public function __wakeup()
    {
        $this->connect();
    }
}

$servername = "mysql.batjaa.com";
$username = "batjaa";
$password = "82YrvNnB2huS";
$db = 'url_db';

$conn = (new Connection($servername, $db, $username, $password))->getConnection();

$key = $_REQUEST['key'];

$query = "SELECT * FROM ip_url WHERE `key`='$key'";
$stmt = $conn->prepare($query); 
$stmt->execute();
$url = $stmt->fetchObject();
if(!$url) {
    header("Location: http://shorten.batjaa.com/");
    exit;
}else{
    $ip = $_SERVER['REMOTE_ADDR'];
    $created_date = date("Y-m-d h:i:sa");

    $query = "INSERT INTO ip_url_visit (`url_id`, `ip`, `created_date`) VALUES ($url->id, '$ip', '$created_date')";
    // use exec() because no results are returned
    $conn->exec($query);


    header("Location: ".$url->url);
    exit;
}



?>