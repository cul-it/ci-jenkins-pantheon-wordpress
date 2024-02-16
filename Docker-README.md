# Docker-README.md

## How to run ci-jenkins-pantheon-wordpress in a container.

- be sure Docker Desktop is running
- > cd ci-jenkins-pantheon-wordpress
- > git checkout <branch>
- > docker-compose up
- go to the site in a browser at http://0.0.0.0:8000/
- stop the container
- > ctrl-c
- list running containers
- > docker ps
- remove container
- > docker-compose down -v

## Debugging
- log output goes to web/wp-content/debug.log
- this version uses php 8.2
