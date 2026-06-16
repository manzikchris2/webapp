 # webbapp.md
    2 
    3 
    4 
    5 ## Project Overview
    6 
    7 This is a PHP web application with a Docker-based development environment. The application consists of:
    8 - **Frontend**: HTML, CSS, and JavaScript files in the `page/` directory
    9 - **Backend**: PHP classes and routing logic in the `src/` directory
   10 - **Database**: MySQL initialized via Docker (see `docker-compose.yml`)
   11 - **Dependencies**: Managed via Composer (see `composer.json`)
   12
   13 ## Development Commands
   14
   15 ### Docker Environment
   16 - Start containers: `docker compose up -d`
   17 - Stop containers: `docker compose down`
   18 - Rebuild containers: `docker compose up --build -d`
   19 - View logs: `docker compose logs -f`
   20 - Access phpMyAdmin: http://localhost:8001 (credentials: root/manzi)
   21
   22 ### PHP Dependencies
   23 - Install dependencies: `composer install`
   24 - Update dependencies: `composer update`
   25
   26 ### Application Access
   27 - Main application: http://localhost:80/home
   28 - API endpoints are defined in `src/routes.php`
   29
   30 ### Database
   31 - Initialization scripts are placed in the `db/` directory (mounted to MySQL container)
   32 - Database credentials are in `.env` file:
   33   - Host: `db` (service name in docker-compose)
   34   - Database: `webproject`
   35   - User: `root`
   36   - Password: `manzi`
   37
   38 ## Project Structure
   39
   40 ```
   41 /page          # Frontend assets (HTML, CSS, JS, images)
   42 /src           # Backend PHP source code
   43   routes.php   # Application routing and API endpoints
   44   *.php        # Entity classes (Customer, Partner, Order, etc.)
   45   functions.php# Helper functions (file serving)
   46   Database.php # Database connection handler
   47 /db            # SQL initialization scripts for MySQL
   48 /vendor        # Composer dependencies
   49 uploads/       # File upload destination
   50 report/        # Generated reports (if any)
   51 ```
   52
   53 ## Key Components
   54
   55 ### Routing
   56 - All API routes are defined in `src/routes.php` using Phroute
   57 - Routes are prefixed with entity types (e.g., `/login`, `/register/customer`, `/product/*`)
   58 - Static file serving (CSS, JS, images) is handled via `file_request()` in `functions.php`
   59
   60 ### Authentication
   61 - Uses PHP sessions with `Checkpoint` class for role verification
   62 - Roles: customer, partner, deliver (rider)
   63 - Session variables: `customer_id`, `PartnersID`
   64
   65 ### Database Interaction
   66 - All database classes extend or use `Database.php` for PDO connections
   67 - Prepared statements are used throughout for security
   68 - Environment variables configure database connection
   69
   70 ### File Uploads
   71 - Uploads are handled via `Upload.php` and `Product::add_product()`
   72 - Files are stored in the `uploads/` directory
   73 - Accepted formats: JPG, PNG, GIF, WebP (max 10MB)
   74
   75 ## Development Workflow
   76
   77 1. Make changes to PHP files in `src/` - changes are immediately available due to volume mount
   78 2. Modify HTML/CSS/JS in `page/` - refresh browser to see changes
   79 3. Database schema changes: add SQL files to `db/` directory (they will execute on container startup if table doesn't exist)
   80 4. For backend-only changes, no need to rebuild containers unless adding new PHP extensions
   81 5. Use `docker compose logs -f www` to monitor PHP application logs
   82 6. Use `docker compose logs -f db` to monitor database logs
   83
   84 ## Common Tasks
   85
   86 ### Adding a new API endpoint
   87 1. Edit `src/routes.php` to add a new route
   88 2. Create or modify a PHP class in `src/` for the business logic
   89 3. Test using browser or API client (e.g., curl, Postman)
   90
   91 ### Modifying frontend
   92 1. Edit files in `page/` directory
   93 2. Clear browser cache if CSS/JS changes don't appear
   94 3. Use browser developer tools for debugging
   95
   96 ### Debugging
   97 - Check PHP error log: `/var/log/php_errors.log` inside container
   98 - Enable/disable debug mode via `APP_DEBUG` in `.env`
   99 - Use `var_dump()` or `error_log()` for debugging (remove before committing)
  100
  101 ## Important Notes
  102
  103 - The application expects to run on port 80 (host) -> port 80 (container)
  104 - Ensure port 80 is free on host machine before starting containers
  105 - The `.env` file should not be committed to version control (it's already in .gitignore)
  106 - Session handling relies on PHP's native sessions - ensure session.save_path is writable
  107 - All SQL queries use parameterized statements to prevent injection
