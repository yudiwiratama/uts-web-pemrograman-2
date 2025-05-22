# Project Ujian Tengah Semester Web Pemrograman 2

## Dockerize to run web app
### 1. Clone Repository

```bash
root@wiratama-ThinkPad-T490:/home/wiratama/uts-web2# tree
.
├── config.php
├── docker-compose.yml
├── Dockerfile
├── functions.php
├── init_db
│   └── database.sql
├── inventory.php
└── README.md
```

### 2. Run this command to execute yaml file using docker-compos
```docker
docker-compose up -d
```

### 3. We can access localhost:8080/inventory.php

```
root@wiratama-ThinkPad-T490:/home/wiratama/uts-web2# docker-compose ps
      Name                    Command               State                          Ports                       
---------------------------------------------------------------------------------------------------------------
uts-web2_mysql_1   docker-entrypoint.sh mysqld      Up      0.0.0.0:3306->3306/tcp,:::3306->3306/tcp, 33060/tcp
uts-web2_web_1     docker-php-entrypoint apac ...   Up      0.0.0.0:8080->80/tcp,:::8080->80/tcp               
```
