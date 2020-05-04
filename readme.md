# wpChat
plugin for wordpress from scratch with mvc work on linux (no admin for window)

**How to Install**
==================

- download zip file from github you can use 

> git clone https://github.com/InitialCrow/wpChat.git .

- rename folder to wpChat if name changed
- then put wpChat folder in you wordpress plugin directory

- we need to install dependencies so with composer (go check how to install composer if you dont have it) use in wpChat directory

> composer install

- after we need to give good right acces on folder use

> sudo chmod 777 wpChat;
> sudo chmod 777 wpChat/app/history.json

- if you server has sublfolder for acces wordpress app go to index.php in plugin and find the line 

> define('BASE_URI', ''); // set here the subfolder path after host

e.g if your site base url is http://localhost/wordpress

put 

> define('BASE_URI', '/wordpress'); // set here the subfolder path after host

- then go to activate this plugin on wp admin

**How to Use**
==================

- go to admin wpChat menu and start the server for client can use the chat

- admin can write global msg or kik user

if you want start server manualy (for window exemply) you can use in wpChat folder

> php server.php
