(function (ctx, $){
	var app = {
		conn : new WebSocket('ws://localhost:8080'),
		userList : [],
		base_uri : null,
		init : function(){
			if( sessionStorage.getItem('BASE_URI') !== undefined && sessionStorage.getItem('BASE_URI') !== null  && sessionStorage.getItem('BASE_URI') !== 'undefined' ){
				this.base_uri = sessionStorage.getItem('BASE_URI');

			
			}
			else{
				var $url = $('.wc_seturi').attr('data-uri');
				console.log($url)
				sessionStorage.setItem('BASE_URI',$url);
			}
			
			self.conn.onmessage = function(e) {
				var event =  JSON.parse(e.data);
				if(event.event === 'initUser'){
					var userList = event.value;
					var $user = $('.wc_userList').find('ul');
					var html ="";

					$user.append(html);
					for(var user in userList){
						html+= "<li>"+userList[user].name+"</li>";
						
					}
					if(event.rectMsg !== undefined){
						 $('.wc_chatView').append("<p>"+event.rectMsg+"</p>");
					}
					$user.find('li').remove();
					$user.append(html);
				}

				if(event.event === 'message'){
					var msg = event.value;
					var $chatScope = $('.wc_chatView');
					var html ="";
					
					if(msg.name === sessionStorage.getItem('userName')){
							
						html+="<p class='wc_bubleRight'>"+ msg.name+' say : '+msg.message+"</p>";
					}
					else{
						html+="<p class='wc_bubleLeft'>"+ msg.name+' say : '+msg.message+"</p>";
					}
					
					$chatScope.append(html);
					$chatScope.scrollTop($chatScope[0].scrollHeight);
				}
				if(event.event === 'retreiveMsg'){
					if(event.value === ''){
						return;
					}
				
					var msgHistory= JSON.parse(event.value);
					var html ="";

					for(var msg in msgHistory){
						if(msgHistory[msg].name === sessionStorage.getItem('userName')){
							
							html+="<p class='wc_bubleRight'>"+ msgHistory[msg].name+' say : '+msgHistory[msg].message+"</p>";
						}
						else{
							html+="<p class='wc_bubleLeft'>"+ msgHistory[msg].name+' say : '+msgHistory[msg].message+"</p>";
						}
						
					}
					var $chatScope = $('.wc_chatView');
					$chatScope.find('p').remove();
					$chatScope.append(html);
					if($chatScope[0]!== undefined){

						$chatScope.scrollTop($chatScope[0].scrollHeight);
					}
				}
				if(event.event === 'disconectUser'){
					
					
					var newList= event.value;
					var $user = $('.wc_userList').find('ul');
					var html ="";

					$user.append(html);
					for(var user in newList){
						html+= "<li>"+newList[user].name+"</li>";
						
					}
					$user.find('li').remove();
					$user.append(html);

				}
				if(event.event === 'getRect'){
					sessionStorage.clear();
					alert(event.rectMsg);
					window.location.href = "/index.php/wc_unlog";
				}
				if(event.event === 'uniqueid'){
					alert('name already taken change name plz')
					self.conn.send(JSON.stringify({command:'disconectUser'}));
					sessionStorage.clear();
					window.location.href = "/index.php/wc_unlog";
					

				}
			};
			this.initChat();
			this.disconectUser();
			this.retreiveMessage();
			this.sendMessage();
		},
		retreiveMessage : function(){
		
			return JSON.stringify({command:'retreiveMsg'});

		
		},
		initUserList : function(){
			var $user = $('.wc_userList').attr('data-curent-user');
			if($user !== undefined){

				sessionStorage.setItem('userName', $user);
				return JSON.stringify({command:'initUser', value: $user})
			}
			
			
		},
		initChat : function(){
			
			var $user = $('.wc_userList').attr('data-curent-user');
			self.conn.onopen = function(e) {
				// userList.push();
				if($user !== undefined){
					self.conn.send(self.initUserList());
				}
				self.conn.send(self.retreiveMessage());
				console.log(e)
				console.log("Connection established!");
			};

		},
		sendMessage : function(){
			var $btn = $('.wc_send');
			var $input = $('.wc_message');
			

			$btn.on('click', function(){
				var userName = sessionStorage.getItem('userName');
				var $message = self.escapeHtml($('.wc_message').val());
				self.conn.send(JSON.stringify({command:'message', userName: userName, value: $message}));
				$input.val('');
				
			});
			$input.on('keydown',function(evt){
				if(evt.keyCode === 13){
					var userName = sessionStorage.getItem('userName');
					var $message = self.escapeHtml($('.wc_message').val());
					self.conn.send(JSON.stringify({command:'message', userName: userName, value: $message}));
					$input.val('');
				}
			});

			
		},
		disconectUser : function(){
			var $chatBar = $('.wc_chatBar');
			$chatBar.prepend('<button class=\'wc_disconect-btn\'>X</button>');

			$('.wc_disconect-btn').on('click', function(){
				self.conn.send(JSON.stringify({command:'disconectUser'}));
				sessionStorage.clear();
				window.location.href = self.base_uri+"/index.php/wc_unlog";
			});
		},
		escapeHtml: function(text){
			var map = {
			'&': '&amp;',
			'<': '&lt;',
			'>': '&gt;',
			'"': '&quot;',
			"'": '&#039;'
			}

			return text.replace(/[&<>"']/g, function(m) { return map[m]; });
		},
	}
	ctx.app = app;
	var self = app;
})(window, jQuery)
