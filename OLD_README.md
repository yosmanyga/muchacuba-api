# Instalación

## Requerimientos

Instalar [Docker](https://github.com/cubalider/workstation/blob/master/docker.md)

En Windows, abrir "Docker Quickstart Terminal"

Importar todas las imágenes

  - docker load < catatnight_postfix.tar
  - docker load < cubalider_docker_nginx.tar
  - docker load < cubalider_docker_php.tar
  - docker load < debian.tar
  - docker load < mongo.tar
  - docker load < node.tar

## Proyecto

*En Windows, el proyecto hay que clonarlo en la carpeta de usuario*

En Windows, abrir `Docker Quickstart Terminal`

Preparar carpetas de trabajo: `mkdir Work Work/Projects && cd Work/Projects`

Hacer fork al proyecto y clonar: `git clone git@github.com:mi_usuario/muchacuba.git`

`cd muchacuba`

# Tests

`cd domain`

`../vendor/behat/behat/bin/behat`