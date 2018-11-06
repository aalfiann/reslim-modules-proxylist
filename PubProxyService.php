<?php
namespace modules\proxylist;
use \modules\proxylist\PubProxy;
use \classes\Auth as Auth;                          //For authentication internal user
use \classes\JSON as JSON;                          //For handling JSON in better way
use \classes\CustomHandlers as CustomHandlers;      //To get default response message
use PDO;                                            //To connect with database

    class PubProxyService {

        var $api='',$level='',$type='',$country='',$not_country='',$port='',
            $google='',$https='',$post='',$user_agent='',$cookies='',$referer='',
            $limit=20,$last_check=0,$speed=0,$refresh=1800,$filepath='',$proxy='',$proxyauth='';

        // database var
        protected $db;

        //base var
        protected $basepath,$baseurl,$basemod;

        //master var
        var $username,$token;
        
        //multilanguage
        var $lang;
        
        //construct database object
        function __construct($db=null) {
			if (!empty($db)) $this->db = $db;
            $this->baseurl = (($this->isHttps())?'https://':'http://').$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']);
            $this->basepath = $_SERVER['DOCUMENT_ROOT'].dirname($_SERVER['PHP_SELF']);
			$this->basemod = dirname(__FILE__);
        }

        //Detect scheme host
        function isHttps() {
            $whitelist = array(
                '127.0.0.1',
                '::1'
            );
            
            if(!in_array($_SERVER['REMOTE_ADDR'], $whitelist)){
                if (!empty($_SERVER['HTTP_CF_VISITOR'])){
                    return isset($_SERVER['HTTPS']) ||
                    ($visitor = json_decode($_SERVER['HTTP_CF_VISITOR'])) &&
                    $visitor->scheme == 'https';
                } else {
                    return isset($_SERVER['HTTPS']);
                }
            } else {
                return 0;
            }
        }
        
        public function proxyList(){
            $pp = new PubProxy;
            $pp->api = $this->api;
            $pp->limit = $this->limit;
            $pp->level = $this->level;
            $pp->type = $this->type;
            $pp->country = $this->country;
            $pp->not_country = $this->not_country;
            $pp->last_check = $this->last_check;
            $pp->speed = $this->speed;
            $pp->port = $this->port;
            $pp->google = $this->google;
            $pp->https = $this->https;
            $pp->post = $this->post;
            $pp->cookies = $this->cookies;
            $pp->referer = $this->referer;
            $pp->user_agent = $this->user_agent;
            $pp->refresh = $this->refresh;
            $arraydata = json_decode($pp->make()->getJson());
            if(!empty($arraydata)){
                $data = [
                    'result' => $arraydata,
                    'status' => 'success',
                    'code' => 'RS501',
                    'message' => CustomHandlers::getreSlimMessage('RS501',$this->lang)
                ];
            } else {
                $data = [
                    'status' => 'error',
                    'code' => 'RS202',
                    'message' => CustomHandlers::getreSlimMessage('RS202',$this->lang)
                ];
            }
            return $data;
        }

        public function getProxy(){
            $pp = new PubProxy;
            $pp->api = $this->api;
            $pp->limit = $this->limit;
            $pp->level = $this->level;
            $pp->type = $this->type;
            $pp->country = $this->country;
            $pp->not_country = $this->not_country;
            $pp->last_check = $this->last_check;
            $pp->speed = $this->speed;
            $pp->port = $this->port;
            $pp->google = $this->google;
            $pp->https = $this->https;
            $pp->post = $this->post;
            $pp->cookies = $this->cookies;
            $pp->referer = $this->referer;
            $pp->user_agent = $this->user_agent;
            $pp->refresh = $this->refresh;
            $arraydata = $pp->make()->getProxy();
            if(!empty($arraydata)){
                $data = [
                    'result' => $arraydata,
                    'status' => 'success',
                    'code' => 'RS501',
                    'message' => CustomHandlers::getreSlimMessage('RS501',$this->lang)
                ];
            } else {
                $data = [
                    'status' => 'error',
                    'code' => 'RS202',
                    'message' => CustomHandlers::getreSlimMessage('RS202',$this->lang)
                ];
            }
            return $data;
        }

        public function showList(){
            if (Auth::validToken($this->db,$this->token,$this->username)){
                $data = $this->proxyList();
            } else {
                $data = [
	    			'status' => 'error',
					'code' => 'RS401',
        	    	'message' => CustomHandlers::getreSlimMessage('RS401',$this->lang)
				];
            }
			return JSON::encode($data,true);
			$this->db = null;
        }

        public function showListPublic(){
			return JSON::encode($this->proxyList(),true);
        }

        public function rotateProxy(){
            if (Auth::validToken($this->db,$this->token,$this->username)){
                $data = $this->getProxy();
            } else {
                $data = [
	    			'status' => 'error',
					'code' => 'RS401',
        	    	'message' => CustomHandlers::getreSlimMessage('RS401',$this->lang)
				];
            }
			return JSON::encode($data,true);
			$this->db = null;
        }

        public function rotateProxyPublic(){
			return JSON::encode($this->getProxy(),true);
        }
    }