FROM node:16-alpine

ARG UID

RUN npm install -g nodemon

RUN apk add nano bash
# Установить зависимости приложения
# Используется символ подстановки для копирования как package.json, так и package-lock.json,
# работает с npm@5+
COPY conf/node/package*.json ./

RUN npm install
# Используется при сборке кода в продакшене
# RUN npm install --only=production
COPY node ./

RUN cat /etc/sysctl.conf

RUN deluser --remove-home node \
  && addgroup -S www-data -g ${UID} \
  && adduser -S -G www-data -u ${UID} www-data

USER ${UID}:${UID}

CMD [ "nodemon", "/usr/src/app/server.js" ]