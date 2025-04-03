# AINP Demo Backend API

This is the backend API for the AINP Demo site. It provides endpoints for managing users, content, media, and settings.

## Setup Instructions

1. Make sure you have XAMPP installed and running with Apache and MySQL services.

2. Create the database and tables:
   - Open phpMyAdmin (http://localhost/phpmyadmin)
   - Create a new database named `ainp_db`
   - Import the `database.sql` file to create the tables and insert default data

3. Configure the database connection:
   - Open `config.php`
   - Update the database credentials if needed:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_USER', 'root');
     define('DB_PASS', '');
     define('DB_NAME', 'ainp_db');
     ```

4. Default admin credentials:
   - Email: ainvisibilitymedia@gmail.com
   - Password: password

## API Endpoints

### Users API (`users.php`)

- `GET /api/users` - Get all users
- `GET /api/users?search=term` - Search users by name or email
- `POST /api/users` - Create a new user
- `PUT /api/users` - Update an existing user
- `DELETE /api/users?id=1` - Delete a user

### Content API (`content.php`)

- `GET /api/content` - Get all content
- `GET /api/content?search=term` - Search content by title or content
- `GET /api/content?section=name` - Get content by section
- `POST /api/content` - Create new content
- `PUT /api/content` - Update existing content
- `DELETE /api/content?id=1` - Delete content

### Media API (`media.php`)

- `GET /api/media` - Get all media
- `GET /api/media?search=term` - Search media by title or description
- `GET /api/media?type=image` - Get media by type (image, video, document)
- `POST /api/media` - Upload new media
- `PUT /api/media` - Update existing media
- `DELETE /api/media?id=1` - Delete media

### Settings API (`settings.php`)

- `GET /api/settings` - Get all settings
- `POST /api/settings` - Create a new setting
- `PUT /api/settings` - Update an existing setting

## Request/Response Format

### Request Format

For POST and PUT requests, send data in JSON format:

```json
{
  "field1": "value1",
  "field2": "value2"
}
```

### Response Format

All responses are in JSON format:

```json
{
  "success": true,
  "message": "Operation successful",
  "data": {
    // Response data
  }
}
```

## Error Handling

Errors are returned with appropriate HTTP status codes:

- 400 Bad Request - Invalid input data
- 401 Unauthorized - Authentication required
- 403 Forbidden - Insufficient permissions
- 404 Not Found - Resource not found
- 405 Method Not Allowed - Invalid HTTP method
- 500 Internal Server Error - Server error

## Security

- All passwords are hashed using bcrypt
- Input validation is performed on all endpoints
- SQL injection prevention using prepared statements
- CORS headers are set to allow cross-origin requests

## File Structure

```
admin/api/
├── config.php         - Database configuration
├── database.sql       - Database schema and initial data
├── users.php          - Users API endpoint
├── content.php        - Content API endpoint
├── media.php          - Media API endpoint
└── settings.php       - Settings API endpoint
``` 