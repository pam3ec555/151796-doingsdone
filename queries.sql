# Запись проектов в таблицу проекты
INSERT INTO projects SET project = "Входящие", author_id = 1, is_delete = 0;
INSERT INTO projects SET project = "Учеба", author_id = 1, is_delete = 0;
INSERT INTO projects SET project = "Работа", author_id = 2, is_delete = 0;
INSERT INTO projects SET project = "Домашние дела", author_id = 2, is_delete = 0;
INSERT INTO projects SET project = "Авто", author_id = 3, is_delete = 0;

# Чтение проектов с таблицы проектов
SELECT * FROM projects;

# Запись пользователей из таблицы пользователей
INSERT INTO users SET
  email = "ignat.v@gmail.com",
  name = "Игнат",
  password = "$2y$10$OqvsKHQwr0Wk6FMZDoHo1uHoXd4UdxJG/5UDtUiie00XaxMHrW8ka",
  date_of_registration = now();
INSERT INTO users SET
  email = "kitty_93@li.ru",
  name = "Леночка",
  password = "$2y$10$bWtSjUhwgggtxrnJ7rxmIe63ABubHQs0AS0hgnOo41IEdMHkYoSVa",
  date_of_registration = now();
INSERT INTO users SET
  email = "warrior07@mail.ru",
  name = "Руслан",
  password = "$2y$10$2OxpEH7narYpkOT1H5cApezuzh10tZEEQ2axgFOaKW.55LxIJBgWW",
  date_of_registration = now();

# Чтение пользователей из таблицы пользователей
SELECT * FROM users;

# Запись задач в таблицу задач
INSERT INTO tasks SET
  date_create = now(),
  deadline = "2018-06-01",
  task = "Собеседование в IT компании",
  project_id = 3,
  author_id = 1,
  is_complete = 0,
  is_delete = 0;

# Чтение задач из таблицы задач
SELECT * FROM tasks;

# связал две таблицы "tasks" & "users" по author_id
SELECT * FROM users u JOIN tasks t ON t.author_id = u.id;

# связал две таблицы "tasks" & "projects" по project_id
SELECT * FROM projects p JOIN tasks t ON t.project_id = p.id;

#-----------------------------------------------------------

# получил список из проектов для одного пользователя
SELECT project
FROM projects p
JOIN tasks t
ON t.project_id = p.id
WHERE t.author_id = 1
GROUP BY project;

# получил список из задач для одного пользователя
SELECT
  t.task,
  t.date_complete,
  t.date_create,
  t.file,
  t.deadline,
  p.project
FROM tasks t
JOIN projects p
ON t.project_id = p.id
WHERE t.author_id = 2;

# Обновляем значение поля date_complete, тем самым показывая, что задача была выполнена
UPDATE tasks t
SET date_complete = "2017-09-10", is_complete = 1
WHERE t.id = 6;

# Проверяем результат предыдущего действия
SELECT * FROM tasks;

# Устанавливаю дедлайн задачи, так как он был пуст
UPDATE tasks t
SET t.deadline = "2017-09-11"
WHERE t.id < 4;

# Вывожу список задач на завтра, если сегодня 2017-09-10
SELECT * FROM tasks t WHERE t.deadline = "2017-09-11";

# Меняю название задачи по id
UPDATE tasks t
SET t.task = "Пойти погулять"
WHERE t.id = 3;

# Проверяем результат предыдущего действия
SELECT * FROM tasks t WHERE t.id = 3;