(function (ctx, $){
	var app = {
		conn : new WebSocket('ws://localhost:8080'),
		userList : [],
		init : function(){
			self.conn.onopen = function(e) {
			
				self.conn.send(self.initUserList());
				
				self.conn.send(self.retreiveMessage());
				console.log(e)
				console.log("Connection established!");
				
			};
			self.conn.onmessage = function(e) {
				
				var event =  JSON.parse(e.data);
				if(event.event === 'getUsers'){
					var userList = event.value;
					var $user = $('.wcUserList').find('ul');
					var html ="";

					$user.append(html);
					for(var user in userList){
						html+= "<li >"+userList[user].name+"<button data-id='"+userList[user].id+"' class='wcUserCo'>X</button></li>";
						
					}
					$user.find('li').remove();
					$user.append(html);
					self.banUser();
				}
				if(event.event === 'retreiveMsg'){
					var $history = $('.wcHistory');
					var html ="";
					var history = JSON.parse(event.value);

					if(event.value !== undefined){
						for(var msg in history){
							html += '<p>'+history[msg].name+' say : '+history[msg].message+'</p>';
						}
						
					}
					$history.find('p').remove();
					$history.append(html);
					$history.scrollTop($history[0].scrollHeight);
				}
				if(event.event === 'message'){
					var msg = event.value;
					var $history = $('.wcHistory');
					var html ="<p>"+event.value.name+" say : "+event.value.message+"</p>";
					$history.append(html);
					$history.scrollTop($history[0].scrollHeight);
				}
				
			};
			
		
			this.retreiveMessage();
			this.sendMessage();
		},
		retreiveMessage : function(){
		
			return JSON.stringify({command:'retreiveMsg'});

		
		},
		initUserList : function(){
			
			return JSON.stringify({command:'getUsers'})
			
			
			
		},
		banUser : function(){

			var $user = $('.wcUserCo');
			$user.on('click',function(){
				var $id= Number($(this).attr('data-id'));
				self.conn.send(JSON.stringify({command:'banUser', value: $id}));
				
				

			})
		},
		sendMessage : function(){
			var $btn = $('.wc_send');
			var $input =$('.wc_message');
			

			$btn.on('click', function(){
				var userName = 'Big Dady (Admin)';
				var $message = self.escapeHtml($('.wc_message').val());

				self.conn.send(JSON.stringify({command:'message', userName: userName, value: $message}));
				$input.val('');
				
			});
			$input.on('keydown',function(evt){
				if(evt.keyCode === 13){
					var userName = 'Big Dady (Admin)';
					var $message = self.escapeHtml($('.wc_message').val());

					self.conn.send(JSON.stringify({command:'message', userName: userName, value: $message}));
					$input.val('');
				}
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
