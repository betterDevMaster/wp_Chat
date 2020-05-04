<?php
namespace App;
Class EzRouter {
	private $match = null;
	private $path = null;
	private $host = null;
	private $uri = null;
	public function __construct(){
		$this->uri = $_SERVER["REQUEST_URI"];
		$this->host = $_SERVER["HTTP_HOST"];
		$this->path = $this->host.$this->uri;
	}
	public function route($WtoMatch, $callback = ""){
		$url_var = [];
		$this->match = strip_tags($WtoMatch);
		if(preg_match('/\/:\S+/', $this->match, $match)){
			$uri = explode('/', $this->uri);
			$matches = explode('/', $this->match);	
			$url_param = explode('/:', $match[0]);
			$url_param_value = [];
			$url_param_value_index = 0;
			
			if(count($uri)!== count($matches)){
				return;
			}
			for($j=0; $j<count($uri);$j++){
				if($uri[$j] !== $matches[$j]){
					array_push($url_param_value, $uri[$j]);
				}
			}
			for($i=0; $i<count($url_param); $i++){
				if(!empty($url_param[$i])){
					$url_param[$i] = preg_replace('/\/(.*)/', '', $url_param[$i]);
					
					$url_var[$url_param[$i]]= $url_param_value[$url_param_value_index];
					$url_param_value_index ++;
				}
			}
			for($i=0; $i<count($matches); $i++){
				if(!empty($matches[$i][0]) && $matches[$i][0]==':'){
					if($matches[$i-1] == $uri[$i-1] ){
						$this->match = $this->uri;
						break;
					}
				}
			}
				
		}
		if($this->match === $this->uri){
			if(!empty($url_var)){
				
				call_user_func($callback, $url_var);
				
			}
			else{
				

				call_user_func($callback);
				
			}
			return $this;
		}
		else{
			return;	
		}	
		
	}
	public function end($showMsg = true){
		if($showMsg){

			echo "route not found";
		}
	}
}