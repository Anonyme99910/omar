# Parfumes Laravel Backend API

## Installation

1. Install dependencies:
```bash
composer install
```

2. Configure environment:
```bash
cp .env.example .env
php artisan key:generate
```

3. Configure database in `.env`:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=parfumes
DB_USERNAME=root
DB_PASSWORD=
```

4. Run migrations:
```bash
php artisan migrate
```

5. Start server:
```bash
php artisan serve
```

## API Endpoints

### Authentication
- POST `/api/register` - Register new user
- POST `/api/login` - Login user
- POST `/api/logout` - Logout user
- GET `/api/user` - Get authenticated user
- PUT `/api/user/profile` - Update user profile
- PUT `/api/user/password` - Change password

### Properties
- GET `/api/properties` - Get all properties
- GET `/api/properties/{id}` - Get property by ID
- POST `/api/properties` - Create new property
- PUT `/api/properties/{id}` - Update property
- DELETE `/api/properties/{id}` - Delete property
- GET `/api/user/properties` - Get user's properties

### Favorites
- GET `/api/favorites` - Get user's favorites
- POST `/api/favorites/{propertyId}` - Add to favorites
- DELETE `/api/favorites/{propertyId}` - Remove from favorites

### Images
- POST `/api/upload` - Upload image
