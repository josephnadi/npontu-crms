
#!/bin/bash
php artisan migrate --force --seed
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan storage:link
php-fpm
