FROM alpine:3.10

LABEL maintainer="Vincent Composieux <vincent.composieux@gmail.com>"

RUN apk add --update --no-cache nginx

COPY nginx.conf /etc/nginx/

COPY symfony.conf /etc/nginx/conf.d/
COPY symfony.conf /etc/nginx/sites-enable/
COPY upstream.conf /etc/nginx/conf.d/

RUN adduser -D -g '' -G www-data www-data

CMD ["nginx"]

EXPOSE 80
EXPOSE 443
