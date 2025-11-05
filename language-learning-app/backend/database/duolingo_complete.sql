-- ============================================
-- DUOLINGO-STYLE LANGUAGE LEARNING DATABASE
-- Complete Schema with Mock Data
-- ============================================

-- Create Database
CREATE DATABASE IF NOT EXISTS duolingo CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE duolingo;

-- Drop existing tables
DROP TABLE IF EXISTS user_achievements;
DROP TABLE IF EXISTS achievements;
DROP TABLE IF EXISTS user_progress;
DROP TABLE IF EXISTS exercises;
DROP TABLE IF EXISTS lessons;
DROP TABLE IF EXISTS courses;
DROP TABLE IF EXISTS users;

-- ============================================
-- TABLE: users
-- ============================================
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    avatar VARCHAR(255) NULL,
    total_xp INT DEFAULT 0,
    current_streak INT DEFAULT 0,
    longest_streak INT DEFAULT 0,
    last_practice_date DATE NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    is_guest BOOLEAN DEFAULT FALSE,
    guest_token VARCHAR(255) NULL UNIQUE,
    email_verified_at TIMESTAMP NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    INDEX idx_email (email),
    INDEX idx_guest_token (guest_token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: courses
-- ============================================
CREATE TABLE courses (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    language_code VARCHAR(10) NOT NULL,
    flag_icon VARCHAR(255) NULL,
    description TEXT NULL,
    difficulty ENUM('beginner', 'intermediate', 'advanced') DEFAULT 'beginner',
    color_primary VARCHAR(7) DEFAULT '#58CC02',
    color_secondary VARCHAR(7) DEFAULT '#89E219',
    total_xp INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    `order` INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    INDEX idx_language (language_code),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: lessons
-- ============================================
CREATE TABLE lessons (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    course_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    difficulty ENUM('beginner', 'intermediate', 'advanced') DEFAULT 'beginner',
    xp_reward INT DEFAULT 10,
    `order` INT DEFAULT 0,
    icon VARCHAR(255) NULL,
    is_locked BOOLEAN DEFAULT FALSE,
    unlock_after_lesson_id BIGINT UNSIGNED NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    FOREIGN KEY (unlock_after_lesson_id) REFERENCES lessons(id) ON DELETE SET NULL,
    INDEX idx_course (course_id),
    INDEX idx_order (`order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: exercises
-- ============================================
CREATE TABLE exercises (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    lesson_id BIGINT UNSIGNED NOT NULL,
    type ENUM('multiple_choice', 'translate', 'speak', 'listen', 'match_pairs', 'fill_blank', 'word_order') NOT NULL,
    question TEXT NOT NULL,
    question_audio VARCHAR(255) NULL,
    options JSON NULL,
    correct_answer TEXT NOT NULL,
    correct_audio VARCHAR(255) NULL,
    explanation TEXT NULL,
    xp_reward INT DEFAULT 5,
    `order` INT DEFAULT 0,
    image VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (lesson_id) REFERENCES lessons(id) ON DELETE CASCADE,
    INDEX idx_lesson (lesson_id),
    INDEX idx_type (type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: user_progress
-- ============================================
CREATE TABLE user_progress (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    course_id BIGINT UNSIGNED NOT NULL,
    lesson_id BIGINT UNSIGNED NULL,
    exercise_id BIGINT UNSIGNED NULL,
    status ENUM('not_started', 'in_progress', 'completed') DEFAULT 'not_started',
    xp_earned INT DEFAULT 0,
    attempts INT DEFAULT 0,
    correct_answers INT DEFAULT 0,
    total_questions INT DEFAULT 0,
    accuracy DECIMAL(5,2) DEFAULT 0.00,
    started_at TIMESTAMP NULL,
    completed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    FOREIGN KEY (lesson_id) REFERENCES lessons(id) ON DELETE CASCADE,
    FOREIGN KEY (exercise_id) REFERENCES exercises(id) ON DELETE CASCADE,
    UNIQUE KEY unique_progress (user_id, lesson_id, exercise_id),
    INDEX idx_user (user_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: achievements
-- ============================================
CREATE TABLE achievements (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    icon VARCHAR(255) NOT NULL,
    xp_requirement INT DEFAULT 0,
    streak_requirement INT DEFAULT 0,
    badge_color VARCHAR(7) DEFAULT '#FFD700',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: user_achievements
-- ============================================
CREATE TABLE user_achievements (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    achievement_id BIGINT UNSIGNED NOT NULL,
    earned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (achievement_id) REFERENCES achievements(id) ON DELETE CASCADE,
    UNIQUE KEY unique_achievement (user_id, achievement_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- MOCK DATA: Users
-- ============================================
INSERT INTO users (name, email, password, role, total_xp, current_streak, longest_streak, is_guest) VALUES
('Admin User', 'admin@duolingo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1500, 15, 30, FALSE),
('John Doe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 450, 7, 12, FALSE),
('Jane Smith', 'jane@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 280, 3, 8, FALSE);
-- Password for all: "password"

-- ============================================
-- MOCK DATA: Courses
-- ============================================
INSERT INTO courses (name, language_code, flag_icon, description, difficulty, color_primary, color_secondary, total_xp, is_active, `order`) VALUES
('English for Beginners', 'en', '🇬🇧', 'Learn English from scratch with fun interactive lessons', 'beginner', '#58CC02', '#89E219', 500, TRUE, 1),
('Spanish Basics', 'es', '🇪🇸', 'Start your Spanish journey with essential vocabulary and grammar', 'beginner', '#FF9600', '#FFB800', 450, TRUE, 2),
('French Fundamentals', 'fr', '🇫🇷', 'Master French basics with engaging exercises', 'beginner', '#CE82FF', '#E5B8FF', 400, TRUE, 3),
('German Essentials', 'de', '🇩🇪', 'Build a strong foundation in German language', 'intermediate', '#1CB0F6', '#4FC3F7', 550, TRUE, 4),
('Arabic for Beginners', 'ar', '🇸🇦', 'Learn Arabic alphabet, pronunciation and basic phrases', 'beginner', '#FF4B4B', '#FF6B6B', 480, TRUE, 5);

-- ============================================
-- MOCK DATA: Lessons (English Course)
-- ============================================
INSERT INTO lessons (course_id, title, description, difficulty, xp_reward, `order`, icon, is_locked) VALUES
-- English Course Lessons
(1, 'Greetings & Introductions', 'Learn how to greet people and introduce yourself', 'beginner', 10, 1, '👋', FALSE),
(1, 'Numbers 1-20', 'Master counting from 1 to 20', 'beginner', 10, 2, '🔢', FALSE),
(1, 'Colors & Shapes', 'Learn basic colors and shapes', 'beginner', 10, 3, '🎨', FALSE),
(1, 'Family Members', 'Vocabulary for family relationships', 'beginner', 15, 4, '👨‍👩‍👧‍👦', FALSE),
(1, 'Food & Drinks', 'Common food and beverage vocabulary', 'beginner', 15, 5, '🍕', FALSE),

-- Spanish Course Lessons
(2, 'Hola! Basic Greetings', 'Essential Spanish greetings', 'beginner', 10, 1, '👋', FALSE),
(2, 'Números 1-10', 'Count to 10 in Spanish', 'beginner', 10, 2, '🔢', FALSE),
(2, 'Colores', 'Learn colors in Spanish', 'beginner', 10, 3, '🌈', FALSE),

-- French Course Lessons
(3, 'Bonjour! Salutations', 'French greetings and politeness', 'beginner', 10, 1, '🇫🇷', FALSE),
(3, 'Les Nombres', 'French numbers 1-20', 'beginner', 10, 2, '🔢', FALSE),

-- German Course Lessons
(4, 'Guten Tag! Grüße', 'German greetings', 'intermediate', 15, 1, '🇩🇪', FALSE),

-- Arabic Course Lessons
(5, 'Arabic Alphabet', 'Learn the Arabic alphabet', 'beginner', 20, 1, '🔤', FALSE);

-- ============================================
-- MOCK DATA: Exercises (English - Lesson 1)
-- ============================================
INSERT INTO exercises (lesson_id, type, question, options, correct_answer, explanation, xp_reward, `order`) VALUES
-- Lesson 1: Greetings
(1, 'multiple_choice', 'How do you say "Hello" in English?', 
 '["Hello", "Goodbye", "Thank you", "Please"]', 
 'Hello', 
 'Hello is the most common greeting in English', 
 5, 1),

(1, 'multiple_choice', 'What is the correct response to "How are you?"', 
 '["I am fine, thank you", "Goodbye", "Hello", "Yes"]', 
 'I am fine, thank you', 
 'This is a polite and common response', 
 5, 2),

(1, 'translate', 'Translate to English: مرحبا (Arabic for Hello)', 
 NULL, 
 'Hello', 
 'مرحبا means Hello in Arabic', 
 5, 3),

(1, 'fill_blank', 'Complete: "Nice to ___ you"', 
 NULL, 
 'meet', 
 'The phrase is "Nice to meet you"', 
 5, 4),

-- Lesson 2: Numbers
(2, 'multiple_choice', 'What number comes after 5?', 
 '["4", "6", "7", "5"]', 
 '6', 
 'The sequence is 5, 6, 7...', 
 5, 1),

(2, 'translate', 'Write the number: Ten', 
 NULL, 
 '10', 
 'Ten is written as 10', 
 5, 2),

(2, 'multiple_choice', 'How do you write "fifteen"?', 
 '["15", "50", "5", "14"]', 
 '15', 
 'Fifteen is 15', 
 5, 3),

-- Lesson 3: Colors
(3, 'multiple_choice', 'What color is the sky?', 
 '["Blue", "Red", "Green", "Yellow"]', 
 'Blue', 
 'The sky is typically blue', 
 5, 1),

(3, 'multiple_choice', 'What color is grass?', 
 '["Green", "Blue", "Red", "Purple"]', 
 'Green', 
 'Grass is green', 
 5, 2),

-- Lesson 4: Family
(4, 'multiple_choice', 'Your mother\'s mother is your ___', 
 '["Grandmother", "Aunt", "Sister", "Cousin"]', 
 'Grandmother', 
 'Your mother\'s mother is your grandmother', 
 5, 1),

(4, 'translate', 'Translate: Father', 
 NULL, 
 'Dad', 
 'Father and Dad mean the same', 
 5, 2),

-- Lesson 5: Food
(5, 'multiple_choice', 'Which one is a fruit?', 
 '["Apple", "Bread", "Cheese", "Rice"]', 
 'Apple', 
 'Apple is a fruit', 
 5, 1),

(5, 'multiple_choice', 'What do you drink?', 
 '["Water", "Pizza", "Burger", "Salad"]', 
 'Water', 
 'Water is a drink', 
 5, 2);

-- ============================================
-- MOCK DATA: Spanish Exercises
-- ============================================
INSERT INTO exercises (lesson_id, type, question, options, correct_answer, explanation, xp_reward, `order`) VALUES
(6, 'multiple_choice', '¿Cómo se dice "Hello" en español?', 
 '["Hola", "Adiós", "Gracias", "Por favor"]', 
 'Hola', 
 'Hola means Hello in Spanish', 
 5, 1),

(6, 'translate', 'Translate: Good morning', 
 NULL, 
 'Buenos días', 
 'Buenos días means Good morning', 
 5, 2),

(7, 'multiple_choice', '¿Cuánto es uno más uno?', 
 '["Dos", "Tres", "Uno", "Cuatro"]', 
 'Dos', 
 '1 + 1 = 2 (dos)', 
 5, 1);

-- ============================================
-- MOCK DATA: Achievements
-- ============================================
INSERT INTO achievements (name, description, icon, xp_requirement, streak_requirement, badge_color) VALUES
('First Steps', 'Complete your first lesson', '🎯', 10, 0, '#58CC02'),
('Week Warrior', 'Maintain a 7-day streak', '🔥', 0, 7, '#FF9600'),
('Century Club', 'Earn 100 XP', '💯', 100, 0, '#FFD700'),
('Dedicated Learner', 'Maintain a 30-day streak', '🏆', 0, 30, '#CE82FF'),
('XP Master', 'Earn 1000 XP', '⚡', 1000, 0, '#1CB0F6'),
('Perfect Score', 'Get 100% on any lesson', '✨', 0, 0, '#FF4B4B');

-- ============================================
-- MOCK DATA: User Progress
-- ============================================
INSERT INTO user_progress (user_id, course_id, lesson_id, exercise_id, status, xp_earned, attempts, correct_answers, total_questions, accuracy, completed_at) VALUES
(2, 1, 1, 1, 'completed', 5, 1, 1, 1, 100.00, NOW()),
(2, 1, 1, 2, 'completed', 5, 1, 1, 1, 100.00, NOW()),
(2, 1, 2, NULL, 'in_progress', 0, 0, 0, 0, 0.00, NULL),
(3, 1, 1, NULL, 'completed', 10, 1, 4, 4, 100.00, NOW());

-- ============================================
-- MOCK DATA: User Achievements
-- ============================================
INSERT INTO user_achievements (user_id, achievement_id, earned_at) VALUES
(2, 1, NOW()),
(3, 1, NOW());

-- ============================================
-- CREATE INDEXES FOR PERFORMANCE
-- ============================================
ALTER TABLE users ADD INDEX idx_total_xp (total_xp);
ALTER TABLE courses ADD INDEX idx_order (`order`);
ALTER TABLE lessons ADD INDEX idx_course_order (course_id, `order`);
ALTER TABLE exercises ADD INDEX idx_lesson_order (lesson_id, `order`);

-- ============================================
-- SUMMARY
-- ============================================
SELECT 'Database setup complete!' AS status;
SELECT COUNT(*) AS total_users FROM users;
SELECT COUNT(*) AS total_courses FROM courses;
SELECT COUNT(*) AS total_lessons FROM lessons;
SELECT COUNT(*) AS total_exercises FROM exercises;
SELECT COUNT(*) AS total_achievements FROM achievements;
