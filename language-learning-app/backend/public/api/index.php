<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Database connection
$host = '127.0.0.1';
$db = 'duolingo';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed: ' . $e->getMessage()]);
    exit();
}

// Get request method and path
$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['REQUEST_URI'];
$path = parse_url($path, PHP_URL_PATH);
$path = str_replace('/parfumes/language-learning-app/backend/public/api', '', $path);
$segments = array_filter(explode('/', $path));
$segments = array_values($segments);

// Get request body
$input = json_decode(file_get_contents('php://input'), true) ?? [];

// Helper function to send JSON response
function sendResponse($data, $code = 200) {
    http_response_code($code);
    echo json_encode($data);
    exit();
}

// Helper function to get auth token
function getAuthToken() {
    $headers = getallheaders();
    if (isset($headers['Authorization'])) {
        return str_replace('Bearer ', '', $headers['Authorization']);
    }
    return null;
}

// Helper function to verify token and get user
function getAuthUser($pdo, $token) {
    if (!$token) return null;
    
    $stmt = $pdo->prepare("SELECT u.* FROM users u 
                           INNER JOIN personal_access_tokens t ON u.id = t.tokenable_id 
                           WHERE t.token = ? AND t.tokenable_type = 'App\\\\Models\\\\User'");
    $stmt->execute([hash('sha256', $token)]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Routes
try {
    // Root API endpoint - show available endpoints
    if ($method === 'GET' && count($segments) === 0) {
        sendResponse([
            'success' => true,
            'message' => 'LinguaLearn API',
            'version' => '1.0',
            'endpoints' => [
                'GET /courses' => 'Get all courses',
                'GET /courses/{id}' => 'Get course by ID',
                'POST /login' => 'Login with email/password',
                'POST /register' => 'Register new account',
                'POST /guest-login' => 'Create guest account',
                'GET /me' => 'Get current user (requires auth)',
                'GET /lessons/{id}/exercises' => 'Get lesson exercises',
                'POST /exercises/{id}/submit' => 'Submit exercise answer',
                'POST /lessons/{id}/complete' => 'Complete lesson'
            ]
        ]);
    }
    
    // GET /courses
    if ($method === 'GET' && isset($segments[0]) && $segments[0] === 'courses' && count($segments) === 1) {
        $stmt = $pdo->query("SELECT c.*, 
                             (SELECT COUNT(*) FROM lessons WHERE course_id = c.id) as lessons_count 
                             FROM courses c 
                             WHERE is_active = 1 
                             ORDER BY `order`");
        $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get lessons for each course
        foreach ($courses as &$course) {
            $stmt = $pdo->prepare("SELECT * FROM lessons WHERE course_id = ? ORDER BY `order`");
            $stmt->execute([$course['id']]);
            $course['lessons'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        sendResponse(['success' => true, 'data' => $courses]);
    }
    
    // GET /courses/{id}
    if ($method === 'GET' && isset($segments[0]) && $segments[0] === 'courses' && count($segments) === 2) {
        $courseId = $segments[1];
        $stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
        $stmt->execute([$courseId]);
        $course = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($course) {
            $stmt = $pdo->prepare("SELECT * FROM lessons WHERE course_id = ? ORDER BY `order`");
            $stmt->execute([$courseId]);
            $course['lessons'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            sendResponse(['success' => true, 'course' => $course]);
        } else {
            sendResponse(['success' => false, 'message' => 'Course not found'], 404);
        }
    }
    
    // POST /guest-login
    if ($method === 'POST' && isset($segments[0]) && $segments[0] === 'guest-login') {
        $guestToken = bin2hex(random_bytes(16));
        $name = 'Guest_' . substr($guestToken, 0, 6);
        $email = 'guest_' . time() . '@temp.com';
        $password = password_hash(bin2hex(random_bytes(16)), PASSWORD_BCRYPT);
        
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, is_guest, guest_token, created_at, updated_at) 
                               VALUES (?, ?, ?, 'user', 1, ?, NOW(), NOW())");
        $stmt->execute([$name, $email, $password, $guestToken]);
        $userId = $pdo->lastInsertId();
        
        $token = bin2hex(random_bytes(32));
        $stmt = $pdo->prepare("INSERT INTO personal_access_tokens (tokenable_type, tokenable_id, name, token, abilities, created_at, updated_at) 
                               VALUES ('App\\\\Models\\\\User', ?, 'guest_token', ?, '[\"*\"]', NOW(), NOW())");
        $stmt->execute([$userId, hash('sha256', $token)]);
        
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        sendResponse([
            'success' => true,
            'user' => $user,
            'token' => $token,
            'guest_token' => $guestToken,
            'message' => 'Guest account created'
        ], 201);
    }
    
    // POST /login
    if ($method === 'POST' && isset($segments[0]) && $segments[0] === 'login') {
        $email = $input['email'] ?? '';
        $password = $input['password'] ?? '';
        
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            $token = bin2hex(random_bytes(32));
            $stmt = $pdo->prepare("INSERT INTO personal_access_tokens (tokenable_type, tokenable_id, name, token, abilities, created_at, updated_at) 
                                   VALUES ('App\\\\Models\\\\User', ?, 'auth_token', ?, '[\"*\"]', NOW(), NOW())");
            $stmt->execute([$user['id'], hash('sha256', $token)]);
            
            sendResponse([
                'success' => true,
                'user' => $user,
                'token' => $token,
                'message' => 'Login successful'
            ]);
        } else {
            sendResponse(['success' => false, 'message' => 'Invalid credentials'], 401);
        }
    }
    
    // POST /register
    if ($method === 'POST' && isset($segments[0]) && $segments[0] === 'register') {
        $name = $input['name'] ?? '';
        $email = $input['email'] ?? '';
        $password = $input['password'] ?? '';
        
        // Check if email exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            sendResponse(['success' => false, 'message' => 'Email already exists'], 422);
        }
        
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, is_guest, created_at, updated_at) 
                               VALUES (?, ?, ?, 'user', 0, NOW(), NOW())");
        $stmt->execute([$name, $email, $hashedPassword]);
        $userId = $pdo->lastInsertId();
        
        $token = bin2hex(random_bytes(32));
        $stmt = $pdo->prepare("INSERT INTO personal_access_tokens (tokenable_type, tokenable_id, name, token, abilities, created_at, updated_at) 
                               VALUES ('App\\\\Models\\\\User', ?, 'auth_token', ?, '[\"*\"]', NOW(), NOW())");
        $stmt->execute([$userId, hash('sha256', $token)]);
        
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        sendResponse([
            'success' => true,
            'user' => $user,
            'token' => $token,
            'message' => 'Registration successful'
        ], 201);
    }
    
    // GET /lessons/{id}/exercises
    if ($method === 'GET' && isset($segments[0]) && $segments[0] === 'lessons' && isset($segments[2]) && $segments[2] === 'exercises') {
        $lessonId = $segments[1];
        $stmt = $pdo->prepare("SELECT * FROM exercises WHERE lesson_id = ? ORDER BY `order`");
        $stmt->execute([$lessonId]);
        $exercises = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Parse JSON options
        foreach ($exercises as &$exercise) {
            if ($exercise['options']) {
                $exercise['options'] = json_decode($exercise['options']);
            }
        }
        
        sendResponse(['success' => true, 'exercises' => $exercises]);
    }
    
    // POST /exercises/{id}/submit
    if ($method === 'POST' && isset($segments[0]) && $segments[0] === 'exercises' && isset($segments[2]) && $segments[2] === 'submit') {
        $exerciseId = $segments[1];
        $answer = $input['answer'] ?? '';
        
        $stmt = $pdo->prepare("SELECT * FROM exercises WHERE id = ?");
        $stmt->execute([$exerciseId]);
        $exercise = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($exercise) {
            $isCorrect = strtolower(trim($answer)) === strtolower(trim($exercise['correct_answer']));
            
            sendResponse([
                'success' => true,
                'is_correct' => $isCorrect,
                'correct_answer' => $isCorrect ? null : $exercise['correct_answer'],
                'explanation' => $exercise['explanation'],
                'xp_earned' => $isCorrect ? $exercise['xp_reward'] : 0
            ]);
        } else {
            sendResponse(['success' => false, 'message' => 'Exercise not found'], 404);
        }
    }
    
    // POST /lessons/{id}/complete
    if ($method === 'POST' && isset($segments[0]) && $segments[0] === 'lessons' && isset($segments[2]) && $segments[2] === 'complete') {
        $lessonId = $segments[1];
        $token = getAuthToken();
        $user = getAuthUser($pdo, $token);
        
        if (!$user) {
            sendResponse(['success' => false, 'message' => 'Unauthorized'], 401);
        }
        
        $stmt = $pdo->prepare("SELECT * FROM lessons WHERE id = ?");
        $stmt->execute([$lessonId]);
        $lesson = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($lesson) {
            // Update user XP
            $stmt = $pdo->prepare("UPDATE users SET total_xp = total_xp + ? WHERE id = ?");
            $stmt->execute([$lesson['xp_reward'], $user['id']]);
            
            sendResponse([
                'success' => true,
                'xp_earned' => $lesson['xp_reward'],
                'message' => 'Lesson completed!'
            ]);
        } else {
            sendResponse(['success' => false, 'message' => 'Lesson not found'], 404);
        }
    }
    
    // GET /me
    if ($method === 'GET' && isset($segments[0]) && $segments[0] === 'me') {
        $token = getAuthToken();
        $user = getAuthUser($pdo, $token);
        
        if ($user) {
            sendResponse(['success' => true, 'user' => $user]);
        } else {
            sendResponse(['success' => false, 'message' => 'Unauthorized'], 401);
        }
    }
    
    // GET /admin/courses (for admin panel)
    if ($method === 'GET' && isset($segments[0]) && $segments[0] === 'admin' && isset($segments[1]) && $segments[1] === 'courses') {
        $stmt = $pdo->query("SELECT c.*, 
                             (SELECT COUNT(*) FROM lessons WHERE course_id = c.id) as lessons_count 
                             FROM courses c 
                             ORDER BY `order`");
        $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
        sendResponse(['success' => true, 'courses' => $courses]);
    }
    
    // Default 404
    sendResponse(['success' => false, 'message' => 'Endpoint not found', 'path' => $path], 404);
    
} catch (Exception $e) {
    sendResponse(['success' => false, 'error' => $e->getMessage()], 500);
}
