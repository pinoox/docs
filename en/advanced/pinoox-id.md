# Pinoox ID

[← Back to index](../README.md)

Every Pinoox install gets a **Pinoox ID**: a stable identifier for *this instance*, created on first boot and never rotated.

It is an identifier, not a secret. Use it to recognize the install — not to authenticate it.

---

## What it is (and is not)

| Value | Role | Changes? |
|-------|------|----------|
| `version_name` / `version_code` | Software version | On upgrade |
| `APP_KEY` | Encryption secret | When rotated |
| Domain / `APP_URL` | Public URL | When the site moves |
| **Pinoox ID** | This install | Never |

Do not derive the ID from hostname, install path, or `APP_KEY`. Those change, or they leak a secret.

---

## Format and storage

```text
px_8f3c2a91b47d4e0aa1c6d92e5f18b3c4
```

- Prefix `px_`
- 32 hex characters (UUID v4, no dashes)

Stored in `pinker/state/identity.php` (gitignored). `pinker:rebuild` does not replace it.

```php
<?php

return [
    'pinoox_id' => 'px_8f3c2a91b47d4e0aa1c6d92e5f18b3c4',
    'created_at' => '2026-08-18T16:29:00+00:00',
];
```

Created automatically on the first web or CLI boot if the file is missing. If a non-empty `pinoox_id` already exists, Pinoox keeps it.

---

## Reading the ID

```php
use Pinoox\Portal\Identity;

$id = pinoox_id();
$id = Identity::id();
$createdAt = Identity::createdAt();
```

The helper and the Portal read the same install file. Call them after bootstrap (`index.php` / `pinoox`), not in a raw PHP script.

---

## Use cases

### 1. Hub, market, and remote APIs

A domain can change; the install should not. Send the Pinoox ID with license checks, app-store downloads, or account linking so the same server stays the same instance after a domain move.

```php
use Pinoox\Portal\Http;
use Pinoox\Portal\Identity;
use Pinoox\Portal\Url;

Http::post('https://www.pinoox.com/api/manager/v1/account/getData', [
    'json' => [
        'pinoox_id' => Identity::id(),
        'remote_url' => Url::origin(),
        'token_key' => config('connect.token_key'),
    ],
]);
```

Pair the public ID with a separate secret (`APP_KEY`, connect token, or an instance secret) when the remote side must *trust* this install.

### 2. Support and diagnostics

Put the ID in a support ticket, `doctor` output, or an admin “about” screen so two otherwise identical sites are distinguishable.

```php
return $this->ok([
    'pinoox_id' => pinoox_id(),
    'version' => config('~pinoox.version_name'),
]);
```

Do not print it on public pages.

### 3. License and activation per install

One purchase can bind to one Pinoox ID. Cloning the project *without* `pinker/state/identity.php` creates a new ID (a new install). Copying that file copies the identity — treat staging copies as a new install by deleting the file.

### 4. Opt-in telemetry and crash reports

If the operator agrees, attach the ID so anonymous reports group by install, not by IP or domain.

```php
Logger::error('Payment gateway timeout', [
    'pinoox_id' => pinoox_id(),
    'gateway' => 'stripe',
]);
```

Having an ID is not permission to send data. Keep telemetry opt-in.

### 5. Cache, queue, and rate-limit keys

When several installs share Redis or a cache host, namespace keys with the ID so they do not collide.

```php
$cacheKey = pinoox_id() . ':market:featured';
```

### 6. Webhooks and outbound integration

Remote systems can store `pinoox_id` as the customer’s site key. URL callbacks may change; the ID does not.

```php
use Pinoox\Portal\Http;

Http::post($partnerWebhook, [
    'json' => [
        'pinoox_id' => pinoox_id(),
        'event' => 'order.paid',
        'order_id' => $orderId,
    ],
]);
```

---

## Cloning, Docker, and reset

| Situation | Result |
|-----------|--------|
| Fresh clone of the git repo | New ID on first boot (`pinker/` is not in git) |
| Copy the whole project including `pinker/state/` | Same ID (same instance, moved) |
| Staging copied from production | Same ID until you delete `pinker/state/identity.php` |
| Docker image without a volume for `pinker/` | New ID on every new container |
| Docker with a persistent `pinker/` volume | Stable ID across restarts |

To mint a new ID (staging, or a new instance on the same files):

1. Delete `pinker/state/identity.php`
2. Boot once (web or CLI)

Do not bake an ID into a Docker image.

---

## Tips

- Pinoox ID is public among admins and your own APIs — never use it as a password or JWT secret
- Do not store the ID in `platform/pinoox.config.php` or commit it; that file is the distribution version
- `.env` is the wrong home: copying `.env.example` must not create or duplicate an ID
- The database is not the source of truth; dumps would clone or drop the identity

---

## Related docs

- [Pinker and cache](./pinker.md)
- [Config](../basic/config.md)
- [Global helpers](./helpers.md)
- [Portal](../basic/portal.md)
- [Kernel and boot pipeline](./kernel.md)

---

[← Back to index](../README.md)
