# Справочник команд

[Содержание документации](README.md)

Запускайте из корня Symfony в доверенном терминале. Примеры используют dev. Не помещайте пароли, ключи и коды восстановления в аргументы, журналы или Git.

## Поиск команд и справка


```bash
php bin/console list guardian --env=dev
php bin/console help guardian:setup --env=dev
php bin/console guardian:doctor --help --env=dev
```


--env=dev и --no-interaction принадлежат Symfony. --no-interaction не предоставляет секреты и не подходит для интерактивных паролей. --help показывает синтаксис; -v подробности. Код 0 подтверждает заявленный результат; --no-admin может завершиться без работающего портала.

## `vendor/bin/guardian`

Включает bundle в dev/test, очищает кеш, запускает guardian:setup --automatic, снова очищает кеш, затем запускает guardian:doctor и guardian:access. Останавливается при первой ошибке. Повтор сохраняет учётные записи и ключи. Отклоняет APP_ENV=prod, требует proc_open.

Без аргументов, с --help, -h или help показывает справку без изменений. Единственное действие setup не принимает дополнительных опций.


```bash
php vendor/bin/guardian --help
php vendor/bin/guardian setup
```


| CLI | Справочник команд |
|---|---|
| [`guardian:setup`](#guardiansetup) | Подготавливает закрытый путь, ключ, хранилище, файлы Symfony и администратора при его отсутствии. |
| [`guardian:install`](#guardianinstall) | Инициализирует хранилище и создаёт администратора с --username, по умолчанию admin. |
| [`guardian:access`](#guardianaccess) | Показывает путь панели и расположение начальных данных, никогда пароль. |
| [`guardian:doctor`](#guardiandoctor) | Проверяет хранилище, права, ключ, маршрут, активного администратора и настройки. |
| [`guardian:password:reset`](#guardianpasswordreset) | Меняет пароль существующего username скрытым вводом по политике, отзывает все его сессии и записывает событие. |
| [`guardian:mfa:reset`](#guardianmfareset) | Удаляет секрет и счётчик TOTP аккаунта username, отзывает сессии и записывает событие. |
| [`guardian:login:unlock`](#guardianloginunlock) | Сбрасывает счётчики username. |
| [`guardian:policy:reset`](#guardianpolicyreset) | Очищает IP-список и выключает расписание, JIT и удалённый экстренный доступ. |
| [`guardian:lockdown`](#guardianlockdown) | on закрывает диагностику и удаляет все сессии; бизнес-страницы работают. |
| [`guardian:emergency`](#guardianemergency) | Создаёт 5 одноразовых кодов для существующего username, заменяя старые. |
| [`guardian:alerts:flush`](#guardianalertsflush) | Отправляет ожидающие оповещения на alert_email через PHP mail(). |
| [`guardian:password:benchmark`](#guardianpasswordbenchmark) | Измеряет проверку хешей на синтетическом секрете, без пароля пользователя. |
| [`guardian:uninstall`](#guardianuninstall) | Подготавливает удаление файлов, достоверно созданных Guardian. |

## `guardian:setup`

Подготавливает закрытый путь, ключ, хранилище, файлы Symfony и администратора при его отсутствии. Сохраняет существующие записи. Отклоняет настройку в production. После прямого запуска очистите кеш и выполните проверки.


```bash
php bin/console guardian:setup --automatic --env=dev
```

--automatic создаёт admin и секрет в закрытом файле 0600. --path задаёт путь длиной 25–181 символов, не заменяя существующий. --no-admin пропускает создание администратора; без активного аккаунта портал недоступен. --username предлагает интерактивное имя, по умолчанию admin; автоматический аккаунт не переименовывает.

```text
--path: ^/[a-zA-Z0-9/_-]{24,180}$
/_profiler; /_wdt → ∅
```

```bash
php bin/console guardian:setup --username=operator --env=dev
php bin/console cache:clear --env=dev
php bin/console guardian:doctor --env=dev
php bin/console guardian:access --env=dev
```

## `guardian:install`

Инициализирует хранилище и создаёт администратора с --username, по умолчанию admin. Скрытый ввод пароля по действующей политике. Отклоняет существующий аккаунт. Не создаёт маршруты и конфигурацию Symfony; обычно используйте полный помощник.


```bash
php bin/console guardian:install --username=admin --env=dev
```


Имя пользователя : 3 Для 64 Альфаномерные черты,

## `guardian:access`

Показывает путь панели и расположение начальных данных, никогда пароль. --base-url принимает реальный HTTPS origin с необязательным портом, без логина, параметров и фрагмента. Замените пример адреса.


```bash
php bin/console guardian:access --base-url=https://example.com --env=dev
```


## `guardian:doctor`

Проверяет хранилище, права, ключ, маршрут, активного администратора и настройки. Код 1 при обнаружении проблемы. Часовые пояса PHP CLI и PHP-FPM могут различаться. Не проверяет доступ из Интернета и правила прокси.


```bash
php bin/console guardian:doctor --env=dev
```


## `guardian:password:reset`

Меняет пароль существующего username скрытым вводом по политике, отзывает все его сессии и записывает событие. Не передавайте пароль аргументом.


```bash
php bin/console guardian:password:reset admin --env=dev
```


## `guardian:mfa:reset`

Удаляет секрет и счётчик TOTP аккаунта username, отзывает сессии и записывает событие. Зарегистрируйте новый аутентификатор; глобальное требование MFA остаётся и может блокировать профайлер.


```bash
php bin/console guardian:mfa:reset admin --env=dev
```


## `guardian:login:unlock`

Сбрасывает счётчики username. --ip АДРЕС также удаляет счётчик источника IPv4/IPv6; иначе сетевая блокировка может сохраниться. --all удаляет все счётчики. Аккаунты, пароли и сессии не меняются; действие журналируется.


```bash
php bin/console guardian:login:unlock admin --ip=192.0.2.10 --env=dev
```


```bash
php bin/console guardian:login:unlock --all --env=dev
```

## `guardian:policy:reset`

Очищает IP-список и выключает расписание, JIT и удалённый экстренный доступ. Аутентификация, MFA, глобальная блокировка и блокировки входа сохраняются. Заново настройте нужные ограничения в панели.


```bash
php bin/console guardian:policy:reset --env=dev
```


## `guardian:lockdown`

on закрывает диагностику и удаляет все сессии; бизнес-страницы работают. off снимает только глобальную блокировку: нужен новый вход, остальные политики сохраняются.


```bash
php bin/console guardian:lockdown on --env=dev
```


```bash
php bin/console guardian:lockdown off --env=dev
```

## `guardian:emergency`

Создаёт 5 одноразовых кодов для существующего username, заменяя старые. Показывает один раз; храните вне сервера в менеджере секретов. Удалённое применение требует включённого экстренного доступа, сессии, пароля и причины. Не обходит глобальную блокировку.


```bash
php bin/console guardian:emergency admin --env=dev
```


## `guardian:alerts:flush`

Отправляет ожидающие оповещения на alert_email через PHP mail(). Настройте транспорт и плановую задачу, проверьте доставку. Ошибочные отправки остаются в очереди. Код 1 при неверном получателе или ошибке отправки.


```bash
php bin/console guardian:alerts:flush --env=dev
```


## `guardian:password:benchmark`

Измеряет проверку хешей на синтетическом секрете, без пароля пользователя. --iterations: 1–10, по умолчанию 3. JSON с алгоритмом, временем и проверками в секунду. Измеряет один локальный CPU-процесс, не GPU или атакующего.


```bash
php bin/console guardian:password:benchmark --iterations=3 --env=dev
```


## `guardian:uninstall`

Подготавливает удаление файлов, достоверно созданных Guardian. Сохраняет аккаунты, настройки, журналы и ключ. Остановите процессы, выполните до composer remove, очистите кеш dev и перезапустите. Проверьте изменённые файлы и файлы без доказанного происхождения.


```bash
php bin/console guardian:uninstall --env=dev
```

--purge запрашивает удаление данных с отказом по умолчанию. --force требует --purge и подтверждает уничтожение; обязателен с --no-interaction. Внешние хранилища, ссылки и неизвестные файлы могут помешать полному удалению. Сохраните нужные ключи и проверьте результат.

```bash
php bin/console guardian:uninstall --purge --env=dev
# --purge --force --no-interaction
```

## Удаление и данные


```bash
php bin/console guardian:uninstall --env=dev
composer remove --dev omegaguardian/profiler-shield
php bin/console cache:clear --env=dev
```


Копируйте хранилище и ключ вместе, проверяйте восстановление. Потерянные ключи автоматически не генерируются заново. После удаления проверяйте профайлер только приватно. Production: APP_DEBUG=0, WebProfilerBundle и сбор отключены. Локальная цепочка аудита не доказывает целостность захваченного хоста.

[Отключение и данные](desinstallation.md)
