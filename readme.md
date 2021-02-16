# Install
1. `docker-compose up -d`
2. `docker-compose exec backend bin/console doctrine:migration:migrate`
2a. Connect to PostgreSQL database: host - `localhost`, port - `5433`, db - `test`, user - `test`, password - `test`