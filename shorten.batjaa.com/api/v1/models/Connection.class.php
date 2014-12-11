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

?>