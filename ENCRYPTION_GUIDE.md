# 🔐 Data Encryption Implementation Guide

## Overview
This document describes the field-level encryption implementation for protecting sensitive customer data (phone numbers and addresses) in compliance with data protection regulations.

---

## 🎯 What is Encrypted

### Customer Data:
- ✅ **Phone Numbers** - Encrypted using AES-256-CBC
- ✅ **Addresses** - Encrypted using AES-256-CBC

### How It Works:
1. **Encryption**: Data is encrypted before storing in database
2. **Hashing**: SHA-256 hash stored for searching
3. **Decryption**: Data is decrypted when accessed
4. **Masking**: Partial masking for display in lists

---

## 🔑 Encryption Architecture

### Database Schema:

```sql
customers table:
├── phone              (VARCHAR) - Legacy, will be deprecated
├── phone_encrypted    (TEXT)    - AES-256 encrypted phone
├── phone_hash         (VARCHAR) - SHA-256 hash for searching
├── address            (VARCHAR) - Legacy, will be deprecated  
├── address_encrypted  (TEXT)    - AES-256 encrypted address
└── address_hash       (VARCHAR) - SHA-256 hash for searching
```

### Encryption Flow:

```
User Input → Validation → Encryption → Database
   ↓                          ↓
   └─────────────────→ Hash (for search)

Database → Decryption → Display
   ↓
   └─────────→ Masking (for lists)
```

---

## 📝 Implementation Details

### 1. Encryption Service

Location: `app/Services/EncryptionService.php`

```php
// Encrypt data
$encrypted = EncryptionService::encrypt($plaintext);

// Decrypt data
$plaintext = EncryptionService::decrypt($encrypted);

// Hash for searching
$hash = EncryptionService::hash($plaintext);

// Mask for display
$masked = EncryptionService::maskPhone('01234567890');
// Result: 012****7890
```

### 2. Model Accessors & Mutators

The `Customer` model automatically handles encryption:

```php
// Creating a customer
$customer = Customer::create([
    'name' => 'أحمد محمد',
    'phone' => '01234567890',      // Auto-encrypted
    'address' => 'القاهرة، مصر'     // Auto-encrypted
]);

// Reading data
echo $customer->phone;              // Auto-decrypted: 01234567890
echo $customer->masked_phone;       // Masked: 012****7890
echo $customer->address;            // Auto-decrypted
echo $customer->masked_address;     // Masked: القاهرة، مصر...
```

### 3. Searching Encrypted Data

Use hash-based search:

```php
// Search by phone
$customers = Customer::searchByPhone('01234567890');

// Search by address
$customers = Customer::searchByAddress('القاهرة');
```

---

## 🛡️ Security Features

### Encryption Algorithm:
- **Algorithm**: AES-256-CBC
- **Key**: Laravel APP_KEY (32 bytes)
- **IV**: Random initialization vector per encryption
- **Padding**: PKCS7

### Hash Algorithm:
- **Algorithm**: SHA-256
- **Purpose**: Enable searching without decryption
- **Indexed**: For fast lookups

### Key Management:
- **Storage**: `.env` file (APP_KEY)
- **Rotation**: Supported (requires re-encryption)
- **Backup**: Critical - store securely

---

## 📊 Performance Considerations

### Impact:
- **Write**: +5-10ms per encrypted field
- **Read**: +2-5ms per encrypted field
- **Search**: No impact (uses hash index)

### Optimization:
- ✅ Hashes indexed for fast searching
- ✅ Lazy decryption (only when accessed)
- ✅ Caching recommended for frequently accessed data

---

## 🔄 Migration Process

### Step 1: Add Encrypted Columns
```bash
php artisan migrate
```

### Step 2: Encrypt Existing Data
```bash
php encrypt_existing_data.php
```

### Step 3: Verify Encryption
```bash
# Check database
SELECT 
    name,
    phone,
    LEFT(phone_encrypted, 50) as encrypted,
    phone_hash
FROM customers
LIMIT 5;
```

### Step 4: Update Application Code
All controllers automatically use encrypted fields via model accessors.

### Step 5: Remove Plain Text (Optional)
After thorough testing:
```sql
ALTER TABLE customers 
DROP COLUMN phone,
DROP COLUMN address;
```

---

## 🧪 Testing

### Test Encryption:
```php
use App\Services\EncryptionService;

$original = '01234567890';
$encrypted = EncryptionService::encrypt($original);
$decrypted = EncryptionService::decrypt($encrypted);

assert($original === $decrypted);
```

### Test Model:
```php
$customer = Customer::create([
    'name' => 'Test',
    'phone' => '01234567890'
]);

// Verify encryption
assert(!empty($customer->phone_encrypted));
assert(!empty($customer->phone_hash));

// Verify decryption
assert($customer->phone === '01234567890');

// Verify masking
assert($customer->masked_phone === '012****7890');
```

### Test Search:
```php
$customers = Customer::searchByPhone('01234567890');
assert($customers->count() > 0);
```

---

## 🚨 Important Security Notes

### DO:
✅ Keep APP_KEY secure and backed up
✅ Use HTTPS in production
✅ Regularly rotate encryption keys
✅ Monitor decryption failures
✅ Audit access to encrypted data
✅ Backup encrypted data regularly

### DON'T:
❌ Store APP_KEY in version control
❌ Share APP_KEY publicly
❌ Use weak encryption keys
❌ Log decrypted sensitive data
❌ Expose encrypted data in API responses
❌ Allow direct database access to encrypted fields

---

## 🔐 Key Rotation

If you need to rotate the encryption key:

### 1. Backup Current Data
```bash
php artisan db:backup
```

### 2. Generate New Key
```bash
php artisan key:generate --show
```

### 3. Re-encrypt Data
```php
// Create re-encryption script
// Decrypt with old key, encrypt with new key
```

### 4. Update .env
```env
APP_KEY=new_key_here
```

### 5. Clear Caches
```bash
php artisan config:clear
php artisan cache:clear
```

---

## 📋 Compliance

### GDPR Compliance:
✅ Data minimization (only necessary data encrypted)
✅ Right to erasure (can delete encrypted data)
✅ Data portability (can export decrypted data)
✅ Security of processing (AES-256 encryption)

### Best Practices:
✅ Encryption at rest
✅ Encryption in transit (HTTPS)
✅ Access controls
✅ Audit logging
✅ Regular security reviews

---

## 🆘 Troubleshooting

### Decryption Fails:
```
Error: "The payload is invalid"
```
**Solution**: APP_KEY changed or data corrupted

### Search Not Working:
```
No results found
```
**Solution**: Ensure hash is generated correctly

### Performance Issues:
```
Slow queries
```
**Solution**: 
- Add indexes to hash columns
- Implement caching
- Use eager loading

---

## 📞 Support

For encryption-related issues:
1. Check logs: `storage/logs/laravel.log`
2. Verify APP_KEY is set
3. Test encryption service directly
4. Contact system administrator

---

## 📚 References

- [Laravel Encryption](https://laravel.com/docs/encryption)
- [AES-256-CBC](https://en.wikipedia.org/wiki/Advanced_Encryption_Standard)
- [GDPR Compliance](https://gdpr.eu/)
- [Data Protection Best Practices](https://owasp.org/www-project-top-ten/)

---

**Last Updated:** November 2, 2025
**Version:** 1.0
**Status:** ✅ Active
