# Task
Создать на Symfony 5+ приложение, которое позволяет через api просматривать/создавать/редактировать список пользователей в бд mysql/postgress
Приложение должно запускаться в docker контейнерах. Обязательные поля в бд: email, username, password. В списке пользователей должен быть поиск по username и emai

# Install
1. `docker-compose up -d`
2. `docker-compose exec backend bin/console doctrine:migration:migrate`
2a. Connect to PostgreSQL database: host - `localhost`, port - `5433`, db - `test`, user - `test`, password - `test`