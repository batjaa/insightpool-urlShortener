<?php
require_once('Constants.class.php');
require_once('Connection.class.php');
require_once('UrlVisit.class.php');

class Url
{
    public $id;
    public $key;
    public $url;
    public $created_date;
    public $urlVisits = array();

    public function save()
    {
        global $conn;
        if($this->getByUrl($this->url)){
            return false;
        }else{
            $sql = "INSERT INTO ip_url (`key`, `url`, `created_date`) VALUES ('$this->key', '$this->url', '$this->created_date')";
            // use exec() because no results are returned
            $conn->exec($sql);
            return $this;
        }
        
    }

    private function getLinks($url){
        $const = new Constants();
        return array(
            'rel' => 'self',
            'href' => $const::BASE_URL . $const::API . '/urls/' . $url->id
        );
    }

    private function getUrlVisits($urlId){
        global $conn;

        $query = "SELECT visit.* FROM ip_url AS url LEFT JOIN ip_url_visit AS visit ON url.id=visit.url_id WHERE url.id=$urlId";
        $statement = $conn->prepare($query);
        $statement -> execute();
        $statement -> setFetchMode(PDO::FETCH_CLASS, 'UrlVisit');
        return $statement -> fetchAll();
    }

    public function get($id)
    {
        global $conn;
        
        $stmt = $conn->prepare("SELECT * FROM ip_url WHERE id=$id"); 
        $stmt->execute();
        $url = $stmt->fetchObject("Url");
        $url->links = $this->getLinks($url);
        $url->urlVisits = $this->getUrlVisits($url->id);
        return $url;
    }
     
    public function getByUrl($url)
    {
        global $conn;
        
        $stmt = $conn->prepare("SELECT * FROM ip_url WHERE url='$url'"); 
        $stmt->execute();
        $url = $stmt->fetchObject("Url");
        $url->links = $this->getLinks($url);
        $url->urlVisits = $this->getUrlVisits($url->id);
        return $url;
    }

    public function getList($offset, $limit){
        global $conn;
        $const = new Constants();

        if(!$offset) $offset = 0;
        if(!$limit) $limit = $const::PAGE_SIZE;

        $query = "SELECT url.* FROM ip_url AS url LEFT JOIN ip_url_visit AS visit ON url.id=visit.url_id ORDER BY url.created_date DESC LIMIT $offset, $limit";
        $statement = $conn->prepare($query);
        $statement -> execute();
        $statement -> setFetchMode(PDO::FETCH_CLASS, 'Url');
        $urls = $statement -> fetchAll();

        $map = array();
        $ids = "";
        forEach($urls as $url){
            $url->links = $this->getLinks($url);
            $map[$url->id] = $url;
            $ids .= $url->id . ',';
        }
        $ids = trim($ids, ",");


        $query = "SELECT visit.* FROM ip_url AS url LEFT JOIN ip_url_visit AS visit ON url.id=visit.url_id WHERE url.id IN ($ids)";
        $statement = $conn -> prepare($query);
        $statement -> execute();
        $statement -> setFetchMode(PDO::FETCH_CLASS, 'UrlVisit');
        $urlVisits = $statement -> fetchAll();
        
        forEach($urlVisits as $urlVisit){
            if($map[$urlVisit->url_id]){
                array_push($map[$urlVisit->url_id]->urlVisits, $urlVisit);
            }
        }

        $response = array();
        $response['data'] = $urls;
        $response['links'] = array();

        if($offset > 0){
            array_push($response['links'], array('rel'=>'first', 'href'=>$const::BASE_URL . $const::API . '/urls?offset=0&limit=' . $limit));
            array_push($response['links'], array('rel'=>'prev', 'href'=>$const::BASE_URL . $const::API . '/urls?offset='.($offset-$limit).'&limit=' . $limit));
        }
        // Calculate last page
        $query = "SELECT count(*) FROM ip_url"; 
        $result = $conn->prepare($query); 
        $result->execute(); 
        $total = $result->fetchColumn(); 
        $lastOffest = floor($total/$limit) * $limit;
        if($offset < $lastOffest){
            array_push($response['links'], array('rel'=>'last', 'href'=>$const::BASE_URL . $const::API . '/urls?offset='. $lastOffest .'&limit=' . $limit));
            array_push($response['links'], array('rel'=>'next', 'href'=>$const::BASE_URL . $const::API . '/urls?offset='.($offset+$limit).'&limit=' . $limit));
        }

        return $response;
    }

    public function __tostring()
    {
        return $this->url;
    }
}