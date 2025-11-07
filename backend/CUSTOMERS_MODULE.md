# Customers Module - Complete Documentation

## ✅ Backend Implementation

### Database
**Table**: `customers`
- `id` - Primary key
- `name` - Customer name (Arabic supported)
- `phone` - Egyptian phone number (required)
- `email` - Email address (optional)
- `address` - Full address (optional)
- `total_purchases` - Total amount spent
- `total_orders` - Number of orders
- `created_at` - Registration date
- `updated_at` - Last update

### API Endpoints

#### Public Endpoints
```
GET /api/customers - List all customers (paginated)
  Query params:
  - per_page: Number of items per page (default: 50)
  - search: Search by name, phone, or email
```

#### Protected Endpoints (Require Auth)
```
POST   /api/customers - Create new customer
GET    /api/customers/{id} - Get customer details with recent sales
PUT    /api/customers/{id} - Update customer
DELETE /api/customers/{id} - Delete customer
GET    /api/customers/{id}/history - Get customer purchase history (paginated)
```

### Request/Response Examples

#### Create Customer
**Request:**
```json
POST /api/customers
{
  "name": "أحمد محمد علي",
  "phone": "01012345678",
  "email": "ahmed@example.com",
  "address": "القاهرة، مصر الجديدة"
}
```

**Response:**
```json
{
  "id": 1,
  "name": "أحمد محمد علي",
  "phone": "01012345678",
  "email": "ahmed@example.com",
  "address": "القاهرة، مصر الجديدة",
  "total_purchases": 0,
  "total_orders": 0,
  "created_at": "2025-10-29T14:00:00.000000Z",
  "updated_at": "2025-10-29T14:00:00.000000Z"
}
```

#### Get Customer with Sales History
**Request:**
```
GET /api/customers/1
```

**Response:**
```json
{
  "id": 1,
  "name": "أحمد محمد علي",
  "phone": "01012345678",
  "email": "ahmed@example.com",
  "address": "القاهرة، مصر الجديدة",
  "total_purchases": 1500.00,
  "total_orders": 5,
  "sales": [
    {
      "id": 1,
      "invoice_number": "INV-001",
      "total": 350.00,
      "payment_method": "cash",
      "status": "completed",
      "created_at": "2025-10-29T14:00:00.000000Z"
    }
  ]
}
```

#### Get Detailed Purchase History
**Request:**
```
GET /api/customers/1/history?page=1
```

**Response:**
```json
{
  "customer": {
    "id": 1,
    "name": "أحمد محمد علي",
    "phone": "01012345678",
    "total_purchases": 1500.00,
    "total_orders": 5
  },
  "sales": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "invoice_number": "INV-001",
        "total": 350.00,
        "items": [
          {
            "product": {
              "id": 1,
              "name": "Dahn Al Oudh Moattaq",
              "name_ar": "دهن العود معتق"
            },
            "quantity": 1,
            "price": 250.00
          }
        ],
        "created_at": "2025-10-29T14:00:00.000000Z"
      }
    ],
    "total": 5,
    "per_page": 20
  }
}
```

---

## ✅ Frontend Implementation

### Pages

#### 1. Customers List (`/clients`)
**File**: `src/views/Customers.vue`

**Features:**
- ✅ Search by name, phone, or email
- ✅ Display customer info in table
- ✅ Show total purchases and orders
- ✅ Add new customer (modal)
- ✅ Edit customer (modal)
- ✅ Delete customer
- ✅ View customer details (navigate to details page)

**Actions:**
- **Add Customer** - Opens modal with form
- **Edit** - Opens modal with pre-filled data
- **Delete** - Confirms and deletes customer
- **View** - Navigates to customer details page

#### 2. Customer Details (`/clients/:id`)
**File**: `src/views/ClientDetails.vue`

**Features:**
- ✅ Display customer information
  - Name (Arabic)
  - Phone (Egyptian format)
  - Email
  - Address
  - Total purchases
  - Total orders
- ✅ Purchase history table
  - Invoice number
  - Date
  - Products list
  - Total amount
  - Status (completed/cancelled)

---

## 📱 Egyptian Phone Number Format

**Valid Formats:**
- `01012345678` - Vodafone
- `01123456789` - Etisalat
- `01234567890` - Orange
- `01512345678` - WE

**Validation:**
- Must start with `010`, `011`, `012`, or `015`
- Must be 11 digits
- No spaces or dashes required

---

## 🎨 UI Components

### Customer Form Fields
```vue
<input v-model="customerForm.name" type="text" required />
<input v-model="customerForm.phone" type="tel" required />
<input v-model="customerForm.email" type="email" />
<textarea v-model="customerForm.address" rows="3"></textarea>
```

### Customer Table Columns
1. **Name** (الاسم) - Arabic name
2. **Phone** (الهاتف) - Egyptian number
3. **Email** (البريد الإلكتروني) - Optional
4. **Total Purchases** (إجمالي المشتريات) - Currency formatted
5. **Total Orders** (عدد الطلبات) - Number
6. **Actions** (الإجراءات) - View, Edit, Delete buttons

---

## 🔄 Data Flow

### Adding a Customer
1. User clicks "إضافة عميل" button
2. Modal opens with empty form
3. User fills: name, phone, email (optional), address (optional)
4. Submit → `POST /api/customers`
5. Success → Toast notification + Refresh list
6. Modal closes

### Viewing Customer Details
1. User clicks eye icon on customer row
2. Navigate to `/clients/{id}`
3. Fetch customer data → `GET /api/customers/{id}`
4. Display customer info card
5. Display purchase history table
6. Each sale shows products purchased

### Editing Customer
1. User clicks edit icon
2. Modal opens with pre-filled data
3. User modifies fields
4. Submit → `PUT /api/customers/{id}`
5. Success → Toast notification + Refresh list
6. Modal closes

---

## 📊 Sample Data (Seeded)

5 Egyptian customers with Arabic names:

1. **أحمد محمد علي** - 01012345678
2. **فاطمة حسن** - 01123456789
3. **محمود السيد** - 01234567890
4. **نور الدين أحمد** - 01098765432
5. **سارة محمود** - 01187654321

---

## 🚀 Testing

### Test Customers API
```bash
# List customers
curl http://localhost/parfumes/backend/public/api/customers

# Get customer details
curl http://localhost/parfumes/backend/public/api/customers/1

# Get customer history
curl http://localhost/parfumes/backend/public/api/customers/1/history

# Create customer (requires auth)
curl -X POST http://localhost/parfumes/backend/public/api/customers \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{"name":"محمد أحمد","phone":"01012345678"}'
```

### Test Frontend
1. Navigate to `http://localhost:5173/clients`
2. Search for customer by name or phone
3. Click "إضافة عميل" to add new customer
4. Click eye icon to view customer details
5. Click edit icon to modify customer
6. Click delete icon to remove customer

---

## ✅ Features Checklist

### Backend
- [x] Customers table migration
- [x] Customer model with relationships
- [x] CustomerController with CRUD
- [x] Customer history endpoint
- [x] Pagination support
- [x] Search functionality
- [x] Egyptian customer seeder
- [x] API routes configured

### Frontend
- [x] Customers list page
- [x] Customer details page
- [x] Add customer modal
- [x] Edit customer modal
- [x] Delete confirmation
- [x] Search functionality
- [x] Purchase history display
- [x] Arabic UI support
- [x] Egyptian phone format
- [x] Currency formatting (EGP)

---

## 📝 Notes

- **Phone numbers** are stored without formatting (11 digits)
- **Email** is optional for customers
- **Address** is optional but recommended
- **Total purchases** auto-updates when sales are created
- **Total orders** auto-increments with each sale
- **Purchase history** shows last 50 sales by default
- **Pagination** available for large customer lists
- **Search** works on name, phone, and email fields

---

## 🎯 Next Steps

1. **Sales Integration**: Link sales to customers
2. **Customer Analytics**: Add charts for customer behavior
3. **Loyalty Program**: Track points and rewards
4. **SMS Notifications**: Send order updates via SMS
5. **Export**: Export customer list to Excel/PDF

---

**Status**: ✅ Complete - Customers module fully operational with Egyptian customer data!
