# Promo APIs 

## Project Description

The project is about implementation of Promocode APIs with many features for SafeBoda built with [Laravel 8](https://laravel.com) and [Google Geocoding API](https://developers.google.com/maps/documentation/geocoding/start). The features of this project include

1. Authentication of Users
2. Generation of promos
3. Management of promos

## Project Setup

### Cloning the GitHub Repository

Clone the repository to your local machine by running the terminal command below.

```bash
git clone https://github.com/Oluwablin/safeboda-api
```

### Setup Database

Create a MySQL database and note down the required connection parameters. (DB Host, Username, Password, Name)

### Install Composer Dependencies

Navigate to the project root directory via terminal and run the following command.

```bash
composer install
```

### Create a copy of your .env file

Run the following command

```bash
cp .env.example .env
```

This should create an exact copy of the .env.example file. Name the newly created file .env and update it with your local environment variables (database connection info and others).

### Generate an app encryption key

```bash
php artisan key:generate
```

### Generate a jwt encryption secret key

```bash
php artisan jwt:secret
```

### Migrate the database

```bash
php artisan migrate
```

### Add the required environment variables.

GOOGLE_MAPS_API_KEY

### API DOCUMENTATION.

Examples of requests and the response for each endpoint can be found [here](https://documenter.getpostman.com/view/11139475/TzY69ZeY)
