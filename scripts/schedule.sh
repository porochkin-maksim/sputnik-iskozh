#!/bin/bash

# Запуск cron для выполнения запланированных задач
# echo "* * * * * root php /var/www/html/artisan schedule:run >> /var/www/html/storage/logs/cron.log 2>&1" | crontab -

#!/bin/bash

# Очистка кэша планировщика
php artisan schedule:clear-cache

# Создание директории для логов, если её нет
mkdir -p /var/www/html/storage/logs

# Запуск планировщика в бесконечном цикле
while [ true ]
do
    php artisan schedule:run >> /var/www/html/storage/logs/schedule.log 2>&1
    sleep 60
done
