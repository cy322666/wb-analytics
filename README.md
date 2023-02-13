Установка:

1) Оформить .env
2) docker-compose up --build 
3) Внутри app контейнера создать рута командой php artisan make:filament-user
4) Авторизоваться http://127.0.0.1:6060/admin/login
5) Создать тестовую локальную бд из админки
--- 
Пример выгрузки: php artisan wb:orders {id account}

Ссылка на дебагер: http://127.0.0.1:6060/telescope/requests

