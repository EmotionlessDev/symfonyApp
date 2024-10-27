# docker/nginx/Dockerfile
FROM nginx:latest

# Копируем конфигурацию Nginx в контейнер
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/nginx/conf.d/default.conf /etc/nginx/conf.d/default.conf

# Копируем проект в контейнер
WORKDIR /var/www
COPY . /var/www

# Устанавливаем права на файлы
RUN chmod -R 755 /var/www