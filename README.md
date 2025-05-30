# Invites Management API

A robust RESTful API built with Laravel for managing invitations and guest lists. This API serves as the backend for the [Invites Management System](https://invites-managment.vercel.app/).

## 🌟 Features

-   **Authentication System**

    -   Secure login/logout functionality
    -   Token-based authentication using Laravel Sanctum
    -   Protected routes for authorized access

-   **Invites Management**
    -   CRUD operations for invites
    -   Guest presence tracking
    -   Real-time status updates

## 🚀 Tech Stack

-   **Framework:** Laravel 10.x
-   **Authentication:** Laravel Sanctum
-   **Database:** MySQL
-   **Testing:** PHPUnit
-   **API Documentation:** OpenAPI/Swagger

## 📋 API Endpoints

### Authentication

```bash
POST /api/auth/login    # User login
POST /api/auth/logout   # User logout (protected)
```

### Invites Management (Protected Routes)

```bash
GET    /api/invites          # List all invites
POST   /api/invites          # Create new invite
GET    /api/invites/{id}     # Get specific invite
PUT    /api/invites/{id}     # Update invite
DELETE /api/invites/{id}     # Delete invite
PUT    /api/invites/{id}/presence  # Update invite presence
```

## 🛠️ Installation

1. **Clone the repository**

```bash
git clone https://github.com/YOUR_USERNAME/invites-managment-api.git
cd invites-managment-api
```

2. **Install dependencies**

```bash
composer install
npm install
```

3. **Environment Setup**

```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure your database in `.env`**

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

5. **Run migrations**

```bash
php artisan migrate
```

6. **Start the server**

```bash
php artisan serve
```

## 🔒 Security

-   All sensitive routes are protected with authentication
-   CORS configuration for secure cross-origin requests
-   Input validation and sanitization
-   Rate limiting on API endpoints

## 🔗 Frontend Integration

This API is integrated with a Next.js frontend application. You can find the frontend repository at:

-   Repository: [github.com/YanesAbdelkader/invites-managment](https://github.com/YanesAbdelkader/invites-managment)
-   Live Demo: [invites-managment.vercel.app](https://invites-managment.vercel.app/)

## 👥 Contributors

-   [@YanesAbdelkader](https://github.com/YanesAbdelkader)
-   [@Juliettelfkk](https://github.com/Juliettelfkk)

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request. For major changes, please open an issue first to discuss what you would like to change.

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request
