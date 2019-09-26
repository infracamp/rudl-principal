#!/bin/bash

set -x -e

mkdir -p /mnt/.ssh
mkdir -p /mnt/log

CONF_PRINCIPAL_SECRET_FILE=/run/secrets/rudl_principal_secret
CONF_PRINCIPAL_SSH_KEY_FILE=/run/secrets/rudl_principal_ssh_key
CONF_SSH_KEY=/mnt/.ssh/id_ed25519


if [[ ! -f $CONF_PRINCIPAL_SECRET_FILE ]]
then
    echo "Error: rudl_principal_secret missing in $CONF_PRINCIPAL_SECRET_FILE"
    echo "Can't unlock cluster"
    echo "Make sure you created and added the secret by running 'docker secret create rudl_principal_secret <secretfile>'"

    exit 2
fi

if [[ -f $CONF_PRINCIPAL_SSH_KEY_FILE ]]
then
    echo "Existing $CONF_PRINCIPAL_SSH_KEY_FILE -> Unlocking key"
    cp $CONF_PRINCIPAL_SSH_KEY_FILE $CONF_SSH_KEY
    chmod 700 $CONF_SSH_KEY
    echo "Unlocking key using principal_secret..."
    ssh-keygen -p -P "$(cat $CONF_PRINCIPAL_SECRET_FILE)" -N "" -f $CONF_SSH_KEY

    echo "Overwriting exising ssh key with key..."
    ssh-keygen -y -f $CONF_SSH_KEY > "$CONF_SSH_KEY.pub"

else
    echo "No $CONF_PRINCIPAL_SSH_KEY_FILE found."
fi;


if [[ ! -f $CONF_SSH_KEY ]]
then
    echo "Creating new ssh key pair in $CONF_SSH_KEY..."
    ssh-keygen -N "" -t ed25519 -f $CONF_SSH_KEY

fi

chown -R www-data /mnt
chmod 777 /mnt


echo "Your ssh public key is";
cat /mnt/.ssh/id_ed25519.pub

php -f /opt/bin/pull-initial-on-startup.php
