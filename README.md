# Slim 3 RESTful application skeleton

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/a0ec0038-d946-4408-8367-3e1c1e26b3e7/mini.png)](https://insight.sensiolabs.com/projects/a0ec0038-d946-4408-8367-3e1c1e26b3e7) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/awurth/slim-rest-base/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/awurth/slim-rest-base/?branch=master)

This is an app skeleton for the Slim PHP Micro-Framework to get started quickly building a REST API

## Features
- [Eloquent ORM](https://github.com/illuminate/database)
- Authentication ([Sentinel](https://github.com/cartalyst/sentinel) + [OAuth 2](https://github.com/bshaffer/oauth2-server-php))
- Validation ([Respect](https://github.com/Respect/Validation) + [Slim Validation](https://github.com/awurth/slim-validation))
- Logs ([Monolog](https://github.com/Seldaek/monolog))
- Dotenv configuration
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

# TODO
- PHPUnit
