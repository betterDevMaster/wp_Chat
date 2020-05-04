<?php

/**
* 
*/
namespace App\Controller;


class ChatController
{
	
	public function init(){
		if(!empty($_SESSION['chat'])){
			
			include_once plugin_dir_path(__FILE__)."/../../views/chat.php";
		}
		else{

			include_once plugin_dir_path(__FILE__)."/../../views/login.php";
		}
	}
	public function login(){
		$name = strip_tags($_POST['login']);
		$_SESSION['chat']['name'] = $name;
		if(BASE_URI == ''){
			wp_redirect('/');
		}
		else{
			wp_redirect(BASE_URI);
		}
		
		exit();
	}
	public function disconect(){
		var_dump('ici');
		unset($_SESSION['chat']['name']);
		if(BASE_URI == ''){
			wp_redirect('/');
		}else{

			wp_redirect(BASE_URI);
		}
		exit();
	}


}