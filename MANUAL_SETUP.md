# Setup


## Setup a new cluster

```bash
apt-get update && apt-get install -y docker.io curl git ccrypt pwgen
docker swarm init

pwgen 64 -s -1 | docker secret create rudl_cf_secret -

passwd=$(pwgen 512 -s -1) && echo $passwd | ccrypt | base64 -w 72 > cluster-secret.enc && echo $passwd | docker secret create rudl_principal_secret -


```


## Recovering a cluster

```
cat cluster-secret.enc | base64 -d | ccrypt -d | docker secret create rudl_principal_secret -
```