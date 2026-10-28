# ΩGuardian Profiler Shield — Установка и использование

PHP 8.3 или новее; Symfony 5.4, 6.4 или 7.4. Для Symfony 8.1 нужен PHP 8.4 или новее. Необходимы Composer и расширение OpenSSL + Tokenizer. Для профайлера установите WebProfilerBundle.

## 1. Установка

Выполняйте команды из корня приложения Symfony, содержащего composer.json и bin/console.

```bash
composer require --dev omegaguardian/profiler-shield
```

## 2. Настройка

```bash
php vendor/bin/guardian setup
```

Помощник активирует bundle в dev/test, подготавливает хранилище и учётную запись администратора, очищает кеш и проверяет настройки. Переходите дальше только после успеха каждого шага. Во время настройки приложение должно оставаться закрытым. Повторный запуск сохраняет учётные записи и ключи.

## 3. Открытие панели

```bash
php bin/console guardian:access --env=dev
```

Команда выводит закрытый путь панели. Замените https://VOTRE-HOTE реальным HTTPS-адресом приложения с портом. Данные входа находятся в .guardian-shield/initial-admin-credentials.txt или указанном хранилище. При первом входе необходимо сменить пароль. Не передавайте этот файл.

## 4. Удаление

Остановите рабочие процессы и выполняйте команды по порядку, проверяя каждый результат. Хранилище и ключ сохраняются. Перезапустите процессы и проверьте профайлер в закрытой среде.

```bash
php bin/console guardian:uninstall --env=dev
composer remove --dev omegaguardian/profiler-shield
php bin/console cache:clear --env=dev
```

Для удаления учётных записей и настроек замените первую команду следующей и подтвердите. Отказ ничего не меняет. Внешние хранилища и изменённые файлы проверьте вручную; сохраните ключи нужных хранилищ.

```bash
php bin/console guardian:uninstall --purge --env=dev
```

## 5. CLI

Справка описывает команды. Команда доступа находит панель, диагностика проверяет настройки. Необходим доверенный локальный доступ.

```bash
php vendor/bin/guardian --help
php bin/console guardian:doctor --env=dev
php bin/console guardian:access --env=dev
```

## Private Packagist

Если организация использует Private Packagist, настройте этот репозиторий перед установкой и сохраните данные Composer по её инструкциям. Никогда не добавляйте токены в Git.

```bash
composer config repositories.guardian composer https://repo.packagist.com/guardian/
```

[Private Packagist](https://packagist.com/docs/setup)
