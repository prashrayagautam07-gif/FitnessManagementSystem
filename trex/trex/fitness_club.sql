CREATE DATABASE IF NOT EXISTS fitness_club;
USE fitness_club;

CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

INSERT INTO admins (username, password)
VALUES ('admin', MD5('admin123'));

CREATE TABLE IF NOT EXISTS members (
    member_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20) NOT NULL UNIQUE,
    membership_type VARCHAR(50) NOT NULL,
    start_date DATE NOT NULL,
    expiry_date DATE NOT NULL,
    status VARCHAR(20) NOT NULL
);

INSERT INTO members
(name, email, phone, membership_type, start_date, expiry_date, status)
VALUES
('Prashraya Gautam', 'prashraya@gmail.com', '9842095443', 'Monthly', '2026-02-01', '2026-03-01', 'Active'),
('Amit Sharma', 'amit.sharma@gmail.com', '9812345678', 'Quarterly', '2026-01-15', '2026-04-15', 'Active'),
('Suman Thapa', 'suman.thapa@gmail.com', '9801122334', 'Yearly', '2025-12-01', '2026-12-01', 'Active'),
('Rohit Singh', 'rohit.singh@gmail.com', '9823456789', 'Monthly', '2026-01-20', '2026-02-20', 'Active'),
('Anita Karki', 'anita.karki@gmail.com', '9866778899', 'Quarterly', '2025-11-10', '2026-02-10', 'Active');

CREATE TABLE IF NOT EXISTS trainers (
    trainer_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    specialization VARCHAR(100),
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20) NOT NULL UNIQUE
);

INSERT INTO trainers
(name, specialization, email, phone)
VALUES
('Ramesh Adhikari', 'Strength Training', 'ramesh.trainer@gmail.com', '9809988776'),
('Bikash Rai', 'Cardio & Fat Loss', 'bikash.rai@gmail.com', '9811122233'),
('Sanjay Gurung', 'CrossFit', 'sanjay.gurung@gmail.com', '9822233344'),
('Nisha Shrestha', 'Yoga & Flexibility', 'nisha.yoga@gmail.com', '9845566778'),
('Kiran Lama', 'Personal Training', 'kiran.lama@gmail.com', '9856677889');

CREATE TABLE IF NOT EXISTS workout_plans (
    plan_id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT NOT NULL,
    trainer_id INT NOT NULL,
    workout_date DATE NOT NULL,
    exercise_name VARCHAR(100) NOT NULL,
    sets INT DEFAULT 0,
    reps INT DEFAULT 0,
    FOREIGN KEY (member_id) REFERENCES members(member_id) ON DELETE CASCADE,
    FOREIGN KEY (trainer_id) REFERENCES trainers(trainer_id) ON DELETE CASCADE
);

INSERT INTO workout_plans
(member_id, trainer_id, workout_date, exercise_name, sets, reps)
VALUES
(1, 1, '2026-02-01', 'Bench Press', 4, 10),
(2, 2, '2026-02-02', 'Treadmill Running', 3, 15),
(3, 3, '2026-02-03', 'Deadlift', 5, 5),
(4, 1, '2026-02-04', 'Squats', 4, 12),
(5, 4, '2026-02-05', 'Yoga Stretching', 3, 8);


