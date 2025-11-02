-- Products Table Restructure for Perfume Inventory
-- Run this in phpMyAdmin or MySQL client

-- Step 1: Set segment prices from selling_price (columns already exist)
UPDATE products SET 
  price_جملة = ROUND(selling_price * 0.85, 2),
  price_قطاعي = selling_price,
  price_صفحة = ROUND(selling_price * 1.1, 2)
WHERE price_جملة = 0;

-- Step 2: Set random volumes (50, 100, 150, 200 mL)
UPDATE products 
SET volume_ml = ELT(FLOOR(1 + RAND() * 4), 50, 100, 150, 200)
WHERE volume_ml = 100;

-- Step 3: Rename columns
ALTER TABLE products 
CHANGE COLUMN stock_quantity quantity INT NOT NULL DEFAULT 0;

ALTER TABLE products 
CHANGE COLUMN min_stock_level alert_quantity INT NOT NULL DEFAULT 10;

ALTER TABLE products 
CHANGE COLUMN images photos LONGTEXT NULL;

-- Step 4: Drop unused columns one by one
ALTER TABLE products DROP COLUMN description;
ALTER TABLE products DROP COLUMN category_id;
ALTER TABLE products DROP COLUMN brand_id;
ALTER TABLE products DROP COLUMN cost_price;
ALTER TABLE products DROP COLUMN barcode;
ALTER TABLE products DROP COLUMN reserved_qty;
ALTER TABLE products DROP COLUMN size;
ALTER TABLE products DROP COLUMN image;

-- Step 5: Verify final structure
SELECT 'Products table restructured successfully!' as Status;
SHOW COLUMNS FROM products;
