# ارسال ایمیل

پینوکس 3.x در هسته (`pincore`) سرویس ایمیل یکپارچه ندارد. روش پیشنهادی: **رویداد (Event) + سرویس ایمیل در Component اپ** — منطق ارسال از کنترلر جدا می‌ماند و با Symfony Mailer (یا هر درایور SMTP) در همان اپ پیاده‌سازی می‌شود.

---

## وابستگی Symfony Mailer

در `apps/{package}/composer.json` اپ خود:

```json
{
  "require": {
    "symfony/mailer": "^6.4",
    "symfony/google-mailer": "^6.4"
  }
}
```

تنظیم SMTP در `.env` پروژه یا اپ:

```env
MAILER_DSN=smtp://user:pass@smtp.example.com:587
MAIL_FROM=noreply@example.com
MAIL_FROM_NAME="My Shop"
```

---

## سرویس MailService

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

## رویداد و Listener

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
            'سفارش شما ثبت شد',
            $html,
        );
    }
}
```

---

## ثبت Listener در boot.php

```php
<?php
use App\com_acme_shop\Component\Event\OrderPlaced;
use App\com_acme_shop\Component\Listener\SendOrderConfirmation;
use Pinoox\Component\AppEvent\AppRegister;

return function (AppRegister $register): void {
    $register->listen(OrderPlaced::NAME, SendOrderConfirmation::class);
};
```

یا در `app.php`:

```php
'event' => [
    OrderPlaced::NAME => SendOrderConfirmation::class,
],
```

---

## فراخوانی از کنترلر

```php
use App\com_acme_shop\Component\Event\OrderPlaced;
use Pinoox\Component\Http\Request;
use Pinoox\Component\Kernel\Controller\ApiController;
use Pinoox\Portal\Event;

class CheckoutController extends ApiController
{
    public function checkout(Request $request)
    {
        $order = $this->createOrder($request);

        Event::dispatch(new OrderPlaced($order->id, $order->email), OrderPlaced::NAME);

        return $this->ok(['order_id' => $order->id], 'order.created');
    }
}
```

کنترلر فقط رویداد را dispatch می‌کند؛ ارسال ایمیل در Listener انجام می‌شود و در صورت خطا می‌توانید صف یا retry جداگانه اضافه کنید.

---

## قالب Twig برای ایمیل

```twig
{# apps/com_acme_shop/theme/default/emails/order-confirm.twig #}
<p>سفارش شماره {{ order_id }} با موفقیت ثبت شد.</p>
```

---

## نکات

- اعتبارسنجی آدرس ایمیل را در FormRequest/کنترلر انجام دهید، نه در MailService.
- برای ایمیل‌های انبوه از `schedule.php` و cron پینوکس استفاده کنید.
- Symfony Mailer با DSN از SMTP، Gmail، Sendmail و سرویس‌های ابری سازگار است.

---

## مستندات مرتبط

- [سرویس‌های اپ](./services.md)
- [رویدادها و boot.php](../../pinoox%20docs/pinoox-app-events.md)
- [Controllers](../basic/controllers.md)
- [View و Twig](../basic/views.md)
