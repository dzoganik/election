# Election App
This is a sample application using PHP and Symfony.

## Used Technologies
- Symfony 6.0.8
- PHP 8.1.5
- MySQL 8
- Nginx
- Docker
- Adminer

## Features
- Extracting data from external XML files.
- Storing the extracted data to the MySQL database.
- Providing a simple REST API with extracted and transformed data from the external XML.
- Test for the API endpoint.

## Launch
- Clone the repo.
- Install docker and docker compose.
- Run "docker-compose up -d" from the project directory.
- composer install
- GET http://localhost:8080/results
