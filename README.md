Instalación: composer install cp .env.example .env php artisan key:generate php artisan migrate --seed npm install EN SHELL COMO ADMINISTRADOR: Get-ExecutionPolicy Set-ExecutionPolicy -Scope CurrentUser -ExecutionPolicy RemoteSigned

Para iniciar: npm run dev php artisan serve

para migraciones: php artisan migrate:fresh --seed

para ruta nueva creada o repo clonado: php artisan route:clear php artisan route:cache

Intrucciones para produccion: composer install --optimize-autoloader --no-dev npm install && npm run build contenido de storage/app/public

Para evitar Laravel\SerializableClosure\Exceptions\InvalidSignatureException:
php artisan config:clear
php artisan config:cache
php artisan route:clear
php artisan route:cache