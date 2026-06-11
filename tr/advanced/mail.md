# E-posta gönderme

[← Dizine dön](../README.md)

Pinoox 3.x çekirdeğinde (`pincore`) yerleşik bir mail servisi sunmaz. Önerilen yaklaşım **Event + uygulama Component'inde mail servisi** — gönderme mantığı controller'lardan ayrı kalır. Gerçek teslimat için uygulamanıza **Symfony Mailer** (veya herhangi bir SMTP sürücüsü) ekleyin; bu isteğe bağlı bir uygulama bağımlılığıdır, "Pinoox = Symfony" değildir.

---

## Symfony Mailer bağımlılığı

Uygulamanızın `apps/{package}/composer.json` dosyasında:

```json
{
  "require": {
    "symfony/mailer": "^6.4",
    "symfony/google-mailer": "^6.4"
  }
}
```

SMTP'yi proje veya uygulama `.env` dosyasında yapılandırın:

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

## Event ve listener

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

## Listener'ı boot.php'de kaydetme

```php
<?php
use App\com_acme_shop\Component\Event\OrderPlaced;
use App\com_acme_shop\Component\Listener\SendOrderConfirmation;
use Pinoox\Component\AppEvent\AppRegister;

return function (AppRegister $register): void {
    $register->listen(OrderPlaced::NAME, SendOrderConfirmation::class);
};
```

Veya `app.php` içinde:

```php
'event' => [
    OrderPlaced::NAME => SendOrderConfirmation::class,
],
```

---

## Controller'dan dispatch

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

Controller yalnızca event'i dispatch eder; listener e-postayı gönderir. Hata durumunda kuyruk veya ayrı yeniden deneme mantığı ekleyebilirsiniz.

---

## E-posta için Twig şablonu

```twig
{# apps/com_acme_shop/theme/default/emails/order-confirm.twig #}
<p>Order #{{ order_id }} was placed successfully.</p>
```

---

## İpuçları

- E-posta adreslerini FormRequest veya controller'da doğrulayın, MailService'te değil.
- Toplu mail için `schedule.php` ve Pinoox cron kullanın.
- Symfony Mailer DSN üzerinden SMTP, Gmail, Sendmail ve bulut sağlayıcılarını destekler.

---

## İlgili dokümantasyon

- [Uygulama servisleri](./services.md)
- [Event'ler ve boot.php](./boot-and-events.md)
- [Controller](../basic/controllers.md)
- [View ve Twig](../basic/views.md)

---

[← Dizine dön](../README.md)
