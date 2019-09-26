# Setup


## Setup a new cluster

```bash
apt-get update && apt-get install -y docker.io curl git ccrypt pwgen
docker swarm init

pwgen 64 -s -1 | docker secret create rudl_cf_secret -
```

### Create secrets and ssh key (One time setup)

```bash
passwd=$(pwgen 512 -s -1)
echo $passwd | ccrypt | base64 -w 72 > rudl_principal_secret.enc
ssh-keygen -t ed25519 -N "$passwd" -f rudl_principal_ssh_key
```

This will create a protected `rudl_principal_secret.enc` - file and a protected ssh_key.

> You should commit these files with your repository!

### Recover your cluster

```
cat rudl_principal_secret.enc | base64 -d | ccrypt -d | docker secret create rudl_principal_secret -
cat rudl_principal_ssh_key | docker secret create rudl_principal_ssh_key -
```


##



```
openssl genpkey -algorithm EC -pkeyopt ec_paramgen_curve:X25519 -pkeyopt ec_param_enc:named_curve
```


## Recovering a cluster

```
cat cluster-secret.enc | base64 -d | ccrypt -d | docker secret create rudl_principal_secret -
```