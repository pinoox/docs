# Отправка электронной почты (Email)

[← Назад к оглавлению](../README.md)

Pinoox 3.x не поставляет встроенный почтовый сервис в ядре (`pincore`). Рекомендуемый подход — **Event + почтовый сервис в компоненте (Component) приложения** — логика отправки остаётся вне контроллеров. Для фактической доставки добавьте **Symfony Mailer** (или любой SMTP-драйвер) внутрь вашего приложения; это необязательная зависимость приложения, а не «Pinoox = Symfony».

---

## Зависимость Symfony Mailer

В `apps/{package}/composer.json` вашего приложения:

```json
{
  "require": {
    "symfony/mailer": "^6.4",
    "symfony/google-mailer": "^6.4"
  }
}
```

Настройте SMTP в `.env` проекта или приложения:

```env
MAILER_DSN=smtp://user:pass@smtp.example.com:587
MAIL_FROM=noreply@example.com
MAIL_FROM_NAME="My Shop"
```

---

## MailService

```php
<?php
namespace App\com_acme_shop\Component;

use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;

final class MailService
{
    private Mailer $mailer;

    public function __construct(?string $dsn = null)
    {
        $this->mailer = new Mailer(Transport::fromDsn($dsn ?? env('MAILER_DSN')));
    }

    public function send(string $to, string $subject, string $html): void
    {
        $email = (new Email())
            ->from(env('MAIL_FROM'))
            ->to($to)
            ->subject($subject)
            ->html($html);

        $this->mailer->send($email);
    }
}
```

---

## Событие (Event) и слушатель (Listener)

```php
<?php
namespace App\com_acme_shop\Component\Event;

final class OrderPlaced
{
    public const NAME = 'shop.order.placed';

    public function __construct(
        public readonly int $orderId,
        public readonly string $customerEmail,
    ) {}
}
```

```php
<?php
namespace App\com_acme_shop\Component\Listener;

use App\com_acme_shop\Component\Event\OrderPlaced;
use App\com_acme_shop\Component\MailService;
use Pinoox\Portal\View;

final class SendOrderConfirmation
{
    public function __invoke(OrderPlaced $event): void
    {
        $html = View::render('emails/order-confirm.twig', [
            'order_id' => $event->orderId,
        ]);

        (new MailService())->send(
            $event->customerEmail,
            'Your order has been placed',
            $html,
        );
    }
}
```

---

## Регистрация слушателя в boot.php

```php
<?php
use App\com_acme_shop\Component\Event\OrderPlaced;
use App\com_acme_shop\Component\Listener\SendOrderConfirmation;
use Pinoox\Component\AppEvent\AppRegister;

return function (AppRegister $register): void {
    $register->listen(OrderPlaced::NAME, SendOrderConfirmation::class);
};
```

Или в `app.php`:

```php
'event' => [
    OrderPlaced::NAME => SendOrderConfirmation::class,
],
```

---

## Отправка события из контроллера

```php
use App\com_acme_shop\Component\Event\OrderPlaced;
use Pinoox\Portal\Event;

public function checkout(Request $request)
{
    $order = $this->createOrder($request);

    Event::dispatch(new OrderPlaced($order->id, $order->email), OrderPlaced::NAME);

    return $this->ok(['order_id' => $order->id], 'order.created');
}
```

Контроллер только отправляет событие; слушатель отправляет письмо. При сбое можно добавить очередь или отдельную логику повторных попыток.

---

## Twig-шаблон письма

```twig
{# apps/com_acme_shop/theme/default/emails/order-confirm.twig #}
<p>Order #{{ order_id }} was placed successfully.</p>
```

---

## Советы

- Валидируйте адреса электронной почты в FormRequest или контроллере, а не в MailService.
- Для массовых рассылок используйте `schedule.php` и cron Pinoox.
- Symfony Mailer поддерживает SMTP, Gmail, Sendmail и облачных провайдеров через DSN.

---

## Связанные документы

- [Сервисы приложения](./services.md)
- [События и boot.php](./boot-and-events.md)
- [Контроллеры](../basic/controllers.md)
- [Представления (Views) и Twig](../basic/views.md)

---

[← Назад к оглавлению](../README.md)
