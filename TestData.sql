USE StudyOrganiser;

-- Demo seed data for a fresh or existing StudyOrganiser database.
-- All demo accounts use the password: Test123!

-- Users
INSERT INTO `User` (`U_username`, `U_password`, `U_role`, `U_is_active`, `U_creation_date`)
VALUES
    ('admin', '$2y$12$DmjAwBHgH6K0wmBMH8Mu.u9iYp2pv/UgGdl6NYHpYvkHkL0WRWaj6', 'admin', 1, NOW() - INTERVAL 120 DAY),
    ('anna', '$2y$12$DmjAwBHgH6K0wmBMH8Mu.u9iYp2pv/UgGdl6NYHpYvkHkL0WRWaj6', 'user', 1, NOW() - INTERVAL 45 DAY),
    ('ben', '$2y$12$DmjAwBHgH6K0wmBMH8Mu.u9iYp2pv/UgGdl6NYHpYvkHkL0WRWaj6', 'user', 1, NOW() - INTERVAL 30 DAY),
    ('claire', '$2y$12$DmjAwBHgH6K0wmBMH8Mu.u9iYp2pv/UgGdl6NYHpYvkHkL0WRWaj6', 'user', 1, NOW() - INTERVAL 21 DAY),
    ('mr_smith', '$2y$12$DmjAwBHgH6K0wmBMH8Mu.u9iYp2pv/UgGdl6NYHpYvkHkL0WRWaj6', 'teacher', 1, NOW() - INTERVAL 300 DAY),
    ('ms_khan', '$2y$12$DmjAwBHgH6K0wmBMH8Mu.u9iYp2pv/UgGdl6NYHpYvkHkL0WRWaj6', 'teacher', 1, NOW() - INTERVAL 250 DAY),
    ('mr_brown', '$2y$12$DmjAwBHgH6K0wmBMH8Mu.u9iYp2pv/UgGdl6NYHpYvkHkL0WRWaj6', 'teacher', 0, NOW() - INTERVAL 180 DAY)
ON DUPLICATE KEY UPDATE
    `U_password` = VALUES(`U_password`),
    `U_role` = VALUES(`U_role`),
    `U_is_active` = VALUES(`U_is_active`),
    `U_creation_date` = VALUES(`U_creation_date`);

-- Subjects
INSERT INTO Subject (`S_name`)
VALUES
    ('Mathematics'),
    ('English'),
    ('Computer Science'),
    ('History')
ON DUPLICATE KEY UPDATE
    `S_name` = VALUES(`S_name`);

-- Teacher to subject assignments
INSERT INTO User_Subject (`U_ID`, `S_ID`)
SELECT u.`U_ID`, s.`S_ID`
FROM `User` u
JOIN Subject s ON s.`S_name` = 'Mathematics'
WHERE u.`U_username` = 'mr_smith'
AND NOT EXISTS (
    SELECT 1
    FROM User_Subject us
    WHERE us.`U_ID` = u.`U_ID`
      AND us.`S_ID` = s.`S_ID`
);

INSERT INTO User_Subject (`U_ID`, `S_ID`)
SELECT u.`U_ID`, s.`S_ID`
FROM `User` u
JOIN Subject s ON s.`S_name` = 'Computer Science'
WHERE u.`U_username` = 'mr_smith'
AND NOT EXISTS (
    SELECT 1
    FROM User_Subject us
    WHERE us.`U_ID` = u.`U_ID`
      AND us.`S_ID` = s.`S_ID`
);

INSERT INTO User_Subject (`U_ID`, `S_ID`)
SELECT u.`U_ID`, s.`S_ID`
FROM `User` u
JOIN Subject s ON s.`S_name` = 'English'
WHERE u.`U_username` = 'ms_khan'
AND NOT EXISTS (
    SELECT 1
    FROM User_Subject us
    WHERE us.`U_ID` = u.`U_ID`
      AND us.`S_ID` = s.`S_ID`
);

INSERT INTO User_Subject (`U_ID`, `S_ID`)
SELECT u.`U_ID`, s.`S_ID`
FROM `User` u
JOIN Subject s ON s.`S_name` = 'History'
WHERE u.`U_username` = 'ms_khan'
AND NOT EXISTS (
    SELECT 1
    FROM User_Subject us
    WHERE us.`U_ID` = u.`U_ID`
      AND us.`S_ID` = s.`S_ID`
);

INSERT INTO User_Subject (`U_ID`, `S_ID`)
SELECT u.`U_ID`, s.`S_ID`
FROM `User` u
JOIN Subject s ON s.`S_name` = 'Mathematics'
WHERE u.`U_username` = 'mr_brown'
AND NOT EXISTS (
    SELECT 1
    FROM User_Subject us
    WHERE us.`U_ID` = u.`U_ID`
      AND us.`S_ID` = s.`S_ID`
);

-- Homework for anna
INSERT INTO Homework (
    `U_ID`,
    `S_ID`,
    `Teacher_U_ID`,
    `title`,
    `description`,
    `due_at`,
    `is_done`,
    `created_at`,
    `updated_at`
)
SELECT
    student.`U_ID`,
    subject.`S_ID`,
    teacher.`U_ID`,
    'Complete quadratic equations worksheet',
    'Solve tasks 1 to 12 and write down the calculation steps for each result.',
    NOW() + INTERVAL 18 DAY,
    0,
    NOW() - INTERVAL 2 DAY,
    NOW() - INTERVAL 2 DAY
FROM `User` student
JOIN Subject subject ON subject.`S_name` = 'Mathematics'
JOIN `User` teacher ON teacher.`U_username` = 'mr_smith'
WHERE student.`U_username` = 'anna'
AND EXISTS (
    SELECT 1
    FROM User_Subject us
    WHERE us.`U_ID` = teacher.`U_ID`
      AND us.`S_ID` = subject.`S_ID`
)
AND NOT EXISTS (
    SELECT 1
    FROM Homework h
    WHERE h.`U_ID` = student.`U_ID`
      AND h.`title` = 'Complete quadratic equations worksheet'
);

INSERT INTO Homework (
    `U_ID`,
    `S_ID`,
    `Teacher_U_ID`,
    `title`,
    `description`,
    `due_at`,
    `is_done`,
    `created_at`,
    `updated_at`
)
SELECT
    student.`U_ID`,
    subject.`S_ID`,
    teacher.`U_ID`,
    'Prepare SQL normalization notes',
    'Summarize first, second and third normal form with one example per rule.',
    NOW() + INTERVAL 5 DAY,
    0,
    NOW() - INTERVAL 1 DAY,
    NOW() - INTERVAL 1 DAY
FROM `User` student
JOIN Subject subject ON subject.`S_name` = 'Computer Science'
JOIN `User` teacher ON teacher.`U_username` = 'mr_smith'
WHERE student.`U_username` = 'anna'
AND EXISTS (
    SELECT 1
    FROM User_Subject us
    WHERE us.`U_ID` = teacher.`U_ID`
      AND us.`S_ID` = subject.`S_ID`
)
AND NOT EXISTS (
    SELECT 1
    FROM Homework h
    WHERE h.`U_ID` = student.`U_ID`
      AND h.`title` = 'Prepare SQL normalization notes'
);

INSERT INTO Homework (
    `U_ID`,
    `S_ID`,
    `Teacher_U_ID`,
    `title`,
    `description`,
    `due_at`,
    `is_done`,
    `created_at`,
    `updated_at`
)
SELECT
    student.`U_ID`,
    subject.`S_ID`,
    teacher.`U_ID`,
    'Read chapter 4 and summarize',
    'Read the text and write a one page summary of the main argument.',
    NOW() + INTERVAL 20 HOUR,
    0,
    NOW() - INTERVAL 3 DAY,
    NOW() - INTERVAL 3 DAY
FROM `User` student
JOIN Subject subject ON subject.`S_name` = 'English'
JOIN `User` teacher ON teacher.`U_username` = 'ms_khan'
WHERE student.`U_username` = 'anna'
AND EXISTS (
    SELECT 1
    FROM User_Subject us
    WHERE us.`U_ID` = teacher.`U_ID`
      AND us.`S_ID` = subject.`S_ID`
)
AND NOT EXISTS (
    SELECT 1
    FROM Homework h
    WHERE h.`U_ID` = student.`U_ID`
      AND h.`title` = 'Read chapter 4 and summarize'
);

INSERT INTO Homework (
    `U_ID`,
    `S_ID`,
    `Teacher_U_ID`,
    `title`,
    `description`,
    `due_at`,
    `is_done`,
    `created_at`,
    `updated_at`
)
SELECT
    student.`U_ID`,
    subject.`S_ID`,
    teacher.`U_ID`,
    'Finish sources comparison table',
    'Compare two historical sources and add a short reliability note for both.',
    NOW() - INTERVAL 2 DAY,
    1,
    NOW() - INTERVAL 10 DAY,
    NOW() - INTERVAL 1 DAY
FROM `User` student
JOIN Subject subject ON subject.`S_name` = 'History'
JOIN `User` teacher ON teacher.`U_username` = 'ms_khan'
WHERE student.`U_username` = 'anna'
AND EXISTS (
    SELECT 1
    FROM User_Subject us
    WHERE us.`U_ID` = teacher.`U_ID`
      AND us.`S_ID` = subject.`S_ID`
)
AND NOT EXISTS (
    SELECT 1
    FROM Homework h
    WHERE h.`U_ID` = student.`U_ID`
      AND h.`title` = 'Finish sources comparison table'
);

-- Homework for ben
INSERT INTO Homework (
    `U_ID`,
    `S_ID`,
    `Teacher_U_ID`,
    `title`,
    `description`,
    `due_at`,
    `is_done`,
    `created_at`,
    `updated_at`
)
SELECT
    student.`U_ID`,
    subject.`S_ID`,
    teacher.`U_ID`,
    'Practice linear functions',
    'Complete the worksheet on slope-intercept form and check the graph sketches.',
    NOW() + INTERVAL 9 DAY,
    0,
    NOW() - INTERVAL 4 DAY,
    NOW() - INTERVAL 4 DAY
FROM `User` student
JOIN Subject subject ON subject.`S_name` = 'Mathematics'
JOIN `User` teacher ON teacher.`U_username` = 'mr_smith'
WHERE student.`U_username` = 'ben'
AND EXISTS (
    SELECT 1
    FROM User_Subject us
    WHERE us.`U_ID` = teacher.`U_ID`
      AND us.`S_ID` = subject.`S_ID`
)
AND NOT EXISTS (
    SELECT 1
    FROM Homework h
    WHERE h.`U_ID` = student.`U_ID`
      AND h.`title` = 'Practice linear functions'
);

INSERT INTO Homework (
    `U_ID`,
    `S_ID`,
    `Teacher_U_ID`,
    `title`,
    `description`,
    `due_at`,
    `is_done`,
    `created_at`,
    `updated_at`
)
SELECT
    student.`U_ID`,
    subject.`S_ID`,
    teacher.`U_ID`,
    'Vocabulary list revision',
    'Revise the unit vocabulary and prepare five example sentences.',
    NOW() + INTERVAL 3 DAY,
    1,
    NOW() - INTERVAL 6 DAY,
    NOW() - INTERVAL 1 DAY
FROM `User` student
JOIN Subject subject ON subject.`S_name` = 'English'
JOIN `User` teacher ON teacher.`U_username` = 'ms_khan'
WHERE student.`U_username` = 'ben'
AND EXISTS (
    SELECT 1
    FROM User_Subject us
    WHERE us.`U_ID` = teacher.`U_ID`
      AND us.`S_ID` = subject.`S_ID`
)
AND NOT EXISTS (
    SELECT 1
    FROM Homework h
    WHERE h.`U_ID` = student.`U_ID`
      AND h.`title` = 'Vocabulary list revision'
);

-- Homework for claire
INSERT INTO Homework (
    `U_ID`,
    `S_ID`,
    `Teacher_U_ID`,
    `title`,
    `description`,
    `due_at`,
    `is_done`,
    `created_at`,
    `updated_at`
)
SELECT
    student.`U_ID`,
    subject.`S_ID`,
    teacher.`U_ID`,
    'Build ER diagram draft',
    'Create the first ER diagram draft for the organiser database including relations.',
    NOW() + INTERVAL 12 DAY,
    0,
    NOW() - INTERVAL 2 DAY,
    NOW() - INTERVAL 2 DAY
FROM `User` student
JOIN Subject subject ON subject.`S_name` = 'Computer Science'
JOIN `User` teacher ON teacher.`U_username` = 'mr_smith'
WHERE student.`U_username` = 'claire'
AND EXISTS (
    SELECT 1
    FROM User_Subject us
    WHERE us.`U_ID` = teacher.`U_ID`
      AND us.`S_ID` = subject.`S_ID`
)
AND NOT EXISTS (
    SELECT 1
    FROM Homework h
    WHERE h.`U_ID` = student.`U_ID`
      AND h.`title` = 'Build ER diagram draft'
);

INSERT INTO Homework (
    `U_ID`,
    `S_ID`,
    `Teacher_U_ID`,
    `title`,
    `description`,
    `due_at`,
    `is_done`,
    `created_at`,
    `updated_at`
)
SELECT
    student.`U_ID`,
    subject.`S_ID`,
    teacher.`U_ID`,
    'Timeline poster outline',
    'Prepare the outline and collect at least six dated events for the poster.',
    NOW() + INTERVAL 36 HOUR,
    0,
    NOW() - INTERVAL 5 DAY,
    NOW() - INTERVAL 5 DAY
FROM `User` student
JOIN Subject subject ON subject.`S_name` = 'History'
JOIN `User` teacher ON teacher.`U_username` = 'ms_khan'
WHERE student.`U_username` = 'claire'
AND EXISTS (
    SELECT 1
    FROM User_Subject us
    WHERE us.`U_ID` = teacher.`U_ID`
      AND us.`S_ID` = subject.`S_ID`
)
AND NOT EXISTS (
    SELECT 1
    FROM Homework h
    WHERE h.`U_ID` = student.`U_ID`
      AND h.`title` = 'Timeline poster outline'
);
