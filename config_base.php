<?php

//ВНИМАНИЕ: будьте аккуратны с этими параметрами, возможна потеря данных при неправильном указании. Программа поставляется "как есть" и ее разработчик не несет никакой ответственности. 
//CAUTION: be careful with this settings, data loss may occur. This software is distributed "as is", and author is not responsible for any problems you might get using it (including data loss or corruption)!

//Rename this file to config.php after adjusting your settings.
//Переименуйте этот файл в config.php после внесения настроек.

//Make sure you understand what the following options mean before continuing.
//Прежде чем продолжить, убедитесь что понимаете значение всех параметров.

//Address of your PlexLibraryManager with trailing slash
//Адрес вашего PlexLibraryManager со слешем на конце
const HOSTNAME = "https://yourdomain.com/plexlibrarymanager/"; 

//Absolute path (related to webserver root), at witch you store your downloaded media, it may be symlink to your torrent folder, for example.
//Strongly recommended to make a symlink folder for this under your webserver root
//Абсолютный (относительно корня вебсервера) путь до папки, куда вы качаете тайтлы, например папки с торрентами. 
//Рекомендуется сделать ее симлинком на реальную папку
const SRC_FOLDER = "/var/www/plex/media";               

//Absolute path (related to webserver root), at witch your new plex library will be
//CAUTION: It's strongly unrecommended to use existing library for this, as you will loose your library data!
//Абсолютный путь до папки с Аниме-библиотекой plex относительно корня вебсервера.
//ВНИМАНИЕ: Крайне не рекомендуется использовать действующую библиотеку, т.к. данная программа вероятно не сможет ее правильно обработать и часть данных библиотеки будет потеряна.
const PLEX_LIB = "/var/www/plex/library/Anime";   

//HTTP authentication. You may enable this if you don't have another authentication enabled and when your webserver is available not only from local network
$require_authentication=0; 

//Interface language, "default -"en" for english, "ru" - русский
$lang="en"; 

//Default media language for external subtitle and audio to show in Plex intergace, "en" for english, or you can choose any code from ISO 639-1 https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes
//Какой язык назначать предпочитаемым внешним субтитрам и аудиодорожкам для отображения в интерфейсе Plex, "ru" - русский, или можно выбрать любой подходящий код из ISO 639-1 https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes
$media_lang="en"; 

/////////Service/////
//Maximum depth of relative symlinks (do not adujust without knowing, what you do!)
//Максимальная глубина относительных симлинков (без особой нужды не менять).
const MAXDEPTH = 10; 
       
