-- 1. Create the database
CREATE DATABASE IF NOT EXISTS woldia_sgms;
USE woldia_sgms;

-- 2. users table (for login)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,  -- we will hash it later
    role ENUM('admin', 'instructor', 'student') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. departments table
CREATE TABLE departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dept_code VARCHAR(10) UNIQUE NOT NULL,  -- e.g., CS, EE
    dept_name VARCHAR(100) NOT NULL         -- e.g., Computer Science
);

-- 4. courses table
CREATE TABLE courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_code VARCHAR(20) UNIQUE NOT NULL,  -- e.g., ICT101
    course_name VARCHAR(100) NOT NULL,
    dept_id INT,
    credit_hours INT NOT NULL DEFAULT 3,
    semester INT NOT NULL,  -- 1 or 2
    FOREIGN KEY (dept_id) REFERENCES departments(id) ON DELETE SET NULL
);

-- 5. students table
CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20) UNIQUE NOT NULL,  -- e.g., WDU/001/15
    full_name VARCHAR(100) NOT NULL,
    dept_id INT,
    year INT NOT NULL,  -- 1,2,3,4,5
    user_id INT UNIQUE,
    FOREIGN KEY (dept_id) REFERENCES departments(id) ON DELETE SET NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 6. instructors table
CREATE TABLE instructors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    staff_id VARCHAR(20) UNIQUE NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    dept_id INT,
    user_id INT UNIQUE,
    FOREIGN KEY (dept_id) REFERENCES departments(id) ON DELETE SET NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 7. course_assignments (which instructor teaches which course)
CREATE TABLE course_assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT,
    instructor_id INT,
    academic_year VARCHAR(10) NOT NULL,  -- e.g., 2025-2026
    semester INT NOT NULL,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    FOREIGN KEY (instructor_id) REFERENCES instructors(id) ON DELETE CASCADE,
    UNIQUE(course_id, instructor_id, academic_year, semester)
);

-- 8. enrollments (which student takes which course)
CREATE TABLE enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT,
    course_id INT,
    academic_year VARCHAR(10) NOT NULL,
    semester INT NOT NULL,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    UNIQUE(student_id, course_id, academic_year, semester)
);

-- 9. grades table
CREATE TABLE grades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    enrollment_id INT,
    grade DECIMAL(4,2),           -- e.g., 85.50
    letter_grade VARCHAR(3),      -- e.g., A, B+
    grade_point DECIMAL(3,2),     -- e.g., 4.00
    FOREIGN KEY (enrollment_id) REFERENCES enrollments(id) ON DELETE CASCADE,
    UNIQUE(enrollment_id)
);

-- ========================================
-- SAMPLE DATA (for testing later)
-- ========================================

-- Default Admin
INSERT INTO users (username, password, role) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');  
-- Password is "password" (hashed)

-- Departments
INSERT INTO departments (dept_code, dept_name) VALUES
('CS', 'Computer Science'),
('EE', 'Electrical Engineering'),
('CE', 'Civil Engineering');

-- Sample Courses
INSERT INTO courses (course_code, course_name, dept_id, credit_hours, semester) VALUES
('ICT101', 'Introduction to Computer', 1, 3, 1),
('PROG101', 'Programming Fundamentals', 1, 4, 1),
('MATH101', 'Calculus I', 1, 3, 1),
('EE201', 'Circuit Analysis', 2, 3, 2);



ALTER TABLE users 
ADD COLUMN is_active TINYINT(1) DEFAULT 1 AFTER role,
ADD COLUMN last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

ALTER TABLE course_assignments 
ADD COLUMN status ENUM('open', 'submitted') NOT NULL DEFAULT 'open' AFTER semester;