-- Classroom Feedback System: run this in phpMyAdmin
SET NAMES utf8mb4;
DROP DATABASE IF EXISTS feedback_db;
CREATE DATABASE feedback_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE feedback_db;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(80) NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('student', 'teacher') NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE classes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    teacher_id INT NOT NULL,
    name VARCHAR(80) NOT NULL,
    join_code CHAR(6) NOT NULL UNIQUE,
    FOREIGN KEY (teacher_id) REFERENCES users(id)
);

CREATE TABLE enrolments (
    class_id INT NOT NULL,
    student_id INT NOT NULL,
    PRIMARY KEY (class_id, student_id),
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES users(id)
);

CREATE TABLE rounds (
    id INT AUTO_INCREMENT PRIMARY KEY,
    class_id INT NOT NULL,
    title VARCHAR(80) NOT NULL,
    status ENUM('draft', 'open', 'closed') NOT NULL DEFAULT 'draft',
    closes_at DATETIME NOT NULL,
    eligible INT NULL,
    reply TEXT NULL,
    replied_at DATETIME NULL,
    share_averages TINYINT NOT NULL DEFAULT 0,
    FOREIGN KEY (class_id) REFERENCES classes(id)
);

CREATE TABLE questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    round_id INT NOT NULL,
    text VARCHAR(200) NOT NULL,
    type ENUM('rating', 'comment') NOT NULL,
    FOREIGN KEY (round_id) REFERENCES rounds(id) ON DELETE CASCADE
);

-- Who answered. No time and no running number, so the order of answering isn't kept.
CREATE TABLE participations (
    round_id INT NOT NULL,
    student_id INT NOT NULL,
    PRIMARY KEY (round_id, student_id),
    FOREIGN KEY (round_id) REFERENCES rounds(id),
    FOREIGN KEY (student_id) REFERENCES users(id)
);

-- What was answered. No student id and no time. The id is random, so row order means nothing.
CREATE TABLE answers (
    id INT PRIMARY KEY,
    round_id INT NOT NULL,
    question_id INT NOT NULL,
    rating TINYINT NULL,
    comment TEXT NULL,
    hidden TINYINT NOT NULL DEFAULT 0,
    FOREIGN KEY (round_id) REFERENCES rounds(id),
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
);


-- Demo data. Every password is password123

INSERT INTO users (name, email, password, role) VALUES
('Sarah Lim', 'sarah@demo.test', '$2y$10$9CbdtE0OztKxEqlv.7Ygk.Oa6Hwd2W299SQGCps9tMb4KwdxRa482', 'teacher'),
('David Mensah', 'david@demo.test', '$2y$10$9CbdtE0OztKxEqlv.7Ygk.Oa6Hwd2W299SQGCps9tMb4KwdxRa482', 'teacher'),
('Priya Nair', 'priya@demo.test', '$2y$10$9CbdtE0OztKxEqlv.7Ygk.Oa6Hwd2W299SQGCps9tMb4KwdxRa482', 'teacher'),
('Aisha Rahman', 'aisha@demo.test', '$2y$10$9CbdtE0OztKxEqlv.7Ygk.Oa6Hwd2W299SQGCps9tMb4KwdxRa482', 'student'),
('Kavin Selvam', 'kavin@demo.test', '$2y$10$9CbdtE0OztKxEqlv.7Ygk.Oa6Hwd2W299SQGCps9tMb4KwdxRa482', 'student'),
('Nimali Perera', 'nimali@demo.test', '$2y$10$9CbdtE0OztKxEqlv.7Ygk.Oa6Hwd2W299SQGCps9tMb4KwdxRa482', 'student'),
('Daniel Okafor', 'daniel@demo.test', '$2y$10$9CbdtE0OztKxEqlv.7Ygk.Oa6Hwd2W299SQGCps9tMb4KwdxRa482', 'student'),
('Maria Santos', 'maria@demo.test', '$2y$10$9CbdtE0OztKxEqlv.7Ygk.Oa6Hwd2W299SQGCps9tMb4KwdxRa482', 'student'),
('Liam Chen', 'liam@demo.test', '$2y$10$9CbdtE0OztKxEqlv.7Ygk.Oa6Hwd2W299SQGCps9tMb4KwdxRa482', 'student'),
('Fathima Nazeer', 'fathima@demo.test', '$2y$10$9CbdtE0OztKxEqlv.7Ygk.Oa6Hwd2W299SQGCps9tMb4KwdxRa482', 'student'),
('Arjun Mehta', 'arjun@demo.test', '$2y$10$9CbdtE0OztKxEqlv.7Ygk.Oa6Hwd2W299SQGCps9tMb4KwdxRa482', 'student'),
('Tom Reid', 'tom@demo.test', '$2y$10$9CbdtE0OztKxEqlv.7Ygk.Oa6Hwd2W299SQGCps9tMb4KwdxRa482', 'student');

INSERT INTO classes (teacher_id, name, join_code) VALUES
(1, 'Web Development, Group A', 'WEBDEV'),
(1, 'Database Systems', 'DATABS'),
(1, 'Project Seminar', 'PROJEC'),
(2, 'Networking Basics', 'NETWRK');

INSERT INTO enrolments (class_id, student_id) VALUES (1, 4), (1, 5), (1, 6), (1, 7), (1, 8), (1, 9), (1, 10), (1, 11), (2, 4), (2, 5), (2, 6), (2, 7), (2, 8), (2, 9), (3, 4), (3, 5), (3, 6), (4, 4), (4, 5), (4, 7), (4, 8), (4, 11);
