# Envío de correo

[← Volver al índice](../README.md)

Pinoox 3.x no incluye un servicio de correo integrado en el núcleo (`pincore`). El enfoque recomendado es **Event + servicio de correo en el Component de la app** — la lógica de envío queda fuera de los controllers. Para la entrega real, añade **Symfony Mailer** (o cualquier driver SMTP) dentro de tu app; es una dependencia opcional de la app, no «Pinoox = Symfony».

---

## Dependencia Symfony Mailer

En el `apps/{package}/composer.json` de tu app:

```json
{
  "require": {
    "symfony/mailer": "^6.4",
    "symfony/google-mailer": "^6.4"
  }
}
```

Configura SMTP en el `.env` del proyecto o de la app:

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

## Event y listener

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

## Registrar el listener en boot.php

```php
<?php
use App\com_acme_shop\Component\Event\OrderPlaced;
use App\com_acme_shop\Component\Listener\SendOrderConfirmation;
use Pinoox\Component\AppEvent\AppRegister;

return function (AppRegister $register): void {
    $register->listen(OrderPlaced::NAME, SendOrderConfirmation::class);
};
```

O en `app.php`:

```php
'event' => [
    OrderPlaced::NAME => SendOrderConfirmation::class,
],
```

---

## Despachar desde un controller

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

El controller solo despacha el evento; el listener envía el correo. En caso de fallo, puedes añadir una cola o lógica de reintento separada.

---

## Plantilla Twig para correo

```twig
{# apps/com_acme_shop/theme/default/emails/order-confirm.twig #}
<p>Order #{{ order_id }} was placed successfully.</p>
```

---

## Consejos

- Valida direcciones de correo en FormRequest o el controller, no en MailService.
- Usa `schedule.php` y el cron de Pinoox para correo masivo.
- Symfony Mailer soporta SMTP, Gmail, Sendmail y proveedores cloud vía DSN.

---

## Documentación relacionada

- [Servicios de app](./services.md)
- [Eventos y boot.php](./boot-and-events.md)
- [Controllers](../basic/controllers.md)
- [Vistas y Twig](../basic/views.md)

---

[← Volver al índice](../README.md)
