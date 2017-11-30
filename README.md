# PlexLibraryManager
A web-based library manager for Plex Media Server, that helps organizing your media into library for plex to easily recognize it. 
It uses symlinks, so actually you don't have to rename and move your media files (which is especially useful, when you download them using torrents and whant to continue sharing them with others). Technically, it creates new symlink-based structure, that satisfies <a href="https://support.plex.tv/hc/en-us/sections/200059498-Naming-and-Organizing-TV-Shows">Plex naming convention</a>.
When adding anime to the library, PlexLibraryManager allows you to adjust episode and season numbers, select priority external media (subtitles and audio tracks) and also to add/delete external media to each episode manually.

Current version designed specially for anime libraries. Multilanguage support (english and russian included).
Specials not yet supported.

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
---------------------------------------------------------------------------------
Support of other library and media types may be added in future.
Please report bugs to https://github.com/sun2everyone/plexlibrarymanager/issues

