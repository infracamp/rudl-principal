#!/bin/bash

apt-get update
apt-get install -y docker.io rsync software-properties-common

## Load ACME2 version of certbot
sudo add-apt-repository ppa:certbot/certbot -y
apt-get update
apt-get install -y certbot