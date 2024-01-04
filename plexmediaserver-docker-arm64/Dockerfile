FROM ubuntu:20.04
CMD ["/bin/bash"]
ARG S6_OVERLAY_VERSION=v2.2.0.3
ARG S6_OVERLAY_ARCH=arm
ARG PLEX_BUILD=linux-arm64
ARG PLEX_DISTRO=debian
ARG DEBIAN_FRONTEND=noninteractive
ARG INTEL_NEO_VERSION=20.48.18558
ARG INTEL_IGC_VERSION=1.0.5699
ARG INTEL_GMMLIB_VERSION=20.3.2
ARG URL="https://downloads.plex.tv/plex-media-server-new/1.32.8.7639-fb6452ebf/debian/plexmediaserver_1.32.8.7639-fb6452ebf_arm64.deb"
ENV TERM=xterm LANG=C.UTF-8 LC_ALL=C.UTF-8
ENTRYPOINT ["/init"]
RUN apt-get update  \
	&& apt-get install -y tzdata curl xmlstarlet uuid-runtime unrar  \
	&& curl -J -L -o /tmp/s6-overlay-${S6_OVERLAY_ARCH}.tar.gz https://github.com/just-containers/s6-overlay/releases/download/${S6_OVERLAY_VERSION}/s6-overlay-${S6_OVERLAY_ARCH}.tar.gz  \
	&& tar xzf /tmp/s6-overlay-${S6_OVERLAY_ARCH}.tar.gz -C / --exclude='./bin'  \
	&& tar xzf /tmp/s6-overlay-${S6_OVERLAY_ARCH}.tar.gz -C /usr ./bin  \
	&& useradd -U -d /config -s /bin/false plex  \
	&& usermod -G users plex  \
	&& mkdir -p /config /transcode /data  \
	&& apt-get -y autoremove  \
	&& apt-get -y clean  \
	&& rm -rf /var/lib/apt/lists/*  \
	&& rm -rf /tmp/*  \
	&& rm -rf /var/tmp/*
EXPOSE 1900/udp 32400/tcp 32410/udp 32412/udp 32413/udp 32414/udp 32469/tcp 8324/tcp
ENV CHANGE_CONFIG_DIR_OWNERSHIP=true HOME=/config
COPY cont-init.d/40-plex-first-run cont-init.d/45-plex-hw-transcode-and-connected-tuner cont-init.d/50-plex-update /etc/cont-init.d/
COPY services.d/plex /etc/services.d/plex/
COPY healthcheck.sh plex_service.sh plex-common.sh installBinary.sh version.txt /
RUN /installBinary.sh
