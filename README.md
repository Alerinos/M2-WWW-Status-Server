# M2-WWW-Status-Server
System to turn on/off/restart the server together with online status and time   
   
The minimum version of PHP 7.4   
   
This is the free version, I also have a premium with colors, the ability to control each channel separately and browse syserr/syslog.  

![panel](https://github.com/Alerinos/M2-WWW-Status-Server/blob/master/screen.png)
setting
```
$server = new Server('IP','USER','PASS');
$server->dir = 'DIR_FILE';
```
put status.sh in the folder with game files. In the start () and stop () functions you can change the name of your script to start and stop the server.   
The HTML code is just an example of use.
