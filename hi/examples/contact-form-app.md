# वॉकथ्रू: कॉन्टैक्ट फ़ॉर्म ऐप

[← इंडेक्स पर वापस जाएँ](../README.md)

कॉन्टैक्ट फ़ॉर्म किसी भी बिज़नेस वेबसाइट के सबसे आम हिस्सों में से एक है। इस वॉकथ्रू में आप इनपुट को **validate** करेंगे, **संदेशों को डेटाबेस में सेव** करेंगे, और एक धन्यवाद (thank-you) पेज पर redirect करेंगे।

**पैकेज:** `com_acme_contact`  
**URL:** `http://localhost/pinoox/contact`  
**पूरा सोर्स:** [docs/source/contact-form-app/](../../source/contact-form-app/) — `apps/` में कॉपी करें
---

## पूर्व-आवश्यकताएँ (Prerequisites)

- [Validation](../basic/validation.md)
- [Migrations](../database/migrations.md)

---

## चरण 1 — ऐप बनाएँ

```bash
php pinoox app:create com_acme_contact --simple
php pinoox app:router set /contact com_acme_contact
```

---

## चरण 2 — `messages` टेबल

```bash
php pinoox migrate:create CreateMessages com_acme_contact
```

```php
<?php
namespace App\com_acme_contact\database\migrations;

use Illuminate\Database\Schema\Blueprint;
use Pinoox\Component\Migration\MigrationBase;

return new class extends MigrationBase
{
    public function up()
    {
        $this->schema->create($this->table('messages', 'com_acme_contact'), function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('email', 180);
            $table->string('subject', 200);
            $table->text('body');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        $this->schema->dropIfExists($this->table('messages', 'com_acme_contact'));
    }
};
```

```bash
php pinoox migrate com_acme_contact
```

---

## चरण 3 — Model

```bash
php pinoox model:create Message com_acme_contact
```

```php
<?php
namespace App\com_acme_contact\Model;

use Pinoox\Component\Database\Model;

class MessageModel extends Model
{
    protected $table = 'messages';

    protected $fillable = ['name', 'email', 'subject', 'body'];
}
```

---

## चरण 4 — Routes

`routes/actions.php`:

```php
<?php

use App\com_acme_contact\Controller\ContactController;
use function Pinoox\Router\action;

action('contact.form', [ContactController::class, 'form']);
action('contact.store', [ContactController::class, 'store']);
action('contact.thanks', [ContactController::class, 'thanks']);
```

`routes/web.php`:

```php
<?php

use function Pinoox\Router\{get, post};

get('/', '@contact.form')->name('home');
post('/send', '@contact.store')->name('contact.store');
get('/thanks', '@contact.thanks')->name('contact.thanks');
```

---

## चरण 5 — Controller

```bash
php pinoox controller:create ContactController com_acme_contact
```

```php
<?php
namespace App\com_acme_contact\Controller;

use App\com_acme_contact\Model\MessageModel;
use Pinoox\Component\Http\Request;
use Pinoox\Component\Kernel\Controller\Controller;
use Pinoox\Portal\View;

class ContactController extends Controller
{
    public function form()
    {
        return View::render('pages/form', [
            'title' => 'Contact us',
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|max:180',
            'subject' => 'required|string|max:200',
            'body' => 'required|string|min:10',
        ]);

        MessageModel::create($data);

        return redirect(url('thanks'));
    }

    public function thanks()
    {
        return View::render('pages/thanks', [
            'title' => 'Message received',
        ]);
    }
}
```

> बाद में आप `create` पर [Event + mail](../advanced/mail.md) के ज़रिए एडमिन को सूचित कर सकते हैं — भेजने का लॉजिक किसी Component में रखें।

---

## चरण 6 — Twig टेम्पलेट्स (inline CSS)

`theme/default/pages/form.twig`:

```twig
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ title }}</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body { font-family: system-ui, sans-serif; background: #f1f5f9; color: #0f172a; margin: 0; line-height: 1.5; }
        .page { max-width: 520px; margin: 0 auto; padding: 2rem 1rem; }
        .panel { background: #fff; border: 2px solid #cbd5e1; border-radius: 10px; padding: 1.5rem; }
        .page-title { margin: 0 0 1.25rem; padding-bottom: .75rem; border-bottom: 2px solid #334155; font-size: 1.5rem; }
        .field { margin-bottom: 1rem; }
        .field label { display: block; font-weight: 600; margin-bottom: .35rem; font-size: .9rem; }
        .field input, .field textarea { width: 100%; padding: .5rem .65rem; border: 2px solid #cbd5e1; border-radius: 6px; font: inherit; }
        .btn { padding: .5rem 1.25rem; font: inherit; font-weight: 600; border-radius: 6px; cursor: pointer; background: transparent; border: 2px solid #2563eb; color: #2563eb; }
        .btn:hover { background: #2563eb; color: #fff; }
    </style>
</head>
<body>
<div class="page">
    <h1 class="page-title">{{ title }}</h1>
    <div class="panel">
        <form method="post" action="{{ url('send') }}">
            <div class="field"><label>Name</label><input name="name" required maxlength="120"></div>
            <div class="field"><label>Email</label><input name="email" type="email" required maxlength="180"></div>
            <div class="field"><label>Subject</label><input name="subject" required maxlength="200"></div>
            <div class="field"><label>Message</label><textarea name="body" rows="5" required minlength="10"></textarea></div>
            <button type="submit" class="btn">Send</button>
        </form>
    </div>
</div>
</body>
</html>
```

`theme/default/pages/thanks.twig`:

```twig
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ title }}</title>
    <style>
        body { font-family: system-ui, sans-serif; background: #f1f5f9; margin: 0; line-height: 1.5; }
        .page { max-width: 520px; margin: 0 auto; padding: 2rem 1rem; }
        .panel { background: #fff; border: 2px solid #cbd5e1; border-radius: 10px; padding: 1.5rem; text-align: center; }
        .page-title { margin: 0 0 1rem; font-size: 1.4rem; }
        .link { display: inline-block; margin-top: 1rem; padding: .45rem 1rem; border: 2px solid #334155; border-radius: 6px; color: #334155; text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>
<div class="page">
    <div class="panel">
        <h1 class="page-title">{{ title }}</h1>
        <p>We will get back to you soon.</p>
        <a class="link" href="{{ url('/') }}">Back to form</a>
    </div>
</div>
</body>
</html>
```

---

## चरण 7 — परीक्षण (Test)

1. `http://localhost/pinoox/contact` खोलें।
2. किसी अमान्य ईमेल के साथ सबमिट करें — validation त्रुटि मिलेगी।
3. मान्य फ़ॉर्म — `/contact/thanks` पर redirect करता है।
4. phpMyAdmin में `com_acme_contact_messages` टेबल देखें।

---

## अगले कदम

| अपग्रेड | दस्तावेज़ |
|---------|-----|
| CAPTCHA / rate limit | [Flows](../basic/flows.md) |
| ईमेल नोटिफ़िकेशन | [Mail](../advanced/mail.md) |
| एडमिन इनबॉक्स | [Flows + Auth](../basic/flows.md) |

---

## संबंधित दस्तावेज़

- [Requests](../basic/requests.md)
- [Validation](../basic/validation.md)
- [URL हेल्पर्स](../basic/url.md)

---

[← इंडेक्स पर वापस जाएँ](../README.md)
