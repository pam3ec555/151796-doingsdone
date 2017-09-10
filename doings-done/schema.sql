CREATE DATABASE doingsdone;

USE doingsdone;

CREATE TABLE projects (
  id INT AUTO_INCREMENT PRIMARY KEY,
  project CHAR(64)
);

CREATE TABLE tasks (
  id INT AUTO_INCREMENT PRIMARY KEY,
  date_create DATETIME,
  date_complete DATETIME,
  name CHAR(64),
  file CHAR,
  deadline DATETIME,
  project_id INT,
  author_id INT
);

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  date_of_registration DATETIME,
  email CHAR(32) UNIQUE ,
  name CHAR(32),
  password CHAR(64),
  contacts CHAR
);