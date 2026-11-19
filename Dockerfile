FROM php:8.2-cli
RUN docker-php-ext-install pdo_sqlite
WORKDIR /app
COPY . /app
# ডেমোর index.html সরিয়ে দেওয়া — যাতে PHP সার্ভার আসল index.php দেখায়
RUN rm -f /app/index.html
EXPOSE 8080
CMD ["sh", "-c", "php -S 0.0.0.0:${PORT:-8080} -t /app"]
