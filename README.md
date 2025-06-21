AmoSendler ⏱️ → 💼

Лендинг‑форма, которая за 1 клик создаёт сделку и контакт в AmoCRM (Kommo)
плюс автоматически помечает, был ли посетитель на странице больше 30 секунд.



✨ Возможности

Функция

Что делает

HTML‑форма

Имя, E‑mail, Телефон, Цена

Таймер

Без JS на клиенте — время вычисляется в PHP

API v4

Создание сделки и контакта одним запросом /leads/complex

Отметка больше 30 сек

Записывается в кастомное поле‑флаг

Авто‑refresh токена

При 401 скрипт запрашивает новый access_token через refresh_token

Alert’ы

Успех / Ошибка без перезагрузки страницы

📂 Структура проекта

/public_html/
└── Название проекта/
    ├─ index.php      # страница‑форма
    ├─ lead.php       # обработчик, работа с API AmoCRM
    └── style.css     # (необязательно) ваш стиль

⚙️ Требования

PHP 7.4+ (расширение curl включено)

Любой тип хостинга (shared/VPS)

Домен, с которого будет обращаться форма

Учётка AmoCRM / Kommo с правами администратора

🚀 Установка

Клонируйте репозиторий или скопируйте каталог AmoSendler в папку сайта.

Создайте Внешнюю интеграцию в AmoCRM:

сохраните client_id, client_secret;

нажмите «Установить» → скопируйте authorization_code.

Обменяйте код на access_token и refresh_token:

curl -X POST https://<SUBDOMAIN>.amocrm.ru/oauth2/access_token \
     -H 'Content-Type: application/json' \
     -d '{
           "client_id":     "...",
           "client_secret": "...",
           "grant_type":    "authorization_code",
           "code":          "AUTH_CODE",
           "redirect_uri":  "https://example.com/oauth"
         }'

В Карточке сделки создайте чек‑бокс (или текстовое) поле:

название — «На сайте больше 30 сек»;

запомните ID (например 338991).

Откройте lead.php и заполните константы:

const SUBDOMAIN     = 'mycompany';          // без .amocrm.ru
const ACCESS_TOKEN  = 'xxxxxxxx';
const REFRESH_TOKEN = 'yyyyyyyy';
const TIME_FIELD_ID = 338991;               // ID поля‑флага

Загрузите файлы на сервер. Готово!

🧑‍💻 Использование

Перейдите в браузере на index.php.

Заполните форму.

Если посетитель был на странице > 30 сек → в новой сделке поле‑флаг = true.

Все статусы выводятся модальными alert().

🔐 Безопасность

Токены хранятся только на сервере и никогда не попадают в браузер.

Скрипт автоматически обновляет access_token, записывая его в access_token.tmp.

Для боевого продакшена рекомендуется вынести токены в .env + закрыть доступ к каталогу AmoSendler по .htaccess.

📝 Лицензия

JQuBiiK © 2025 — Feel free to fork & contribute ✌️

