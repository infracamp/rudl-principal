#!/bin/bash

set -x -e

mkdir -p /mnt/.ssh
mkdir -p /mnt/log


if [[ ! -f /mnt/.ssh/id_ed25519.pub ]]
then
    echo "Creating new ssh key pair"
    ssh-keygen -N "" -t ed25519 -f /mnt/.ssh/id_ed25519

fi

chown -R www-data /mnt
chmod 777 /mnt


echo "Your ssh public key is";
cat /mnt/.ssh/id_ed25519.pub

php -f /opt/bin/pull-initial-on-startup.php
