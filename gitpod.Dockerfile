FROM gitpod/workspace-full-vnc

# Instala MySQL
RUN sudo apt-get update && sudo apt-get install -y mysql-server
