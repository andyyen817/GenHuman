# Zeabur 優化的 GenHuman Dockerfile
FROM php:8.1-apache

# 設置工作目錄
WORKDIR /var/www/html

# 安裝系統依賴和 PHP 擴展
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    supervisor \
    && docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath gd \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# 安裝 Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 啟用 Apache 模組
RUN a2enmod rewrite proxy proxy_http
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# 複製應用代碼
COPY . .

# 安裝 PHP 依賴
RUN cd server && composer install --optimize-autoloader --no-dev --no-interaction

# Apache 虛擬主機配置
RUN echo '<VirtualHost *:8080>\n\
    DocumentRoot /var/www/html/server/public\n\
    ServerName localhost\n\
    \n\
    <Directory /var/www/html/server/public>\n\
        Options Indexes FollowSymLinks\n\
        AllowOverride All\n\
        Require all granted\n\
        \n\
        # URL 重寫規則\n\
        RewriteEngine On\n\
        RewriteCond %{REQUEST_FILENAME} !-f\n\
        RewriteCond %{REQUEST_FILENAME} !-d\n\
        RewriteRule ^(.*)$ index.php [QSA,L]\n\
    </Directory>\n\
    \n\
    # 靜態資源緩存\n\
    <LocationMatch "\\.(css|js|png|jpg|jpeg|gif|ico|svg)$">\n\
        ExpiresActive On\n\
        ExpiresDefault "access plus 1 month"\n\
        Header append Cache-Control "public"\n\
    </LocationMatch>\n\
    \n\
    # API 代理到 Webman\n\
    ProxyPreserveHost On\n\
    ProxyPass /api/ http://127.0.0.1:8787/\n\
    ProxyPassReverse /api/ http://127.0.0.1:8787/\n\
    \n\
    # 錯誤和訪問日誌\n\
    ErrorLog ${APACHE_LOG_DIR}/error.log\n\
    CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# 更新 Apache 端口配置
RUN sed -i 's/Listen 80/Listen 8080/' /etc/apache2/ports.conf

# 設置權限
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 777 /var/www/html/server/runtime

# Supervisor 配置
RUN echo '[supervisord]\n\
nodaemon=true\n\
user=root\n\
\n\
[program:apache2]\n\
command=/usr/sbin/apache2ctl -D FOREGROUND\n\
stdout_logfile=/dev/stdout\n\
stdout_logfile_maxbytes=0\n\
stderr_logfile=/dev/stderr\n\
stderr_logfile_maxbytes=0\n\
\n\
[program:webman]\n\
command=php /var/www/html/server/start.php start\n\
directory=/var/www/html/server\n\
stdout_logfile=/dev/stdout\n\
stdout_logfile_maxbytes=0\n\
stderr_logfile=/dev/stderr\n\
stderr_logfile_maxbytes=0\n\
autorestart=true\n\
user=www-data' > /etc/supervisor/conf.d/supervisord.conf

# 健康檢查腳本
RUN echo '#!/bin/bash\n\
curl -f http://localhost:8080/ || exit 1' > /healthcheck.sh && chmod +x /healthcheck.sh

# 暴露端口
EXPOSE 8080

# 設置健康檢查
HEALTHCHECK --interval=30s --timeout=10s --start-period=60s --retries=3 \
    CMD /healthcheck.sh

# 啟動 Supervisor
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
