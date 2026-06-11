# メール送信

[← 索引に戻る](../README.md)

Pinoox 3.x のコア（`pincore`）には組み込みメールサービスは同梱されていません。推奨アプローチは **Event + アプリ Component 内のメールサービス** — 送信ロジックを Controller から分離します。実際の配信には、アプリ内に **Symfony Mailer**（または任意の SMTP ドライバー）を追加します。これは任意のアプリ依存関係であり、「Pinoox = Symfony」ではありません。

---

## Symfony Mailer 依存関係

アプリの `apps/{package}/composer.json` 内:

```json
{
  "require": {
    "symfony/mailer": "^6.4",
    "symfony/google-mailer": "^6.4"
  }
}
```

プロジェクトまたはアプリの `.env` で SMTP を設定:

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

## Event とリスナー

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

## boot.php でリスナーを登録

```php
<?php
use App\com_acme_shop\Component\Event\OrderPlaced;
use App\com_acme_shop\Component\Listener\SendOrderConfirmation;
use Pinoox\Component\AppEvent\AppRegister;

return function (AppRegister $register): void {
    $register->listen(OrderPlaced::NAME, SendOrderConfirmation::class);
};
```

または `app.php` 内:

```php
'event' => [
    OrderPlaced::NAME => SendOrderConfirmation::class,
],
```

---

## Controller からディスパッチ

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

Controller は Event のみディスパッチし、リスナーがメールを送信します。失敗時はキューまたは別のリトライロジックを追加できます。

---

## メール用 Twig テンプレート

```twig
{# apps/com_acme_shop/theme/default/emails/order-confirm.twig #}
<p>Order #{{ order_id }} was placed successfully.</p>
```

---

## ヒント

- メールアドレスは FormRequest または Controller で Validation し、MailService では行わない
- 一括メールには `schedule.php` と Pinoox cron を使用
- Symfony Mailer は DSN 経由で SMTP、Gmail、Sendmail、クラウドプロバイダーをサポート

---

## 関連ドキュメント

- [App services](./services.md)
- [イベントと boot.php](./boot-and-events.md)
- [Controller](../basic/controllers.md)
- [View と Twig](../basic/views.md)

---

[← 索引に戻る](../README.md)
