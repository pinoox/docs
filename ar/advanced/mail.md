# إرسال البريد الإلكتروني

[← العودة إلى الفهرس](../README.md)

لا يتضمن Pinoox 3.x خدمة بريد مدمجة في النواة (`pincore`). النهج الموصى به هو **حدث (Event) + خدمة بريد في مكوّن التطبيق (Component)** — بحيث يبقى منطق الإرسال خارج المتحكمات (Controllers). أما للإرسال الفعلي، فأضف **Symfony Mailer** (أو أي مشغّل SMTP) داخل تطبيقك؛ وهذه تبعية اختيارية للتطبيق وليست بمعنى أن "Pinoox = Symfony".

---

## تبعية Symfony Mailer

في ملف `apps/{package}/composer.json` الخاص بتطبيقك:

```json
{
  "require": {
    "symfony/mailer": "^6.4",
    "symfony/google-mailer": "^6.4"
  }
}
```

اضبط إعدادات SMTP في ملف `.env` الخاص بالمشروع أو التطبيق:

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

## الحدث (Event) والمستمع (Listener)

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

## تسجيل المستمع في boot.php

```php
<?php
use App\com_acme_shop\Component\Event\OrderPlaced;
use App\com_acme_shop\Component\Listener\SendOrderConfirmation;
use Pinoox\Component\AppEvent\AppRegister;

return function (AppRegister $register): void {
    $register->listen(OrderPlaced::NAME, SendOrderConfirmation::class);
};
```

أو في `app.php`:

```php
'event' => [
    OrderPlaced::NAME => SendOrderConfirmation::class,
],
```

---

## إطلاق الحدث (Dispatch) من المتحكم

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

المتحكم يطلق الحدث فقط؛ والمستمع هو من يرسل البريد. وعند الفشل، يمكنك إضافة طابور (Queue) أو منطق إعادة محاولة منفصل.

---

## قالب Twig للبريد الإلكتروني

```twig
{# apps/com_acme_shop/theme/default/emails/order-confirm.twig #}
<p>Order #{{ order_id }} was placed successfully.</p>
```

---

## نصائح

- تحقّق من صحة عناوين البريد الإلكتروني في FormRequest أو في المتحكم، وليس في MailService.
- استخدم `schedule.php` ومهام cron في Pinoox للبريد الجماعي.
- يدعم Symfony Mailer كلاً من SMTP و Gmail و Sendmail ومزوّدي الخدمات السحابية عبر DSN.

---

## وثائق ذات صلة

- [خدمات التطبيق](./services.md)
- [الأحداث و boot.php](./boot-and-events.md)
- [المتحكمات (Controllers)](../basic/controllers.md)
- [العروض (Views) و Twig](../basic/views.md)

---

[← العودة إلى الفهرس](../README.md)
