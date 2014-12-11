<?php
require_once 'API.class.php';
require_once '../models/Url.class.php';

class ShortenAPI extends API
{
    protected $User;

    public function __construct($request, $origin) {
        parent::__construct($request);

        // Abstracted out for example
        /*
        $APIKey = new Models\APIKey();
        $User = new Models\User();

        if (!array_key_exists('apiKey', $this->request)) {
            throw new Exception('No API Key provided');
        } else if (!$APIKey->verifyKey($this->request['apiKey'], $origin)) {
            throw new Exception('Invalid API Key');
        } else if (array_key_exists('token', $this->request) &&
             !$User->get('token', $this->request['token'])) {

            throw new Exception('Invalid User Token');
        }

        $this->User = $User;
        */
    }

    /**
     * Example of an Endpoint
     */

    protected function urls($args){
        if($this->method == 'GET') {
            $argSize = count($args);
            if($argSize == 0){
                $url = new Url();
                $offset = $this->request['offset'];
                $limit = $this->request['limit'];
                return $url->getList($offset, $limit);
            }else if($argSize == 1){
                $url = new Url();
                return $url->get($args[0]);
            }
            
        } else if($this->method == 'POST') {
            $url = new Url();
            $url->key = (string)time();
            $url->url = $this->request['url'];
            $url->created_date = date("Y-m-d h:i:sa");
            $newUrl = $url->save();
            if($newUrl){
                return $newUrl;
            }else{
                throw new Exception('URL already exists');
            }
            // return "Your name is Batjaa test: ".$conn;
        } else {
            return "Only accepts POST requests";
        }
    }

    protected function example() {
        if ($this->method == 'GET') {
            $url = new Url();
            return $url->getList();
            // return "Your name is Batjaa test: ".$conn;
        } else {
            return "Only accepts GET requests";
        }
    }
 }
?>