# 发送邮件（Email）

[← 返回索引](../README.md)

Pinoox 3.x 的核心（`pincore`）不内置邮件服务。推荐做法是 **事件（Event）+ 应用 Component 中的邮件服务** —— 让发送逻辑远离控制器。实际发送时，可在你的应用中引入 **Symfony Mailer**（或任意 SMTP 驱动）；这是应用的可选依赖，并不意味着 “Pinoox = Symfony”。

---

## Symfony Mailer 依赖

在应用的 `apps/{package}/composer.json` 中：

```json
{
  "require": {
    "symfony/mailer": "^6.4",
    "symfony/google-mailer": "^6.4"
  }
}
```

在项目或应用的 `.env` 中配置 SMTP：

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

## 事件与监听器（Listener）

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

## 在 boot.php 中注册监听器

```php
<?php
use App\com_acme_shop\Component\Event\OrderPlaced;
use App\com_acme_shop\Component\Listener\SendOrderConfirmation;
use Pinoox\Component\AppEvent\AppRegister;

return function (AppRegister $register): void {
    $register->listen(OrderPlaced::NAME, SendOrderConfirmation::class);
};
```

或者在 `app.php` 中：

```php
'event' => [
    OrderPlaced::NAME => SendOrderConfirmation::class,
],
```

---

## 在控制器中派发（Dispatch）

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

控制器只负责派发事件；由监听器发送邮件。发送失败时，可以增加队列或单独的重试逻辑。

---

## 邮件的 Twig 模板

```twig
{# apps/com_acme_shop/theme/default/emails/order-confirm.twig #}
<p>Order #{{ order_id }} was placed successfully.</p>
```

---

## 提示

- 在 FormRequest 或控制器中验证邮箱地址，而不是在 MailService 中。
- 批量发送邮件请使用 `schedule.php` 和 Pinoox 的 cron。
- Symfony Mailer 通过 DSN 支持 SMTP、Gmail、Sendmail 以及各种云服务商。

---

## 相关文档

- [应用服务（App services）](./services.md)
- [事件与 boot.php](./boot-and-events.md)
- [控制器（Controllers）](../basic/controllers.md)
- [视图与 Twig](../basic/views.md)

---

[← 返回索引](../README.md)
