# GenHuman Webman 框架 Dockerfile
FROM php:8.1-cli

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

# 複製應用代碼
COPY . .

# 創建必要的目錄並設置權限
RUN mkdir -p server/runtime/logs \
    && chmod -R 777 server/runtime

# 安裝 PHP 依賴
RUN cd server && composer install --no-dev --optimize-autoloader --no-interaction || true

# 暴露端口 8080 (對應 Zeabur 配置)
EXPOSE 8080

# 啟動 Webman 服務
WORKDIR /var/www/html/server
CMD ["php", "start.php", "start"]
