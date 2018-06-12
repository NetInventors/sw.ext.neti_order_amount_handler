#!/bin/bash
SCRIPT_VERSION='2.0.1'
WORKING_DIR=$(dirname $0)
PLUGIN_NAME=$(basename ${WORKING_DIR})

echo "Start build script version ${SCRIPT_VERSION}"

if [ ! -z "$1" ]; then
    PLUGIN_NAME=$1
fi

if [ "." = "$PLUGIN_NAME" ]; then
    echo 'ERROR: Unable to resolve plugin name'
    exit 2
fi

VERSION=$(php -r "echo preg_replace('/.*<version>([^<]+)<\/version>.*/ims', '\\1', file_get_contents('${WORKING_DIR}/plugin.xml'), 1);")
TEMP_DIR='/tmp/ShopwarePlugins/'${PLUGIN_NAME}
CURRENT_DIR=$(pwd)
EXCLUDES="sftp-config.json nbproject .idea"

# Remove existing package file
if [ -f "${CURRENT_DIR}/${PLUGIN_NAME}-${VERSION}.zip" ]; then
    rm "${CURRENT_DIR}/${PLUGIN_NAME}-${VERSION}.zip"
fi

# Create temporary build folder
mkdir -p ${TEMP_DIR}

# Copy all files from source folder to temporary folder
cp -Rp ${WORKING_DIR}/* ${TEMP_DIR}

# Remove build.sh from temporary folder
rm "${TEMP_DIR}/build.sh"

# Remove excludes from temporary folder
for i in ${EXCLUDES}; do
    if [ -e "${TEMP_DIR}/$i" ]; then
        rm -R "${TEMP_DIR}/$i"
    fi
done

# Step into the temporary build folder
cd ${TEMP_DIR}

# Replace __SECRET__ in plugin bootstrap to detect manipulations
SECRET=$(head -c 1000 /dev/urandom | tr -dc 'A-Za-z0-9%,!_;:#@' | fold -w 32 | head -n 1)
sed -i "s/__SECRET__/${SECRET}/g" "${PLUGIN_NAME}.php"

# Create md5.json file including the md5 checksum of every file in the package and the plugin __SECRET__
echo "<?php return [" > md5checksum.php
for i in $(find -path ./.git -prune -o -name build.sh -prune -o -type f -printf '%P\n'); do
    echo "'$i' => '$(md5sum ${i} | awk '{ print $1 }')'," >> md5checksum.php
done;
echo "'__SECRET__' => '${SECRET}'," >> md5checksum.php
echo "];" >> md5checksum.php

# Move one level up in folder structure
cd ..

# Zip temporary folder contents to package file in plugin folder
zip -qr "${CURRENT_DIR}/${PLUGIN_NAME}-${VERSION}.zip" $(basename ${TEMP_DIR})

# Step back into the current plugin folder
cd ${CURRENT_DIR}

echo "Package wurde erstellt unter ${CURRENT_DIR}/${PLUGIN_NAME}-${VERSION}.zip"

# Remove temporary build folder
rm -R $(dirname ${TEMP_DIR})
