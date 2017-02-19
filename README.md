# Slim-rest-base - Slim 3 skeleton
This is a skeleton for Slim PHP micro-framework to get started quickly

## Features
- Rest router
- Eloquent ORM
- Authentication (Sentinel)
- Validation (Respect)

## Installation
### 1. Create project
#### Using composer
``` bash
$ composer create-project awurth/slim-rest-base [app-name]
```

#### Manual install
``` bash
$ git clone https://github.com/awurth/slim-rest-base.git [app-name]
$ cd [app-name]
$ composer install
```

### 2. Create tables
``` bash
$ php bootstrap/database.php
```

## Features
### Dump routes
Execute the following command at the project root to print all routes in your terminal
``` bash
$ php console routes
```

# TODO
- JWT auth
- FoundHandler (inject response in controller ?)
- Route builder (manage resources, not routes, configure them (chained methods), ...)
- RESTful validator
- Database console commands
