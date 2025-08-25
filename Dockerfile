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
    git \
    && docker-php-ext-install pdo_mysql zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# 安裝 Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 先複製 composer 文件
COPY server/composer.json server/composer.lock* ./server/

# 創建必要的目錄並設置權限
RUN mkdir -p server/runtime/logs \
    && chmod -R 777 server/runtime

# 安裝 PHP 依賴 (移除 || true 以確保安裝成功)
RUN cd server && composer install --no-dev --optimize-autoloader --no-interaction

# 複製其餘應用代碼
COPY . .

# 確保權限正確
RUN chmod -R 777 server/runtime

# 暴露端口 8080 (對應 Zeabur 配置)
EXPOSE 8080

# 啟動 Webman 服務
WORKDIR /var/www/html/server
CMD ["php", "start.php", "start"]
