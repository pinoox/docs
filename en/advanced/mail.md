# Sending Email

[← Back to index](../README.md)

Pinoox 3.x does not ship a built-in mail service in the core (`pincore`). The recommended approach is **Event + mail service in the app Component** — sending logic stays out of controllers. For actual delivery, add **Symfony Mailer** (or any SMTP driver) inside your app; that is an optional app dependency, not “Pinoox = Symfony”.

---

## Symfony Mailer dependency

In your app's `apps/{package}/composer.json`:

```json
{
  "require": {
    "symfony/mailer": "^6.4",
    "symfony/google-mailer": "^6.4"
  }
}
```

Configure SMTP in the project or app `.env`:

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

## Event and listener

Put classes in `Event/` and `Listener/` — they are auto-discovered on boot. No `boot.php` registration is required.

```php
<?php
// Event/OrderPlaced.php
namespace App\com_acme_shop\Event;

use Pinoox\Component\Event\Event;

class OrderPlaced extends Event
{
    public function __construct(
        public readonly int $orderId,
        public readonly string $customerEmail,
    ) {}
}
```

```php
<?php
// Listener/SendOrderConfirmation.php
namespace App\com_acme_shop\Listener;

use App\com_acme_shop\Event\OrderPlaced;
use App\com_acme_shop\Component\MailService;
use Pinoox\Portal\View;

class SendOrderConfirmation
{
    public function handle(OrderPlaced $event): void
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

Extra listeners can still go in `boot.php` or `app.php` `events.listen`. Full guide: [Events](./events.md).

---

## Dispatch from a controller

```php
use App\com_acme_shop\Event\OrderPlaced;

public function checkout(Request $request)
{
    $order = $this->createOrder($request);

    OrderPlaced::dispatch($order->id, $order->email);

    return $this->ok(['order_id' => $order->id], 'order.created');
}
```

The controller only dispatches the event; the listener sends the email. On failure, you can add a queue or separate retry logic.

---

## Twig template for email

```twig
{# apps/com_acme_shop/theme/default/emails/order-confirm.twig #}
<p>Order #{{ order_id }} was placed successfully.</p>
```

---

## Tips

- Validate email addresses in FormRequest or the controller, not in MailService.
- Use `schedule.php` and Pinoox cron for bulk mail.
- Symfony Mailer supports SMTP, Gmail, Sendmail, and cloud providers via DSN.

---

## Related docs

- [App services](./services.md)
- [Events](./events.md)
- [Events and boot.php](./boot-and-events.md)
- [Controllers](../basic/controllers.md)
- [Views and Twig](../basic/views.md)

---

[← Back to index](../README.md)
