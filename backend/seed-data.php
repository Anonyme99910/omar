<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Property;
use Illuminate\Support\Facades\Hash;

echo "===========================================\n";
echo "  SEEDING MOCK DATA\n";
echo "===========================================\n\n";

// Create Mock Users
echo "[1/2] Creating mock users...\n";

$users = [
    [
        'full_name' => 'أحمد محمد',
        'email' => 'ahmed@example.com',
        'password' => Hash::make('password123'),
        'phone_number' => '12345678',
        'is_admin' => false,
        'is_active' => true,
    ],
    [
        'full_name' => 'فاطمة علي',
        'email' => 'fatima@example.com',
        'password' => Hash::make('password123'),
        'phone_number' => '23456789',
        'is_admin' => false,
        'is_active' => true,
    ],
    [
        'full_name' => 'محمود حسن',
        'email' => 'mahmoud@example.com',
        'password' => Hash::make('password123'),
        'phone_number' => '34567890',
        'is_admin' => false,
        'is_active' => true,
    ],
    [
        'full_name' => 'سارة خالد',
        'email' => 'sara@example.com',
        'password' => Hash::make('password123'),
        'phone_number' => '45678901',
        'is_admin' => false,
        'is_active' => false,
    ],
    [
        'full_name' => 'عمر يوسف',
        'email' => 'omar@example.com',
        'password' => Hash::make('password123'),
        'phone_number' => '56789012',
        'is_admin' => false,
        'is_active' => true,
    ],
];

$createdUsers = [];
foreach ($users as $userData) {
    $user = User::where('email', $userData['email'])->first();
    if (!$user) {
        $user = User::create($userData);
        echo "  ✅ Created user: {$userData['full_name']} ({$userData['email']})\n";
    } else {
        echo "  ⏭️  User exists: {$userData['full_name']}\n";
    }
    $createdUsers[] = $user;
}

echo "\n[2/2] Creating mock properties...\n";

$properties = [
    [
        'title' => 'شقة فاخرة في وسط المدينة',
        'description' => 'شقة حديثة مكونة من 3 غرف نوم وصالة كبيرة مع إطلالة رائعة على المدينة. تشطيب سوبر لوكس مع جميع المرافق.',
        'price' => 250000,
        'price_unit' => 'OMR',
        'location' => 'مسقط، روي',
        'size' => 150,
        'size_unit' => 'sqm',
        'phone_number' => '12345678',
        'category' => 'apartment',
        'status' => 'pending',
        'images' => json_encode([]),
    ],
    [
        'title' => 'فيلا راقية مع حديقة',
        'description' => 'فيلا فخمة مكونة من 5 غرف نوم مع حديقة واسعة ومسبح خاص. موقع هادئ ومميز.',
        'price' => 450000,
        'price_unit' => 'OMR',
        'location' => 'مسقط، القرم',
        'size' => 350,
        'size_unit' => 'sqm',
        'phone_number' => '23456789',
        'category' => 'villa',
        'status' => 'approved',
        'images' => json_encode([]),
    ],
    [
        'title' => 'أرض سكنية للبيع',
        'description' => 'أرض سكنية في موقع استراتيجي، مناسبة للبناء السكني أو التجاري. جميع الخدمات متوفرة.',
        'price' => 180000,
        'price_unit' => 'OMR',
        'location' => 'مسقط، بوشر',
        'size' => 500,
        'size_unit' => 'sqm',
        'phone_number' => '34567890',
        'category' => 'land',
        'status' => 'pending',
        'images' => json_encode([]),
    ],
    [
        'title' => 'محل تجاري في منطقة حيوية',
        'description' => 'محل تجاري بموقع ممتاز على شارع رئيسي، مناسب لجميع الأنشطة التجارية.',
        'price' => 120000,
        'price_unit' => 'OMR',
        'location' => 'مسقط، الخوير',
        'size' => 80,
        'size_unit' => 'sqm',
        'phone_number' => '45678901',
        'category' => 'commercial',
        'status' => 'approved',
        'images' => json_encode([]),
    ],
    [
        'title' => 'شقة عائلية واسعة',
        'description' => 'شقة مريحة مكونة من 4 غرف نوم مع شرفة كبيرة. قريبة من المدارس والمراكز التجارية.',
        'price' => 180000,
        'price_unit' => 'OMR',
        'location' => 'مسقط، الموالح',
        'size' => 180,
        'size_unit' => 'sqm',
        'phone_number' => '56789012',
        'category' => 'apartment',
        'status' => 'pending',
        'images' => json_encode([]),
    ],
    [
        'title' => 'فيلا دوبلكس حديثة',
        'description' => 'فيلا دوبلكس بتصميم عصري، 4 غرف نوم، مجلس، صالة، مطبخ مجهز، موقف سيارات.',
        'price' => 320000,
        'price_unit' => 'OMR',
        'location' => 'مسقط، السيب',
        'size' => 280,
        'size_unit' => 'sqm',
        'phone_number' => '12345678',
        'category' => 'villa',
        'status' => 'approved',
        'images' => json_encode([]),
    ],
    [
        'title' => 'شقة استوديو مفروشة',
        'description' => 'استوديو مفروش بالكامل، مناسب للعزاب أو الأزواج الجدد. جميع المرافق متوفرة.',
        'price' => 85000,
        'price_unit' => 'OMR',
        'location' => 'مسقط، الغبرة',
        'size' => 45,
        'size_unit' => 'sqm',
        'phone_number' => '23456789',
        'category' => 'apartment',
        'status' => 'rejected',
        'images' => json_encode([]),
    ],
    [
        'title' => 'أرض زراعية للاستثمار',
        'description' => 'أرض زراعية خصبة مع مصدر مياه، مناسبة للزراعة أو الاستثمار طويل الأجل.',
        'price' => 95000,
        'price_unit' => 'OMR',
        'location' => 'الباطنة، صحار',
        'size' => 1000,
        'size_unit' => 'sqm',
        'phone_number' => '34567890',
        'category' => 'land',
        'status' => 'pending',
        'images' => json_encode([]),
    ],
];

foreach ($properties as $index => $propertyData) {
    // Assign to random user
    $owner = $createdUsers[array_rand($createdUsers)];
    $propertyData['owner_id'] = $owner->id;
    
    $property = Property::create($propertyData);
    echo "  ✅ Created property: {$propertyData['title']}\n";
    echo "     Owner: {$owner->full_name}\n";
    echo "     Status: {$propertyData['status']}\n";
}

echo "\n===========================================\n";
echo "  SEEDING COMPLETE!\n";
echo "===========================================\n\n";

echo "Summary:\n";
echo "  👥 Users: " . count($users) . " created\n";
echo "  🏠 Properties: " . count($properties) . " created\n\n";

echo "Statistics:\n";
echo "  - Pending: " . collect($properties)->where('status', 'pending')->count() . "\n";
echo "  - Approved: " . collect($properties)->where('status', 'approved')->count() . "\n";
echo "  - Rejected: " . collect($properties)->where('status', 'rejected')->count() . "\n\n";

echo "Test User Credentials:\n";
echo "  Email: ahmed@example.com\n";
echo "  Password: password123\n\n";

echo "Admin Credentials:\n";
echo "  Email: admin@parfumes.com\n";
echo "  Password: Admin@123\n\n";

echo "===========================================\n";
echo "  READY TO TEST!\n";
echo "===========================================\n";
