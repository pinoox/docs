# Build And Release A Pinx App

[Back to index](../README.md)

Pinx builds your single-app project into a `.pinx` package that can be installed on a full Pinoox platform.

---

## Before Building

Run:

```bash
pinx doctor
pinx test
pinx migrate:status
```

If your app uses Vue, React, or a Vite-powered theme:

```bash
pinx fe:build
```

If you changed routes, config, Twig templates, or runtime metadata:

```bash
pinx pinker:rebuild
```

---

## Build

```bash
pinx build
```

The package is written to:

```text
export/
```

Build to a custom path:

```bash
pinx build -o export/my-shop.pinx
```

`pinx build` excludes local-only files by default:

- `.env`
- `vendor/`
- `bin/`
- `platform/`
- `storage/`
- `export/`
- development tools

---

## Versioning

The app version lives in `app.php`:

```php
'version-name' => '1.0.0',
'version-code' => 1,
```

Release can bump it for you:

```bash
pinx release --bump=patch
pinx release --bump=minor
pinx release --bump=major
```

---

## Signing

Signing proves that a package came from a trusted source.

Configure signing in `app.php`:

```php
'pinx' => [
    'sign' => [
        'enabled' => true,
        'key' => env('PINX_SIGN_KEY'),
        'key_id' => env('PINX_SIGN_KEY_ID'),
    ],
],
```

Then build a signed release:

```bash
pinx release --bump=patch --sign
```

Keep private signing keys out of Git and `.pinx` packages.

---

## Release Checklist

1. `pinx doctor` has no blocking failures.
2. Tests pass.
3. `.env` does not contain production secrets that will be packaged.
4. DevDB is not used for production.
5. Frontend assets are built if the app uses a frontend stack.
6. Pinker/cache is rebuilt when needed.
7. `app.php` version is correct.
8. The `.pinx` package exists in `export/`.

---

## Install On A Platform

Upload the `.pinx` package to a Pinoox platform manager or install it through the platform CLI.

After installation on the target server:

```bash
php pinoox migrate com_acme_shop
php pinoox cache:build com_acme_shop
php pinoox pinker:rebuild com_acme_shop
```

Use the package name from your `app.php`.

---

## Related

- [Pinx CLI](./pinx-cli.md)
- [app.php manifest](./app-manifest.md)
- [Pinker and cache](../advanced/pinker.md)
