# Task
Створити на Symfony 5+ сервіс, яки дозволяє через api отримувати/створювати/редагувати список користувачів у бд mysql/postgres
Сервіс повинен запускатись у докер контейнерах. Обов'язкові поля у бд: email, username, password. У списку користувачів повинен бути пошук по username і email.

#TODO
Серіалізувати юзера одним чином
Винести логіку з контролеру у хендлери

# Install
1. `docker-compose up -d`
2. `docker-compose exec backend bin/console doctrine:migration:migrate`
    * Connect to PostgreSQL database: host - `localhost`, port - `5433`, db - `test`, user - `test`, password - `test`
