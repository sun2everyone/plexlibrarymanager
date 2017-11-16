<?php

//ВНИМАНИЕ: будьте аккуратны с этими параметрами, возможна потеря данных при неправильном указании. Программа поставляется "как есть" и ее разработчик не несет никакой ответственности. 

$configured = 0; //Измените на 1 после внесения настроек в этот файл. 
//Прежде чем продолжить, убедитесь что понимаете значение всех параметров.

const HOSTNAME = "https://yourdomain.com/plexlibrarymanager/"; //Адрес вашего PlexLibraryManager со слешем на конце
const SRC_FOLDER = "/var/www/plex/media";               //Абсолютный путь до папки относительно корня вебсервера ,куда вы качаете тайтлы, например папки с торрентами. 
//const SRC_FOLDER = "/var/www/plex/media"; //Рекомендуется сделать ее симлинком на реальную папку
const PLEX_LIB = "/var/www/plex/library/Anime";  //Абсолютный путь до папки с Аниме-библиотекой plex относительно корня вебсервера. 
//const PLEX_LIB = "/var/www/plex/library";  //Пример рекомендуемого пути
                                         //ВНИМАНИЕ: крайне не рекомендуется использовать действующую библиотеку, т.к. данная программа вероятно не сможет ее правильно обработать и часть данных библиотеки будет потеряна.

$require_authentication=1; //HTTP authentication. You may disable this if you have another authentication enabled or when your webserver is available only from local network
$lang="en"; //Interface language, default - english

//Служебные
const MAXDEPTH = 10; //Максимальная глубина относительных симлинков (без особой нужды не менять).
       
