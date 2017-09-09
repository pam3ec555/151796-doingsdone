# Запись проектов в таблицу проекты
INSERT INTO projects SET project = "Входящие";
INSERT INTO projects SET project = "Учеба";
INSERT INTO projects SET project = "Работа";
INSERT INTO projects SET project = "Домашние дела";
INSERT INTO projects SET project = "Авто";

# Чтение проектов с таблицы проектов
SELECT * FROM projects;

# Запись пользователей из таблицы пользователей
INSERT INTO users SET
  email = "ignat.v@gmail.com",
  name = "Игнат",
  password = "$2y$10$OqvsKHQwr0Wk6FMZDoHo1uHoXd4UdxJG/5UDtUiie00XaxMHrW8ka";
INSERT INTO users SET
  email = "kitty_93@li.ru",
  name = "Леночка",
  password = "$2y$10$bWtSjUhwgggtxrnJ7rxmIe63ABubHQs0AS0hgnOo41IEdMHkYoSVa";
INSERT INTO users SET
  email = "warrior07@mail.ru",
  name = "Руслан",
  password = "$2y$10$2OxpEH7narYpkOT1H5cApezuzh10tZEEQ2axgFOaKW.55LxIJBgWW";

# Чтение пользователей из таблицы пользователей
SELECT * FROM users