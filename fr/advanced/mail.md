# Envoi d'emails

[← Retour à l'index](../README.md)

Pinoox 3.x n'embarque pas de service de mail intégré dans le cœur (`pincore`). L'approche recommandée est **Event + service de mail dans le Component de l'application** — la logique d'envoi reste hors des contrôleurs. Pour l'envoi effectif, ajoutez **Symfony Mailer** (ou n'importe quel driver SMTP) dans votre application ; c'est une dépendance optionnelle de l'application, et non « Pinoox = Symfony ».

---

## Dépendance Symfony Mailer

Dans le `apps/{package}/composer.json` de votre application :

```json
{
  "require": {
    "symfony/mailer": "^6.4",
    "symfony/google-mailer": "^6.4"
  }
}
```

Configurez SMTP dans le `.env` du projet ou de l'application :

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

## Événement et listener

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

## Enregistrer le listener dans boot.php

```php
<?php
use App\com_acme_shop\Component\Event\OrderPlaced;
use App\com_acme_shop\Component\Listener\SendOrderConfirmation;
use Pinoox\Component\AppEvent\AppRegister;

return function (AppRegister $register): void {
    $register->listen(OrderPlaced::NAME, SendOrderConfirmation::class);
};
```

Ou dans `app.php` :

```php
'event' => [
    OrderPlaced::NAME => SendOrderConfirmation::class,
],
```

---

## Dispatcher depuis un contrôleur

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

Le contrôleur ne fait que dispatcher l'événement ; le listener envoie l'email. En cas d'échec, vous pouvez ajouter une file d'attente (queue) ou une logique de réessai séparée.

---

## Template Twig pour l'email

```twig
{# apps/com_acme_shop/theme/default/emails/order-confirm.twig #}
<p>Order #{{ order_id }} was placed successfully.</p>
```

---

## Conseils

- Validez les adresses email dans une FormRequest ou le contrôleur, pas dans MailService.
- Utilisez `schedule.php` et le cron de Pinoox pour les envois en masse.
- Symfony Mailer prend en charge SMTP, Gmail, Sendmail et les fournisseurs cloud via DSN.

---

## Documentation associée

- [Services d'application](./services.md)
- [Événements et boot.php](./boot-and-events.md)
- [Contrôleurs](../basic/controllers.md)
- [Vues et Twig](../basic/views.md)

---

[← Retour à l'index](../README.md)
