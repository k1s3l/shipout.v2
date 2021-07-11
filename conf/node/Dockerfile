FROM node:carbon

# Создать директорию app
WORKDIR /app

# Установить зависимости приложения
# Используется символ подстановки для копирования как package.json, так и package-lock.json,
# работает с npm@5+
COPY package*.json ./
COPY server.js ./

RUN npm install
# Используется при сборке кода в продакшене
# RUN npm install --only=production

CMD [ "node", "server.js" ]