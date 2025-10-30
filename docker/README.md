## Docker environment

Once you're done, simply `cd` to your project and run `docker-compose up -d`.

To open ssh session use `docker exec -it BK-php-fpm bash` where **`BK-php-fpm`** is the container name.

This will initialise and start all the containers, then leave them running in the background.

## Services exposed outside your environment

You can access your application via **`localhost`**, if you're running the containers directly, or through **``** when
run on a vm. nginx respond to any hostname, in case you want to add your own hostname on
your `/etc/hosts`

| Service             | Hostname                      | Port number |
| ------------------- | ----------------------------- | ----------- |
| Webserver (Nginx)   | [localhost](http://localhost) | 80,443      |
| Database (Postgres) | [localhost](http://localhost) | 5432        |

# Docker compose cheatsheet

**Note:** you need to cd first to where your docker-compose.yml file lives.

- Start containers in the background: `docker-compose up -d`
- Start containers on the foreground: `docker-compose up`. You will see a stream of logs for every container running.
- Stop containers: `docker-compose stop`
- Kill containers: `docker-compose kill`
- View container logs: `docker-compose logs`
- Execute command inside of container: `docker-compose exec SERVICE_NAME COMMAND` where `COMMAND` is whatever you want
  to run. Examples:
    - Shell into the PHP container, `docker-compose exec php-fpm bash`
    - Run symfony console, `docker-compose exec php-fpm bin/console`
    - Open a mysql shell, `docker-compose exec mysql mysql -uroot -pCHOSEN_ROOT_PASSWORD`

# Recommendations

It's hard to avoid file permission issues when fiddling about with containers due to the fact that, from your OS point
of view, any files created within the container are owned by the process that runs the docker engine (this is usually
root). Different OS will also have different problems, for instance you can run stuff in containers
using `docker exec -it -u $(id -u):$(id -g) CONTAINER_NAME COMMAND` to force your current user ID into the process, but
this will only work if your host OS is Linux, not mac. Follow a couple of simple rules and save yourself a world of
hurt.

- Run composer outside of the php container, as doing so would install all your dependencies owned by `root` within your
  vendor folder.
- Run commands (Laravel's artisan or npm) straight inside of your container. You can easily open a shell as described
  above and do your thing from there.

`docker exec -it Crypto-Oracle-php-fpm bash`
