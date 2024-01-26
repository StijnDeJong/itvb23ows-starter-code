FROM mysql:5.5.42
WORKDIR /app
COPY . /app
COPY . /docker-entrypoint-initdb.d
EXPOSE 3000
CMD ["php", "-S", "0.0.0.0:3000"]