# arm64 docker image of Plex Media Server

As there are no official arm64 docker image of plexmediaserver, so you can build you own:

`docker build -t plexmediaserver:latest .`

Current version: 1.32.8.7639-fb6452ebf 

You can search older versions here https://github.com/axlecrusher/plex_download_urls and customize Dockerfile

All contents of this folder taken from official docker image https://hub.docker.com/r/plexinc/pms-docker

Tested on Orange Pi 4B Armbian 23.11.1 bookworm