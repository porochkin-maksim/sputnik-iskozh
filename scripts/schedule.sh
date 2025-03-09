#!/bin/bash

# Запуск cron для выполнения запланированных задач
# echo "* * * * * root php /var/www/html/artisan schedule:run >> /var/www/html/storage/logs/cron.log 2>&1" | crontab -

#!/bin/bash

php artisan schedule:clear-cache

while [ true ]
do
 php /var/www/html/artisan schedule:run &
 sleep 60
done
