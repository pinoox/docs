# Envio de E-mail

[← Voltar ao índice](../README.md)

O Pinoox 3.x não inclui um serviço de e-mail embutido no core (`pincore`). A abordagem recomendada é **Event + serviço de e-mail no Component do app** — a lógica de envio fica fora dos controllers. Para a entrega em si, adicione o **Symfony Mailer** (ou qualquer driver SMTP) dentro do seu app; trata-se de uma dependência opcional do app, e não de “Pinoox = Symfony”.

---

## Dependência do Symfony Mailer

No `apps/{package}/composer.json` do seu app:

```json
{
  "require": {
    "symfony/mailer": "^6.4",
    "symfony/google-mailer": "^6.4"
  }
}
```

Configure o SMTP no `.env` do projeto ou do app:

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

## Event e listener

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

## Registrar o listener no boot.php

```php
<?php
use App\com_acme_shop\Component\Event\OrderPlaced;
use App\com_acme_shop\Component\Listener\SendOrderConfirmation;
use Pinoox\Component\AppEvent\AppRegister;

return function (AppRegister $register): void {
    $register->listen(OrderPlaced::NAME, SendOrderConfirmation::class);
};
```

Ou no `app.php`:

```php
'event' => [
    OrderPlaced::NAME => SendOrderConfirmation::class,
],
```

---

## Disparar a partir de um controller

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

O controller apenas dispara o event; o listener envia o e-mail. Em caso de falha, você pode adicionar uma fila (queue) ou uma lógica de retry separada.

---

## Template Twig para e-mail

```twig
{# apps/com_acme_shop/theme/default/emails/order-confirm.twig #}
<p>O pedido #{{ order_id }} foi realizado com sucesso.</p>
```

---

## Dicas

- Valide endereços de e-mail no FormRequest ou no controller, não no MailService.
- Use `schedule.php` e o cron do Pinoox para envio em massa.
- O Symfony Mailer suporta SMTP, Gmail, Sendmail e provedores em nuvem via DSN.

---

## Documentação relacionada

- [Serviços do app](./services.md)
- [Events e boot.php](./boot-and-events.md)
- [Controllers](../basic/controllers.md)
- [Views e Twig](../basic/views.md)

---

[← Voltar ao índice](../README.md)
