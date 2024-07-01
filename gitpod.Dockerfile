FROM gitpod/workspace-full-vnc

USER root

# Instalar MySQL
RUN apt-get update && \
    apt-get install -y mysql-server

# Configuración de MySQL
RUN sed -i 's/bind-address\s*=\s*127.0.0.1/bind-address = 0.0.0.0/' /etc/mysql/mysql.conf.d/mysqld.cnf

# Crear script para inicialización de MySQL
RUN echo "#!/bin/bash\n\
service mysql start\n\
mysql -e \"CREATE DATABASE IF NOT EXISTS studybuddy_db;\"\n\
mysql -e \"CREATE USER 'vicovt'@'%' IDENTIFIED BY 'vicky14';\"\n\
mysql -e \"GRANT ALL PRIVILEGES ON studybuddy_db.* TO 'vicovt'@'%';\"\n\
mysql -e \"FLUSH PRIVILEGES;\"\n\
tail -f /dev/null" > /usr/local/bin/setup_mysql.sh

RUN chmod +x /usr/local/bin/setup_mysql.sh

# Exponer el puerto MySQL
EXPOSE 3306

USER gitpod

# Instalar extensiones de VSCode
RUN code --install-extension bmewburn.vscode-intelephense-client \
          --install-extension felixfbecker.php-intellisense \
          --install-extension neilbrayfield.php-docblocker \
          --install-extension formulahendry.auto-rename-tag
