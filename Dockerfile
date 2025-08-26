# GenHuman Webman 框架 Dockerfile - 緊急修復版本
FROM php:8.1-cli

# 設置工作目錄
WORKDIR /var/www/html

# 安裝必要的系統依賴和 PHP 擴展
RUN apt-get update && apt-get install -y \
    curl \
    libpng-dev \
    libzip-dev \
    unzip \
    git \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libonig-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql zip bcmath gd mbstring pcntl \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# 安裝 Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 複製所有應用代碼
COPY . .

# 創建必要的目錄並設置權限
RUN mkdir -p server/runtime/logs \
    && chmod -R 777 server/runtime

# 安裝 PHP 依賴
RUN cd server && composer install --no-dev --optimize-autoloader --no-interaction

# 暴露端口 8080
EXPOSE 8080

# 簡化啟動命令 - 不使用守護進程模式
WORKDIR /var/www/html/server
CMD ["php", "start.php", "start"]