# Sending Email

[← 색인으로 돌아가기](../README.md)

Pinoox 3.x core(`pincore`)에는 내장 mail service가 없습니다. 권장 방법은 **Event + 앱 Component의 mail service** — 전송 logic을 Controller 밖에 둡니다. 실제 delivery는 앱에 **Symfony Mailer**(또는 SMTP driver) 추가; 선택적 앱 dependency이며 “Pinoox = Symfony”가 아닙니다.

---

## Symfony Mailer dependency

앱 `apps/{package}/composer.json`에서:

```json
{
  "require": {
    "symfony/mailer": "^6.4",
    "symfony/google-mailer": "^6.4"
  }
}
```

프로젝트 또는 앱 `.env`에 SMTP 설정:

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

## Event와 listener

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

## boot.php에서 listener 등록

```php
<?php
use App\com_acme_shop\Component\Event\OrderPlaced;
use App\com_acme_shop\Component\Listener\SendOrderConfirmation;
use Pinoox\Component\AppEvent\AppRegister;

return function (AppRegister $register): void {
    $register->listen(OrderPlaced::NAME, SendOrderConfirmation::class);
};
```

또는 `app.php`에서:

```php
'event' => [
    OrderPlaced::NAME => SendOrderConfirmation::class,
],
```

---

## Controller에서 dispatch

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

Controller는 event만 dispatch; listener가 email 전송. 실패 시 queue 또는 retry logic 추가 가능.

---

## Email용 Twig template

```twig
{# apps/com_acme_shop/theme/default/emails/order-confirm.twig #}
<p>Order #{{ order_id }} was placed successfully.</p>
```

---

## Tips

- email address는 MailService가 아니라 FormRequest 또는 Controller에서 validate.
- bulk mail에는 `schedule.php`와 Pinoox cron 사용.
- Symfony Mailer는 DSN으로 SMTP, Gmail, Sendmail, cloud provider 지원.

---

## 관련 문서

- [App services](./services.md)
- [Events and boot.php](./boot-and-events.md)
- [Controller](../basic/controllers.md)
- [Views and Twig](../basic/views.md)

---

[← 색인으로 돌아가기](../README.md)
