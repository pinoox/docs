# ईमेल भेजना (Sending Email)

[← अनुक्रमणिका पर वापस जाएँ](../README.md)

Pinoox 3.x core (`pincore`) में कोई built-in mail service नहीं देता। अनुशंसित तरीका है **Event + ऐप Component में mail service** — भेजने का लॉजिक controllers से बाहर रहता है। वास्तविक डिलीवरी के लिए अपने ऐप में **Symfony Mailer** (या कोई भी SMTP driver) जोड़ें; यह एक वैकल्पिक ऐप dependency है, न कि “Pinoox = Symfony”।

---

## Symfony Mailer dependency

आपके ऐप के `apps/{package}/composer.json` में:

```json
{
  "require": {
    "symfony/mailer": "^6.4",
    "symfony/google-mailer": "^6.4"
  }
}
```

प्रोजेक्ट या ऐप के `.env` में SMTP कॉन्फ़िगर करें:

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

## Event और listener

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

## boot.php में listener रजिस्टर करना

```php
<?php
use App\com_acme_shop\Component\Event\OrderPlaced;
use App\com_acme_shop\Component\Listener\SendOrderConfirmation;
use Pinoox\Component\AppEvent\AppRegister;

return function (AppRegister $register): void {
    $register->listen(OrderPlaced::NAME, SendOrderConfirmation::class);
};
```

या `app.php` में:

```php
'event' => [
    OrderPlaced::NAME => SendOrderConfirmation::class,
],
```

---

## Controller से dispatch करना

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

Controller केवल event को dispatch करता है; listener ईमेल भेजता है। विफलता पर, आप queue या अलग retry लॉजिक जोड़ सकते हैं।

---

## ईमेल के लिए Twig template

```twig
{# apps/com_acme_shop/theme/default/emails/order-confirm.twig #}
<p>Order #{{ order_id }} was placed successfully.</p>
```

---

## सुझाव

- ईमेल पतों को FormRequest या controller में validate करें, MailService में नहीं।
- Bulk mail के लिए `schedule.php` और Pinoox cron का उपयोग करें।
- Symfony Mailer DSN के माध्यम से SMTP, Gmail, Sendmail और क्लाउड providers का समर्थन करता है।

---

## संबंधित दस्तावेज़

- [App services](./services.md)
- [Events and boot.php](./boot-and-events.md)
- [Controllers](../basic/controllers.md)
- [Views and Twig](../basic/views.md)

---

[← अनुक्रमणिका पर वापस जाएँ](../README.md)
