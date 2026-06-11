# E-Mails versenden

[← Zurück zur Übersicht](../README.md)

Pinoox 3.x bringt im Core (`pincore`) keinen eingebauten Mail-Service mit. Der empfohlene Ansatz ist **Event + Mail-Service im App-Component** — die Versandlogik bleibt damit außerhalb der Controller. Für den tatsächlichen Versand fügen Sie **Symfony Mailer** (oder einen beliebigen SMTP-Treiber) in Ihrer App hinzu; das ist eine optionale App-Abhängigkeit, nicht „Pinoox = Symfony“.

---

## Symfony-Mailer-Abhängigkeit

In der `apps/{package}/composer.json` Ihrer App:

```json
{
  "require": {
    "symfony/mailer": "^6.4",
    "symfony/google-mailer": "^6.4"
  }
}
```

SMTP in der `.env` des Projekts oder der App konfigurieren:

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

## Event und Listener

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

## Listener in boot.php registrieren

```php
<?php
use App\com_acme_shop\Component\Event\OrderPlaced;
use App\com_acme_shop\Component\Listener\SendOrderConfirmation;
use Pinoox\Component\AppEvent\AppRegister;

return function (AppRegister $register): void {
    $register->listen(OrderPlaced::NAME, SendOrderConfirmation::class);
};
```

Oder in `app.php`:

```php
'event' => [
    OrderPlaced::NAME => SendOrderConfirmation::class,
],
```

---

## Dispatch aus einem Controller

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

Der Controller löst nur das Event aus; der Listener verschickt die E-Mail. Bei Fehlern können Sie eine Queue oder separate Retry-Logik ergänzen.

---

## Twig-Template für die E-Mail

```twig
{# apps/com_acme_shop/theme/default/emails/order-confirm.twig #}
<p>Order #{{ order_id }} was placed successfully.</p>
```

---

## Tipps

- Validieren Sie E-Mail-Adressen im FormRequest oder im Controller, nicht im MailService.
- Verwenden Sie `schedule.php` und den Pinoox-Cron für Massenmails.
- Symfony Mailer unterstützt SMTP, Gmail, Sendmail und Cloud-Anbieter per DSN.

---

## Verwandte Dokumente

- [App-Services](./services.md)
- [Events und boot.php](./boot-and-events.md)
- [Controller](../basic/controllers.md)
- [Views und Twig](../basic/views.md)

---

[← Zurück zur Übersicht](../README.md)
