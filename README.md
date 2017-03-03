# Slim-rest-base - Slim 3 skeleton
This is a skeleton for Slim PHP micro-framework to get started quickly

## Features
- Rest router
- Eloquent ORM
- Authentication (Sentinel)
- Validation (Respect)

## Installation
``` bash
$ composer create-project awurth/slim-rest-base [app-name]
```

## Features
### Create database
``` bash
$ php console db
```

### Create users
``` bash
$ php console user:create
```
Use `--admin` option to set the user as admin

### Dump routes
Execute the following command at the project root to print all routes in your terminal
``` bash
$ php console routes
```

Use --markdown or -m option to display routes in markdown format
``` bash
$ php console routes -m > API.md
```
