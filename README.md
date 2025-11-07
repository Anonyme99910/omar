# 🌸 Perfume Store Management System

A comprehensive full-stack web application for managing a perfume store, built with Laravel (Backend) and Vue.js (Frontend).

## 📋 Table of Contents

- [Features](#features)
- [Tech Stack](#tech-stack)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Project Structure](#project-structure)
- [API Documentation](#api-documentation)
- [Screenshots](#screenshots)
- [Contributing](#contributing)
- [License](#license)

## ✨ Features

### 🛍️ Point of Sale (POS)
- **Smart Customer Selection** with search by name/phone
- **Product Grid View** with real-time stock display
- **Package Management** for bundled products
- **Packaging Options** (with/without box) with custom pricing
- **Multiple Payment Methods** (Cash, Transfer)
- **Partial Payment Support** with remaining balance tracking
- **Invoice Generation** with PDF download
- **WhatsApp Integration** for invoice sharing
- **Responsive Design** - Full mobile support with sticky cart

### 📦 Inventory Management
- **Product Management** (CRUD operations)
- **Production Cost Tracking** for profit analysis
- **Auto-Price Calculation** (Wholesale → Retail → Online)
- **Stock Alerts** with customizable thresholds
- **Low Stock Notifications**
- **Photo Upload** for products
- **Volume/Size Tracking** (ml)
- **SKU Management**

### 📊 Import/Export
- **Excel/CSV Import** with template download
- **SQL Import** with security validation
- **Bulk Product Upload** (up to 1000 items)
- **Import Guide** with detailed instructions
- **Error Handling** with detailed reports

### 👥 Customer Management
- **Customer Database** with encrypted data
- **Segment-Based Pricing** (Wholesale, Retail, Online)
- **Purchase History** tracking
- **Phone Number Encryption** for security
- **Smart Search** with fuzzy matching

### 📈 Analytics & Reports
- **Dashboard** with real-time statistics
- **Sales Analysis** (Daily, Monthly)
- **Stock Value Calculation** based on production cost
- **Profit Margin Analysis**
- **Top Products** tracking
- **Top Customers** ranking
- **Inventory Net Worth** display

### 🧾 Invoice System
- **Professional PDF Invoices** with modern design
- **Invoice Numbering** (auto-generated)
- **Invoice History** with search
- **Payment Status** tracking (Paid, Partial, Unpaid)
- **Void Invoice** functionality
- **Invoice Details** view

### 🔐 Security
- **Laravel Sanctum** authentication
- **Role-Based Access Control** (Admin, Employee)
- **Data Encryption** for sensitive information
- **CORS Protection**
- **XSS Prevention**
- **SQL Injection Protection**

### 📱 Responsive Design
- **Mobile-First** approach
- **Desktop Optimized** layouts
- **Touch-Friendly** controls
- **Progressive Web App** ready

## 🛠️ Tech Stack

### Backend
- **Framework:** Laravel 10.x
- **Database:** MySQL
- **Authentication:** Laravel Sanctum
- **PDF Generation:** TCPDF
- **Excel Processing:** Maatwebsite Excel
- **Encryption:** Laravel Encryption

### Frontend
- **Framework:** Vue.js 3 (Composition API)
- **Build Tool:** Vite
- **Styling:** TailwindCSS
- **Icons:** Lucide Vue Next
- **HTTP Client:** Axios
- **Notifications:** Vue Toastification
- **Router:** Vue Router

### Development Tools
- **Version Control:** Git
- **Package Manager:** npm, Composer
- **Code Editor:** VS Code (recommended)

## 📥 Installation

### Prerequisites
- PHP >= 8.1
- Composer
- Node.js >= 16.x
- MySQL >= 5.7
- Git

### Backend Setup

```bash
# Clone the repository
git clone https://github.com/Anonyme99910/omar.git
cd omar

# Navigate to backend directory
cd backend

# Install PHP dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure database in .env file
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=perfume_store
DB_USERNAME=root
DB_PASSWORD=

# Run migrations
php artisan migrate

# Seed database (optional)
php artisan db:seed

# Create storage link
php artisan storage:link

# Start development server
php artisan serve
```

### Frontend Setup

```bash
# Navigate to frontend directory
cd ../frontend

# Install dependencies
npm install

# Configure API endpoint in .env
VITE_API_URL=http://localhost:8000/api

# Start development server
npm run dev

# Build for production
npm run build
```

## ⚙️ Configuration

### Environment Variables

#### Backend (.env)
```env
APP_NAME="Perfume Store"
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=perfume_store
DB_USERNAME=root
DB_PASSWORD=

SANCTUM_STATEFUL_DOMAINS=localhost:5173,localhost
SESSION_DOMAIN=localhost
```

#### Frontend (.env)
```env
VITE_API_URL=http://localhost:8000/api
```

## 🚀 Usage

### Default Credentials
```
Username: admin
Password: admin123
```

### Quick Start Guide

1. **Login** with default credentials
2. **Add Products** via Stock Management
3. **Add Customers** (optional)
4. **Open POS** and start selling
5. **Generate Invoices** automatically
6. **View Reports** in Dashboard

## 📁 Project Structure

```
parfumes/
├── backend/                 # Laravel Backend
│   ├── app/
│   │   ├── Http/
│   │   │   └── Controllers/ # API Controllers
│   │   ├── Models/          # Eloquent Models
│   │   └── Imports/         # Excel Import Classes
│   ├── database/
│   │   ├── migrations/      # Database Migrations
│   │   └── seeders/         # Database Seeders
│   ├── resources/
│   │   └── views/           # Blade Templates (PDF)
│   ├── routes/
│   │   └── api.php          # API Routes
│   └── storage/
│       └── app/
│           ├── invoices/    # Generated PDFs
│           └── templates/   # Import Templates
│
├── frontend/                # Vue.js Frontend
│   ├── src/
│   │   ├── components/      # Vue Components
│   │   ├── views/           # Page Views
│   │   ├── router/          # Vue Router
│   │   ├── services/        # API Services
│   │   └── utils/           # Utility Functions
│   ├── public/              # Static Assets
│   └── dist/                # Production Build
│
└── README.md                # This file
```

## 📡 API Documentation

### Authentication
```
POST /api/login
POST /api/logout
GET  /api/user
```

### Products
```
GET    /api/products
POST   /api/products
GET    /api/products/{id}
PUT    /api/products/{id}
DELETE /api/products/{id}
GET    /api/products/search
GET    /api/products/low-stock
```

### Sales
```
GET    /api/sales
POST   /api/sales
GET    /api/sales/{id}
PUT    /api/sales/{id}
GET    /api/sales/{id}/pdf
```

### Customers
```
GET    /api/customers
POST   /api/customers
GET    /api/customers/{id}
PUT    /api/customers/{id}
DELETE /api/customers/{id}
```

### Import
```
POST /api/products/import/excel
POST /api/products/import/sql
GET  /api/products/import/template
GET  /api/products/import/guide
```

### Reports
```
GET /api/dashboard
GET /api/reports/sales
```

## 📸 Screenshots

### Dashboard
![Dashboard](screenshots/dashboard.png)

### POS System
![POS](screenshots/pos.png)

### Product Management
![Products](screenshots/products.png)

### Invoice PDF
![Invoice](screenshots/invoice.png)

## 🎯 Key Features Explained

### Packaging System
- Products can be sold with or without packaging
- Custom packaging quantity and price per item
- Packaging cost included in invoice total
- Displayed in cart and PDF invoice

### Smart Pricing
- Three pricing tiers: Wholesale (جملة), Retail (قطاعي), Online (صفحة)
- Auto-calculation: Retail = Wholesale × 1.15, Online = Wholesale × 1.25
- Customer segment determines pricing
- Manual override available

### Production Cost Tracking
- Track actual product cost
- Calculate profit margins
- Stock value based on production cost
- Potential profit analysis

### Mobile POS
- Full-screen cart on mobile
- Sticky bottom button with summary
- All desktop features available
- Touch-optimized controls

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 👨‍💻 Author

**Omar**
- GitHub: [@Anonyme99910](https://github.com/Anonyme99910)

## 🙏 Acknowledgments

- Laravel Framework
- Vue.js Framework
- TailwindCSS
- TCPDF Library
- All open-source contributors

## 📞 Support

For support, email: [your-email@example.com]

## 🔄 Version History

### v1.0.0 (2025-11-07)
- Initial release
- POS system with packaging support
- Product management with import/export
- Customer management with encryption
- Invoice generation with PDF
- Dashboard with analytics
- Mobile responsive design

---

Made with ❤️ by Omar
