<?php

if ( !class_exists( 'Intrinio_API' ) ) {
    class Intrinio_API
    {
    	static $auth_user;
    	static $auth_pass;
    	static $url;

    	public function __construct($config = array()) {
    		self::$url = "https://api.intrinio.com/";
		    self::$auth_user = $config['user'];
		    self::$auth_pass = $config['password'];
		}

        public static function call($url) {
        	
        	$auth_header = "Basic " . base64_encode(self::$auth_user . ":" . self::$auth_pass);

        	$req = curl_init(); 
	        curl_setopt($req, CURLOPT_URL, self::$url . $url); 
	        curl_setopt($req, CURLOPT_HTTPHEADER,array('Authorization: ' . $auth_header)); 
	        curl_setopt($req, CURLOPT_RETURNTRANSFER,true); 
	        curl_setopt($req, CURLOPT_FOLLOWLOCATION, true);

	        curl_setopt($req, CURLOPT_SSL_VERIFYPEER, false);
	        /*
	        curl_setopt($req, CURLOPT_SSL_VERIFYPEER, true);
			curl_setopt($req, CURLOPT_SSL_VERIFYHOST, 2);
			curl_setopt($req, CURLOPT_CAINFO, plugin_dir_path( __FILE__ ) . "intrinio.cer");
			*/

			$output = curl_exec($req);
			// print_r(curl_error($req));
			curl_close($req);
			return json_decode($output, true);
        	
		} 
    }
}
