FROM infracamp/kickstart-flavor-gaia:testing

ENV DEV_CONTAINER_NAME="rudl-principal"
ENV CONF_CLUSTER_NAME="rudl1"
ENV CONF_CLUSTER_DOMAIN="rudl1.infracamp.org"

ADD / /opt
RUN ["bash", "-c",  "chown -R user /opt"]
RUN ["/kickstart/flavorkit/scripts/start.sh", "build"]

ENTRYPOINT ["/kickstart/flavorkit/scripts/start.sh", "standalone"]
