# WebApp

A PHP-based web application with Docker containerization for easy deployment. The application features user authentication, product management, order processing, and role-based access control for customers, partners, and delivery personnel.

## Features

- User authentication (login/register) with OTP verification
- Role-based access control (Customer, Partner, Delivery)
- Product management (add, update, view products)
- Order management (create, accept, track orders)
- Payment processing integration
- Profile management for all user types
- Search functionality
- File upload capabilities
- RESTful API endpoints

## Project Structure

```
/page           # Frontend assets (HTML, CSS, JavaScript, images)
/src            # Backend PHP source code
  routes.php    # Application routing and API endpoints
  *.php         # Entity classes (Customer, Partner, Order, etc.)
  functions.php # Helper functions (file serving)
  Database.php  # Database connection handler
/db             # SQL initialization scripts for MySQL
/vendor         # Composer dependencies
uploads/        # File upload destination
report/         # Generated reports
```

## Prerequisites

- Docker and Docker Compose
- PHP 7.4+ (if running locally without Docker)
- MySQL 8.0+ (if running locally without Docker)

## Installation & Setup

### Using Docker (Recommended)

1. Clone the repository:
   ```bash
   git clone <repository-url>
   cd webapp
   ```

2. Start the application:
   ```bash
   docker compose up -d
   ```

3. The application will be available at:
   - Main app: http://localhost/home
   - phpMyAdmin: http://localhost:8001 (username: root, password: manzi)

### Manual Installation (Without Docker)

1. Install PHP dependencies:
   ```bash
   composer install
   ```

2. Set up MySQL database:
   - Create a database named `webproject`
   - Import the SQL files from the `db/` directory
   - Update database credentials in `.env` file

3. Start PHP built-in server:
   ```bash
   php -S localhost:80 -t . src/routes.php
   ```

4. Access the application at http://localhost

## Environment Variables

Copy `.env.example` to `.env` and configure:

```
# Database Configuration
DB_HOST=localhost
DB_PORT=3306
DB_NAME=webproject
DB_USER=root
DB_PASS=your_password
DB_CHARSET=utf8mb4

# Application Environment
APP_ENV=development
APP_DEBUG=true

# API Configuration (if needed)
user_id=your_user_id
Token=your_token
```

## API Endpoints

All API endpoints are defined in `src/routes.php`. Key endpoints include:

### Authentication
- `POST /login` - Customer login
- `POST /login/partner` - Partner login
- `POST /deliver/login` - Delivery personnel login
- `POST /register/customer` - Customer registration
- `POST /register/partner` - Partner registration
- `POST /deliver/register` - Delivery personnel registration
- `POST /forgot/check` - Password reset initiation
- `GET /reset/{origin}/{email}` - Password reset form
- `GET /retrive/{origin}` - Password reset processing
- `POST /customer/change_pass` - Change password
- `GET /logout/*` - Logout endpoints

### Customer
- `GET /customer/profile` - View profile
- `POST /customer/update_profile` - Update profile
- `GET /home` - Customer home page
- `GET /payment` - Payment page
- `POST /pay` - Process payment
- `POST /order` - Create order
- `GET /order/cart` - View cart
- `POST /order/quantity_change` - Update cart quantity
- `POST /order/delete` - Remove item from cart
- `POST /search` - Search products
- `POST /search/retrive` - Get search results
- `GET /products/all` - Get all products
- `GET /product/best` - Get best sellers
- `GET /products/category` - Get products by category/partner

### Partner
- `GET /partner/profile` - View partner profile
- `POST /partner/update_profile` - Update partner profile
- `GET /partner/home` - Partner dashboard
- `GET /partner/get_profile` - Get partner ID
- `GET /partner/all` - Get active orders
- `GET /partner/categories` - Get categories for product addition
- `POST /partner/check/mail` - Check email availability
- `POST /partner/check/tel` - Check phone availability
- `GET /products/partner` - Get partner's products
- `POST /change/image` - Update profile image
- `POST /add_product` - Add new product
- `POST /product/update` - Update product
- `POST /orders/accept` - Accept order
- `POST /orders/partner` - Get orders for partner

### Delivery Personnel
- `GET /deliver` - Delivery login page
- `GET /deliver/profile` - View delivery profile
- `GET /deliver/murugo` - Delivery dashboard
- `GET /deliver/retive` - Get assigned orders
- `GET /deliver/accept/{order_id}` - Accept delivery assignment
- `GET /deliver/current_orders` - Get current deliveries
- `GET /deliver/done` - Mark delivery as complete
- `GET /deliver/history` - Get delivery history
- `POST /deliver/register` - Register as delivery personnel
- `POST /deliver/login` - Delivery personnel login
- `GET /deliver/logout` - Delivery logout
- `POST /deliver/OTP/{counter}` - OTP verification for delivery
- `POST /deliver/register` - Delivery registration

## Database Schema

The application uses MySQL with the following main tables:
- `users` - Stores user information (customers, partners, delivery)
- `products` - Product catalog
- `orders` - Order records
- `order_items` - Items within orders
- `categories` - Product categories
- Additional tables for OTP, payments, sessions, etc.

Refer to the SQL files in the `db/` directory for complete schema.

## Development

### Making Changes
- Frontend: Edit files in `/page` directory
- Backend: Edit PHP files in `/src` directory
- Routes: Modify `src/routes.php` for API endpoints
- Database: Add SQL files to `/db` directory (they execute on container startup if tables don't exist)

### Debugging
- PHP error log: Check `/var/log/php_errors.log` inside the web container
- Enable debug mode: Set `APP_DEBUG=true` in `.env`
- Use browser developer tools for frontend debugging
- Use `var_dump()` or `error_log()` for backend debugging (remove before committing)

## Security Notes

- Passwords are hashed using PHP's `password_hash()` function
- All database queries use prepared statements to prevent SQL injection
- File uploads are restricted to safe extensions (JPG, PNG, GIF, WebP)
- Session management uses PHP's native sessions
- Input validation is performed on all form submissions
- CORS considerations: Adjust as needed for production deployment

## Deployment

For production deployment:
1. Set `APP_ENV=production` in `.env`
2. Disable debug mode: `APP_DEBUG=false`
3. Update database credentials for production environment
4. Consider using a proper web server (nginx/apache) instead of PHP built-in server
5. Set up proper SSL/TLS certificates
6. Configure appropriate file permissions
7. Set up regular database backups

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Ensure code follows existing style
5. Test your changes thoroughly
6. Submit a pull request

## License

[Specify your license here]

## Contact

[Your contact information]
