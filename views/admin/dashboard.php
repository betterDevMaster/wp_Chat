<!DOCTYPE html>
<html>
<head>
	<title>dashboard</title>
</head>
<body>
	<h1 class="wcTitle">wpChat</h1>
	<p>manage web socket server and chat option</p>
	<div class="wcWebSock">
	<h4>Server: </h4>
	<p>server command : </p>
	<ul>
		<?php if($_SESSION['chatServer']['on'] === true) {?>
			<p class="wcStatus"> server status : <span class="wcPing wcOn"></span> on line! <?php if(!empty($_SESSION['chatServer']['logs'])) echo $_SESSION['chatServer']['logs'];?></p>

			<li><a class="wcOn" href="#">start</a></li>
			<li><a href="<?php echo BASE_URI;?>/wp-admin/index.php/server/restart">restart</a></li>
			<li><a href="<?php echo BASE_URI;?>/wp-admin/index.php/server/stop">stop</a></li>
			
		<?php } else{ ?>
			<p class="wcStatus"> server status : <span class="wcPing wcOff"></span> off line!</p>
		<li><a href="<?php echo BASE_URI;?>/wp-admin/index.php/server/start">start</a></li>
		<?php } ?>
		

	</ul>


	</div>
	<div class="wcUserList">
	<?php if($_SESSION['chatServer']['on'] === true){  ?>
	
		<h4>user are logged : </h4>
		<ul>
			
		</ul>
		<div class="wcHistory">
		
		</div>

		<input type="text" class="wc_message" placeholder="send your alert message"><button class="wc_send">send</button>

		<a href="<?php echo BASE_URI;?>/wp-admin/index.php/clearChat">clear</a>
	<?php }else{?>
	<p>chat server disabled</p>
	<?php	} ?>
	</div>
	
</body>
</html>