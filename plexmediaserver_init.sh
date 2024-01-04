#!/bin/bash
PLEX_CONFIG_DIR="/config"
PLEX_USER="www-data"
PLEX_GROUP="plex"

# Absolute Series Scanner
if [ ! -f "${PLEX_CONFIG_DIR}/Library/Application Support/Plex Media Server/Scanners/Series/Absolute Series Scanner.py" ]; then
    echo "Installing Absolute Series Scanner...."
    mkdir -p "${PLEX_CONFIG_DIR}/Library/Application Support/Plex Media Server/Scanners/Series"
    curl -o "${PLEX_CONFIG_DIR}/Library/Application Support/Plex Media Server/Scanners/Series/Absolute Series Scanner.py" https://raw.githubusercontent.com/ZeroQI/Absolute-Series-Scanner/master/Scanners/Series/Absolute%20Series%20Scanner.py
    chown -R $PLEX_USER:$PLEX_GROUP "${PLEX_CONFIG_DIR}/Library/Application Support/Plex Media Server/Scanners"
    chmod -R 775 "${PLEX_CONFIG_DIR}/Library/Application Support/Plex Media Server/Scanners"
fi

# Hama
if [ ! -d "${PLEX_CONFIG_DIR}/Library/Application Support/Plex Media Server/Plug-ins/Hama.bundle" ] ; then
    echo "Installing Hama.bundle"
    apt update && apt install -y unzip git
    git clone https://github.com/ZeroQI/Hama.bundle.git "${PLEX_CONFIG_DIR}/Library/Application Support/Plex Media Server/Plug-ins/Hama.bundle"
    chown -R $PLEX_USER:$PLEX_GROUP "${PLEX_CONFIG_DIR}/Library/Application Support/Plex Media Server/Plug-ins"
    chmod 775 -R "${PLEX_CONFIG_DIR}/Library/Application Support/Plex Media Server/Plug-ins"
    rm -rf /var/cache/apt/*
fi
/init