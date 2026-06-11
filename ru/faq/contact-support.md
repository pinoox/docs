# Связаться с поддержкой

[← Вернуться к оглавлению](../README.md)

Если после просмотра [Частых проблем](./common-issues.md) у вас остаётся блокер, используйте официальные каналы ниже. Перед обращением в поддержку подготовьте версию Pinoox, версию PHP, текст ошибки и шаги воспроизведения.

---

## Общая поддержка

**Email:** [support@pinoox.com](mailto:support@pinoox.com)

Подходит для:

- Вопросов по установке и деплою
- Неожиданного поведения фреймворка
- Руководства по HMVC и архитектуре приложений

Укажите в письме:

1. Версию Pinoox (`composer.json` → `version` или git tag)
2. Версию PHP (`php -v`)
3. ОС и веб-сервер (Apache/nginx, MAMP, cPanel, …)
4. Полный текст ошибки или скриншот
5. Минимальные шаги воспроизведения

---

## GitHub Issues

Для подтверждённых багов, запросов функций и публичного технического обсуждения:

**Репозиторий:** [github.com/pinoox/pinoox](https://github.com/pinoox/pinoox/issues)

Перед созданием нового issue:

- Поищите дубликаты
- Протестируйте на последнем stable/beta релизе
- Если связано с `pincore`, также проверьте пакет `pinoox/pincore`

Рекомендуемый шаблон issue:

```markdown
## Environment
- Pinoox: 3.1-beta
- PHP: 8.1.x
- OS: Windows / Linux

## Expected
...

## Actual
...

## Steps to reproduce
1. ...
2. ...
```

---

## Сообщения о безопасности

**Email:** [security@pinoox.com](mailto:security@pinoox.com)

**Только** для уязвимостей безопасности — SQL injection, обход auth, RCE, раскрытие секретов.

- Не публикуйте детали публично (GitHub issue), пока не готов патч
- По возможности приложите минимальный PoC и описание impact

---

## Участие в разработке

Для PR и разработки фреймворка:

- [Contributing](../introduction/contributions.md)
- Fork → branch → test (`php pinoox test`) → Pull Request

---

## Ресурсы для самостоятельного решения

| Тема | Документ |
|-------|-----|
| Установка | [installing-pinoox.md](../start/installing-pinoox.md) |
| Первое приложение | [your-first-app.md](../start/your-first-app.md) |
| Частые проблемы | [common-issues.md](./common-issues.md) |
| Тестирование | [getting-started.md](../test/getting-started.md) |

**Сайт:** [pinoox.com](https://www.pinoox.com/)

---

## Связанные документы

- [Частые проблемы](./common-issues.md)
- [Что такое Pinoox?](../introduction/what-is-pinoox.md)
- [Contributing](../introduction/contributions.md)
- [Установка Pinoox](../start/installing-pinoox.md)

---

[← Вернуться к оглавлению](../README.md)
