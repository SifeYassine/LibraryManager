# Installation Guide

## Prerequisites

Before you begin, ensure you have met the following requirements:

-   [Composer](https://getcomposer.org/download/) installed on your machine
-   [Node.js and npm](https://nodejs.org/) installed on your machine

## Cloning the Repository

1. **Open your terminal.**
2. **Navigate to the directory where you want to clone the repository:**

    ```bash
    cd path/to/your/directory
    ```

3. **Run the following command to clone the repository:**

    ```bash
    git clone https://github.com/SifeYassine/LibraryManager.git
    ```

## Installing Dependencies

1. **Install PHP dependencies using Composer:**

    ```bash
    composer install
    ```

2. **Install JavaScript dependencies using npm:**

    ```bash
    npm install
    ```

3. **Build the project:**

    ```bash
     npm run build
    ```

## Setting Up Environment Variables

1. **Open the `.env` file in a text editor and set up your database and other environment variables as needed:**

    ```env
    APP_NAME=Laravel
    APP_ENV=local
    APP_KEY=base64:...
    APP_DEBUG=true
    APP_URL=http://localhost

    LOG_CHANNEL=stack
    LOG_DEPRECATIONS_CHANNEL=null
    LOG_LEVEL=debug

    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=your_database
    DB_USERNAME=your_username
    DB_PASSWORD=your_password...
    ```

## Generating Application Key

1. **Generate the application key:**

    ```bash
    php artisan key:generate
    ```

## Running Migrations and Seeders

1. **Run the database migrations:**

    ```bash
    php artisan migrate
    ```

## Serving the Application

1. **Serve the application locally:**

    ```bash
    php artisan serve
    ```

2. **Open your browser and navigate to the following URL:**

    ```
    http://127.0.0.1:8000
    ```

If you encounter any issues, please refer to the [Laravel Documentation](https://laravel.com/docs) or open an issue in the repository.

Happy coding!
