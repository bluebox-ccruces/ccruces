# ðŸš€ GuÃ­a de Deployment - CCruces V2

## Requisitos del VPS

- Ubuntu 22.04 LTS
- 1 GB RAM mÃ­nimo (2 GB recomendado)
- PHP 8.2+
- MySQL 8.0+
- Nginx
- Composer
- Node.js 20+
- Git

## 1. Preparar el Servidor

```bash
# Actualizar el sistema
sudo apt update && sudo apt upgrade -y

# Instalar dependencias bÃ¡sicas
sudo apt install -y software-properties-common curl git unzip

# Agregar repositorio de PHP 8.2
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

# Instalar PHP 8.2 y extensiones
sudo apt install -y php8.2-fpm php8.2-mysql php8.2-xml php8.2-mbstring \
    php8.2-curl php8.2-zip php8.2-bcmath php8.2-gd php8.2-intl

# Instalar MySQL
sudo apt install -y mysql-server
sudo mysql_secure_installation

# Instalar Nginx
sudo apt install -y nginx

# Instalar Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Instalar Node.js 20
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs
```

## 2. Crear Base de Datos

```bash
sudo mysql -u root -p
```

```sql
CREATE DATABASE ccruces_v2 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'ccruces_user'@'localhost' IDENTIFIED BY 'TU_CONTRASEÃ‘A_SEGURA';
GRANT ALL PRIVILEGES ON ccruces_v2.* TO 'ccruces_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

## 3. Clonar el Proyecto

```bash
# Crear directorio del proyecto
sudo mkdir -p /var/www
cd /var/www

# Clonar repositorio
sudo git clone https://github.com/Cercik/CCruces.git ccruces-v2
cd ccruces-v2

# Dar permisos al usuario www-data
sudo chown -R www-data:www-data /var/www/ccruces-v2
sudo chmod -R 755 /var/www/ccruces-v2
```

## 4. Instalar Dependencias

```bash
cd /var/www/ccruces-v2

# Instalar dependencias PHP
composer install --no-dev --optimize-autoloader

# Instalar dependencias Node.js
npm install

# Compilar assets para producciÃ³n
npm run build
```

## 5. Configurar el Entorno

```bash
# Copiar archivo de configuraciÃ³n
cp .env.example .env

# Editar configuraciÃ³n
nano .env
```

**Configurar en .env:**
```env
APP_NAME="CCruces V2"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tu-dominio.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ccruces_v2
DB_USERNAME=ccruces_user
DB_PASSWORD=TU_CONTRASEÃ‘A_SEGURA
```

```bash
# Generar application key
php artisan key:generate

# Ejecutar migraciones
php artisan migrate --force

# Cachear configuraciones
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Permisos correctos
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache
```

## 6. Configurar Nginx

```bash
sudo nano /etc/nginx/sites-available/ccruces-v2
```

**Contenido del archivo:**
```nginx
server {
    listen 80;
    server_name tu-dominio.com www.tu-dominio.com;
    root /var/www/ccruces-v2/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

```bash
# Habilitar sitio
sudo ln -s /etc/nginx/sites-available/ccruces-v2 /etc/nginx/sites-enabled/

# Deshabilitar sitio por defecto
sudo rm /etc/nginx/sites-enabled/default

# Verificar configuraciÃ³n
sudo nginx -t

# Reiniciar Nginx
sudo systemctl restart nginx
```

## 7. Configurar SSL con Let's Encrypt

```bash
# Instalar Certbot
sudo apt install -y certbot python3-certbot-nginx

# Obtener certificado SSL
sudo certbot --nginx -d tu-dominio.com -d www.tu-dominio.com

# RenovaciÃ³n automÃ¡tica (ya estÃ¡ configurada)
sudo certbot renew --dry-run
```

## 8. Optimizaciones de PHP

```bash
sudo nano /etc/php/8.2/fpm/php.ini
```

**Configuraciones recomendadas:**
```ini
memory_limit = 256M
upload_max_filesize = 20M
post_max_size = 25M
max_execution_time = 60
```

```bash
# Reiniciar PHP-FPM
sudo systemctl restart php8.2-fpm
```

## 9. Configurar Firewall

```bash
sudo ufw allow OpenSSH
sudo ufw allow 'Nginx Full'
sudo ufw enable
sudo ufw status
```

## 10. Configurar Cron para Tareas Programadas

```bash
sudo crontab -e -u www-data
```

**Agregar:**
```cron
* * * * * cd /var/www/ccruces-v2 && php artisan schedule:run >> /dev/null 2>&1
```

## 11. Configurar Logs

```bash
# Rotar logs de Laravel
sudo nano /etc/logrotate.d/laravel
```

```
/var/www/ccruces-v2/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    notifempty
    create 0640 www-data www-data
    sharedscripts
}
```

## 12. Actualizaciones Futuras

```bash
cd /var/www/ccruces-v2

# Modo mantenimiento
php artisan down

# Actualizar cÃ³digo
git pull origin main

# Actualizar dependencias
composer install --no-dev --optimize-autoloader
npm install && npm run build

# Limpiar cachÃ©s
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Recrear cachÃ©s
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Ejecutar migraciones nuevas
php artisan migrate --force

# Salir de mantenimiento
php artisan up
```

## 13. VerificaciÃ³n Final

```bash
# Estado de servicios
sudo systemctl status nginx
sudo systemctl status php8.2-fpm
sudo systemctl status mysql

# Ver logs en tiempo real
tail -f /var/www/ccruces-v2/storage/logs/laravel.log
tail -f /var/log/nginx/error.log
```

## ðŸ” Seguridad Adicional

1. **Cambiar usuario de MySQL:**
   ```bash
   # No usar 'root' en producciÃ³n
   ```

2. **Configurar fail2ban:**
   ```bash
   sudo apt install fail2ban
   sudo systemctl enable fail2ban
   ```

3. **Deshabilitar acceso directo a .env:**
   Ya estÃ¡ protegido por Nginx, pero verifica que no sea accesible desde el navegador.

4. **Backups automÃ¡ticos:**
   ```bash
   # Script de backup (ejemplo bÃ¡sico)
   #!/bin/bash
   mysqldump -u ccruces_user -p ccruces_v2 > /backups/db-$(date +%Y%m%d).sql
   tar -czf /backups/files-$(date +%Y%m%d).tar.gz /var/www/ccruces-v2
   ```

## ðŸ“Š Monitoreo

- Logs de Laravel: `/var/www/ccruces-v2/storage/logs/`
- Logs de Nginx: `/var/log/nginx/`
- Logs de PHP: `/var/log/php8.2-fpm.log`

## ðŸ†˜ Troubleshooting

### Error 500
```bash
# Ver logs
tail -f storage/logs/laravel.log

# Verificar permisos
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache
```

### Error de Base de Datos
```bash
# Verificar conexiÃ³n
php artisan tinker
>>> DB::connection()->getPdo();
```

### CSS/JS no carga
```bash
# Recompilar assets
npm run build

# Limpiar cachÃ© de navegador
# Verificar que public/build/ exista
```

## ðŸ“ž Soporte

Para problemas especÃ­ficos del proyecto:
- GitHub: https://github.com/Cercik/CCruces
- Issues: https://github.com/Cercik/CCruces/issues

---

**Ãšltima actualizaciÃ³n:** Diciembre 2025
**VersiÃ³n:** 2.0
