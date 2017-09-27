CREATE DATABASE doingsdone;

USE doingsdone;

CREATE TABLE projects (
  id INT AUTO_INCREMENT PRIMARY KEY,
  project CHAR(64),
  author_id INT,
  is_delete BOOLEAN
);

CREATE TABLE tasks (
  id INT AUTO_INCREMENT PRIMARY KEY,
  date_create DATETIME,
  date_complete DATETIME,
  task CHAR(64),
  file_url TEXT,
  file_name CHAR(64),
  deadline DATETIME,
  project_id INT,
  author_id INT,
  is_complete BOOLEAN,
  is_delete BOOLEAN
);

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  date_of_registration DATETIME,
  email CHAR(32) UNIQUE,
  name CHAR(32),
  password CHAR(64),
  contacts CHAR
);