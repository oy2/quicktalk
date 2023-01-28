
## quicktalk

Quicktalk is a messaging app writing in Laravel and Vue.js. It is a simple app that allows users to send messages to each other and form groups to chat with multiple people.
It demonstrates the use of Vue to enable reactive components and Laravel to handle the backend. Additionally, it uses Laravel Echo to enable real-time updates.

### Table of Contents
- [Features](#features)
- [Requirements](#requirements)
- [Running](#running)
- [Screenshots](#screenshots)

### Key Features
- Real-time messaging (direct and group)
- User authentication (login, registration, password reset)
- User profile (update name, email, password, profile picture) & editor

### Requirements
- PHP >= 7.1.3
- Composer
- Node.js
- NPM
- SQL Database (e.g. MySQL, SQLite)
- Pusher -- or any broadcast driver supported by Laravel
- Redis -- or any queue driver supported by Laravel

docker-compose.yml can easily generate a development environment

### Running
This is a laravel application so npm and composer are required to run it. To install the dependencies run the following commands:

    npm install
    composer install

After installing the dependencies, you need to create a `.env` file. You can copy the `.env.example` file and rename it to `.env`. Then, you need to generate an application key by running the following command:
    
    php artisan key:generate

After that, you need to create a database and update the `.env` file with the database credentials. Then, run the following command to run the migrations:
    
    php artisan migrate

By default, the app is setup to use the `pusher` driver for broadcasting. You can change this in the `.env` file. If you want to use pusher, you need to create an account on pusher and update the `.env` file with the credentials.

After that, you need to run the following command to compile the assets:
    
    npm run build

To run the application, run the following command:
    
    php artisan serve

### Screenshots

#### Chat Window
![Chat Window](docs/images/chatdemo.png
)

### Creation
![Chat Window](docs/images/creation.png
)
