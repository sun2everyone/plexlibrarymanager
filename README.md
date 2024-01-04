# PlexLibraryManager
A web-based library manager for Plex Media Server, that helps organizing your media into library for plex to easily recognize it. 
It uses symlinks, so actually you don't have to rename and move your media files (which is especially useful, when you download them using torrents and whant to continue sharing them with others). Technically, it creates new symlink-based structure, that satisfies <a href="https://support.plex.tv/hc/en-us/sections/200059498-Naming-and-Organizing-TV-Shows">Plex naming convention</a>.
When adding anime to the library, PlexLibraryManager allows you to adjust episode and season numbers, select priority external media (subtitles and audio tracks) and also to add/delete external media to each episode manually.

Designed specially for anime libraries. Multilanguage support (english and russian included).
Specials supported. 'Movie' and 'Show' library types supported. 

### Disclaimer:

This software is distributed "as is", and author is not responsible for any problems you might get using it (including data loss or corruption)!

### Requirements:
```
Webserver with php5.6+
OS and filesystem that supports symbolic (soft, like "ln -s ..") links
Modern web browser with Javascript and CSS (Currently tested in Firefox 56.0+ and Chrome 49.0)
```
### Installation:

0. <i>For anime library it is recommend to install <a href="https://github.com/ZeroQI/Absolute-Series-Scanner">Absolute Series Scanner</a> and <a href="https://github.com/ZeroQI/Hama.bundle">HTTP Anidb Metadata Agent</a> first.</i>
1. Pull master branch from this repo or download <a href="https://github.com/sun2everyone/plexlibrarymanager/archive/master.zip">master.zip</a> and extract under your webserver directory.
2. Create symlink under webserver root to the directory where your downloaded media files are stored
3. Create folder under webserver root where your new Plex library will be
4. Copy <i>config_base.php</i> to <i>config.php</i> and adjust settings (be careful)
5. Make sure user <i>plex</i> can read new library directory
6. Configure your webserver for you to have access to this software
7. Add library to the Plex via it's web interface
8. Enjoy


Sample:
```
  cd /var/www
  git clone https://github.com/sun2everyone/plexlibrarymanager.git
  mkdir -p plex/media                         #root media folder
  cd plex
  ln -s /mnt/media media 
  mkdir -p libraries/Anime                    #library folder
  cd /var/www/plexlibrarymanager
  cp config_base.php config.php
  nano config.php
  chown -R www-data:www-data /var/www/plex
  usermod -aG www-data plex
```
### Docker run example
1. Adjust values in `docker-compose.yml` and `Dockerfile`
2. `docker build -t plexlibrarymanager:latest .`
3. `docker-compose up -d`

---------------------------------------------------------------------------------
Support of other media types may be added in future.
Please report bugs to https://github.com/sun2everyone/plexlibrarymanager/issues

# PlexLibraryManager
Веб-менеджер библиотеки для Plex Media Server, с помощью которого можно организовать библиотеку для плекса, которая будет отвечать принципам наименования <a href="https://support.plex.tv/hc/en-us/sections/200059498-Naming-and-Organizing-TV-Shows">Plex naming convention</a>. Использует символические ссылки, благодаря чему не приходится переименовывать файлы вручную или копировать их, что очень удобно если они были загружены с торрентов и хочется их раздавать. При добавлении нового тайтла в библиотеку можно отредактировать номера эпизодов, задать приоритет внешним субтитрам и озвучке а так же вручуную добавить или удалить их для каждого эпизода.

Специально разработан для аниме-библиотеки. Поддержка русского и английского языков интерфейса.
Спешлы поддерживаются. Поддерживаются типы библиотек 'Фильмы' и 'Сериалы'.

### Дисклеймер:

Программа распространяется "как есть", и автор не несет ответственности за возможные последствия ее использования (включая потерю и повреждение данных)!

### Технические требования:
```
Веб-сервер с php5.6+
Операционная и файловая системы с поддержкой символических ссылок (типа "ln -s ..")
Современный браузер с Javascript и CSS (Протестировано в Firefox 56.0+ и Chrome 49.0)
```
### Установка:

0. <i>Для аниме-библиотек сначала рекомендуется установить <a href="https://github.com/ZeroQI/Absolute-Series-Scanner">Absolute Series Scanner</a> и <a href="https://github.com/ZeroQI/Hama.bundle">HTTP Anidb Metadata Agent</a></i>
1. Клоинровать master branch этого репозитория или скачать <a href="https://github.com/sun2everyone/plexlibrarymanager/archive/master.zip">master.zip</a> и извлечь в директорию веб-сервера.
2. Создать симлинк в директории вебсервера на папку, где хранятся ваши медиа-файлы (например аниме-раздачи)
3. Создать в директории вебсервера папку для новой Plex-библиотеки
4. Копировать <i>config_base.php</i> в <i>config.php</i> и произвести в последнем необходимые настройки
5. Убедиться, что пользователь <i>plex</i> имеет доступ на чтение к новой библиотеке
6. Сконфигурировать веб-сервер для доступа к PLM
7. Добавить новую библиотеку в Plex с помощью его веб-интерфейса
8. Пользоваться


Пример:
```
  cd /var/www
  git clone https://github.com/sun2everyone/plexlibrarymanager.git
  mkdir -p plex/media                         #root media folder
  cd plex
  ln -s /mnt/media media 
  mkdir -p libraries/Anime                    #library folder
  cd /var/www/plexlibrarymanager
  cp config_base.php config.php
  nano config.php
  chown -R www-data:www-data /var/www/plex
  usermod -aG www-data plex
```
### пример запуска с помощью Docker
1. Подправьте значения в `docker-compose.yml` и `Dockerfile`
2. `docker build -t plexlibrarymanager:latest .`
3. `docker-compose up -d`
---------------------------------------------------------------------------------
Поддержка других типов медиа-файлов может быть добавлена в будущем.
Пожалуйста, сообщайте о багах сюда https://github.com/sun2everyone/plexlibrarymanager/issues

