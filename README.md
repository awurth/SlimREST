# Slim REST base - A Slim 3 skeleton
This is an app skeleton for the Slim PHP Micro-Framework to get started quickly building a REST API

## Features
- [Eloquent ORM](https://github.com/illuminate/database)
- Authentication ([Sentinel](https://github.com/cartalyst/sentinel))
- Validation ([Respect](https://github.com/Respect/Validation) + [Slim Validation](https://github.com/awurth/slim-validation))
- Logs ([Monolog](https://github.com/Seldaek/monolog))
- Console commands for updating the database schema and creating users
- A RESTful router

## Installation
``` bash
$ composer create-project awurth/slim-rest-base [app-name]
```

## Features
### Create database tables
``` bash
$ php bin/console db
```

### Create users
``` bash
$ php bin/console user:create
```
Use `--admin` option to set the user as admin

### Dump routes
Execute the following command at the project root to print all routes in your terminal
``` bash
$ php bin/console routes
```

Use --markdown or -m option to display routes in markdown format
``` bash
$ php bin/console routes -m > API.md
```

If you're using [Oh My Zsh](https://github.com/robbyrussell/oh-my-zsh), you can install the symfony2 plugin, which provides an alias and autocompletion:
``` bash
# Without Symfony2 plugin
$ php bin/console db

# With Symfony2 plugin
$ sf db
```

## Note
You might want to replace the authentication part with a real OAuth implementation
