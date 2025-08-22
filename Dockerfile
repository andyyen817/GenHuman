# 簡化的 Zeabur GenHuman Dockerfile
FROM php:8.1-apache

# 設置工作目錄
WORKDIR /var/www/html

# 安裝必要的系統依賴和 PHP 擴展
RUN apt-get update && apt-get install -y \
    curl \
    libpng-dev \
    libzip-dev \
    unzip \
    && docker-php-ext-install pdo_mysql \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# 安裝 Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 啟用 Apache rewrite 模組
RUN a2enmod rewrite
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# 複製應用代碼
COPY . .

# 創建必要的目錄並設置權限
RUN mkdir -p server/runtime/logs \
    && chmod -R 777 server/runtime \
    && chown -R www-data:www-data /var/www/html

# 安裝 PHP 依賴
RUN cd server && composer install --no-dev --optimize-autoloader --no-interaction || true

# 配置 Apache 虛擬主機
RUN echo '<VirtualHost *:80>\n\
    DocumentRoot /var/www/html/server/public\n\
    <Directory /var/www/html/server/public>\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# 暴露端口 80
EXPOSE 80

# 啟動 Apache
CMD ["apache2-foreground"]
