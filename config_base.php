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

//Absolute path (related to webserver root), at witch your new plex libraries will be
//CAUTION: It's strongly unrecommended to use existing library for this, as you will loose your library data!
//Абсолютный путь до папок с библиотеками plex относительно корня вебсервера.
//ВНИМАНИЕ: Крайне не рекомендуется использовать действующую библиотеку, т.к. данная программа вероятно не сможет ее правильно обработать и часть данных библиотеки будет потеряна. 
$plex_libs= array(
    array(
        'name' => "Anime",
        'path' => "/var/www/plex/library/Anime",
        'type' => "shows", //default, for multi-episode media like anime, shows etc. //для аниме, сериалов и т.п.
        ),
    array(
        'name' => "Films",
        'path' => "/var/www/plex/library/Films",
        'type' => "movies", //for single-file media, like films //для фильмов
        ),
    );

//Interface language, "default -"en" for english, "ru" - русский
$lang="en"; 

//Default media language for external subtitle and audio to show in Plex intergace, "en" for english, or you can choose any code from ISO 639-1 https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes
//Какой язык назначать предпочитаемым внешним субтитрам и аудиодорожкам для отображения в интерфейсе Plex, "ru" - русский, или можно выбрать любой подходящий код из ISO 639-1 https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes
$media_lang="en"; 

/////////Service/////
//Maximum depth of relative symlinks (do not adujust without knowing, what you do!)
//Максимальная глубина относительных симлинков (без особой нужды не менять).
const MAXDEPTH = 10; 
//Config file version (for compatibility)
const CONF_V = 2;
//Setting UTF-8 locale is needed to avoid problems with UTF-8 filenames (incorrect pathinfo() behaviour). If this locale doesn't  present in your system, generate it using "dpkg-reconfigure locales"
setlocale(LC_ALL,'en_US.UTF-8');
       
