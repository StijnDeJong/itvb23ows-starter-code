FROM php
WORKDIR /app
COPY . /app
EXPOSE 3000
RUN docker-php-ext-install mysqli
RUN docker-php-ext-enable mysqli
CMD ["php", "-S", "0.0.0.0:3000"]
