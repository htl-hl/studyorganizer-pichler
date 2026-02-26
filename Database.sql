DROP DATABASE IF EXISTS StudyOrganiser;
CREATE DATABASE StudyOrganiser;
USE StudyOrganiser;

CREATE TABLE `User` (
	U_ID INT PRIMARY KEY AUTO_INCREMENT,
	U_username VARCHAR(255) NOT NULL UNIQUE,
	U_password VARCHAR(255) NOT NULL,
	U_role ENUM('user', 'admin', 'teacher') NOT NULL,
	U_is_active BOOLEAN NOT NULL,
	U_creation_date DATETIME NOT NULL
);

CREATE TABLE Subject (
	S_ID INT PRIMARY KEY AUTO_INCREMENT,
    S_name VARCHAR(255) NOT NULL UNIQUE
);

CREATE TABLE User_Subject (
    U_ID INT NOT NULL,
    S_ID INT NOT NULL,
    PRIMARY KEY (U_ID, S_ID),
    KEY idx_user_subject_sid (S_ID),
    CONSTRAINT fk_user_subject_user FOREIGN KEY (U_ID) REFERENCES `User`(U_ID),
    CONSTRAINT fk_user_subject_subject FOREIGN KEY (S_ID) REFERENCES Subject(S_ID)
);

CREATE TABLE Homework (
    H_ID INT PRIMARY KEY AUTO_INCREMENT,
    U_ID INT NOT NULL,
    S_ID INT NOT NULL,
    Teacher_U_ID INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    due_at DATETIME NOT NULL,
    is_done BOOLEAN NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    KEY idx_homework_owner (U_ID),
    KEY idx_homework_subject (S_ID),
    KEY idx_homework_teacher_subject (Teacher_U_ID, S_ID),
    CONSTRAINT fk_homework_user FOREIGN KEY (U_ID) REFERENCES `User`(U_ID),
    CONSTRAINT fk_homework_subject FOREIGN KEY (S_ID) REFERENCES Subject(S_ID),
    CONSTRAINT fk_homework_teacher_subject FOREIGN KEY (Teacher_U_ID, S_ID) REFERENCES User_Subject(U_ID, S_ID)
);
