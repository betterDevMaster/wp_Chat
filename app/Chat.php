<?php
namespace App;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use App\ChatController;

use StdClass;

class Chat implements MessageComponentInterface {
    protected $clients;
    private $userList = [];
    private $unique = 0;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        // Store the new connection to send messages to later
        $this->clients->attach($conn);
      
        echo "New connection! ({$conn->resourceId})\n";

    }

    public function onMessage(ConnectionInterface $from, $msg) {
    	
	$numRecv = count($this->clients) - 1;
	echo sprintf('Connection %d sending message "%s" to %d other connection%s' . "\n"
	, $from->resourceId, $msg, $numRecv, $numRecv == 1 ? '' : 's');

	$msg = json_decode($msg);

	if($msg->command === 'initUser'){
		$credential = new StdClass();
		if(!empty($msg->value)){
			var_dump($msg->value);

			$credential->name =  htmlspecialchars($msg->value);
			$credential->id = $from->resourceId;
			echo $credential->name." -> join chat";
			foreach($this->userList as $user){
				if($credential->name == $user->name){
					
					
						$from->send(json_encode(array('event'=>'uniqueid')));
						
					
					
				}
			}

			array_push($this->userList, $credential);
		}
		foreach ($this->clients as $client) {

			// The sender is not the receiver, send to each client connected
			$client->send(json_encode(array('event'=>'initUser', 'value'=>$this->userList)));
			$client->send(json_encode(array('event'=>'getUsers', 'value'=>$this->userList)));
		}
	}
	if($msg->command === 'message'){
		$user= [];
		$user['name'] = $msg->userName;
		$user['message'] = $msg->value;

		$userJson = json_encode($user);
		$file = __DIR__.'/history.json';
		$history = file_get_contents($file);
		if (!empty($history)){
			$arr = array('name' =>  $msg->userName, 'message' => htmlspecialchars($msg->value) );
			$json = json_decode($history, true);
			array_push($json, $arr);
			$json = json_encode($json);
			$historyMsg = fopen($file,'w+');
			fputs($historyMsg, $json);
			fclose($historyMsg);
		}
		else{
			$historyMsg = fopen($file,'a');
			$arr = array($arr = $user);
			$json = json_encode($arr);
			fputs($historyMsg,$json);
			fclose($historyMsg);
		
		}
		foreach ($this->clients as $client) {

			$client->send(json_encode(array('event'=>'message', 'value'=> array('name' =>$msg->userName , 'message'=>$msg->value ))));
		}
	}
	if($msg->command === 'retreiveMsg'){
		$file = __DIR__.'/history.json';
		$msgHistory = file_get_contents($file);
		
		foreach ($this->clients as $client) {
			// The sender is not the receiver, send to each client connected
			if($client == $from){
				$client->send(json_encode(array('event' =>'retreiveMsg' , 'value'=>$msgHistory)));
			}
		}
	}
	if($msg->command === 'disconectUser'){
		
		$this->clients->detach($from);

		for($i=0; $i<count($this->userList);$i++){
			if($this->userList[$i]->id == $from->resourceId){
				array_splice($this->userList, $i,1);

			}
		}
		foreach ($this->clients as $client) {

			// The sender is not the receiver, send to each client connected
			$client->send(json_encode(array('event'=>'disconectUser', 'value'=>$this->userList)));
			$client->send(json_encode(array('event'=>'getUsers', 'value'=>$this->userList)));
		}	
	}
	if($msg->command === 'getUsers'){
		foreach ($this->clients as $client) {

			// The sender is not the receiver, send to each client connected
			$client->send(json_encode(array('event'=>'getUsers', 'value'=>$this->userList)));
		}
	}
	if($msg->command === 'banUser'){
		$badGuy = null; // guy will be kick
		for($i=0; $i<count($this->userList);$i++){
			if($this->userList[$i]->id == $msg->value){
				$badGuy = $this->userList[$i]->name;
				array_splice($this->userList, $i,1);

			}
		}
		foreach ($this->clients as $client) {

		
			if($client->resourceId === $msg->value){

				$this->clients->detach($client);
				$client->send(json_encode(array('event'=>'getRect', 'rectMsg'=>'you was kick from the chat <(^^<', 'value'=>$this->userList)));
			}
			$client->send(json_encode(array('event'=>'initUser', 'rectMsg'=>$badGuy.' was kick from the chat <(^^<', 'value'=>$this->userList)));
			$client->send(json_encode(array('event'=>'getUsers', 'value'=>$this->userList)));
		}
	}
    }
    public function onClose(ConnectionInterface $conn) {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);

        for($i=0; $i<count($this->userList);$i++){
        	if($this->userList[$i]->id == $conn->resourceId){
        		array_splice($this->userList, $i,1);
        		
        	}
        }


        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
}
