# Package naming

[← Back to index](../README.md)

Every Pinoox app has a **package name** — a unique identifier used for the app folder, `app.php`, namespace, database table prefix, and CLI commands.

---

## Format

```
{scope}_{owner}_{app}
{scope}_{owner}_{app}_{module}   ← optional fourth segment
```

| Part | Length | Description | Examples |
|------|--------|-------------|----------|
| **scope** | 2–10 chars | Domain / category prefix | `com`, `ir`, `io`, `co`, `opensource` |
| **owner** | 1+ chars | Brand, team, or username | `pinoox`, `github`,   `google`, `yoosefap`, `esmaeilbahrani`, `acme` |
| **app** | 1+ chars | App purpose | `manager`, `shop`, `ai`, `financial` |
| **module** | optional | Submodule inside the same app | `panel`, `api`, `admin` |

### Valid examples

```
com_pinoox_manager
com_acme_shop
co_pinoox_app
ir_mysite_financial
io_pinoox_ai
com_acme_shop_panel
opensource_acme_blog
```

### Invalid examples

```
manager              ← short alias, not a package
com_shop             ← only two segments
bad-name             ← hyphens are not allowed
a_b_c                ← scope must be at least 2 characters
```

---

## Rules (enforced by the framework)

- Characters: **lowercase** letters `a-z`, digits `0-9`, underscore `_`
- Structure: **3 or 4** segments separated by `_`
- Scope: **2–10** characters, must start with a letter
- Max total length: **64** characters
- **Case-insensitive input**: `COM_PINOOX_MANAGER` and `com_pinoox_manager` refer to the same package (stored as lowercase)

---

## One name, three places

The package name must match everywhere:

```
apps/io_pinoox_ai/          ← folder name
app.php → 'package' => 'io_pinoox_ai'
App\io_pinoox_ai\Controller\...   ← PHP namespace
```

---

## Recommended scopes

| Scope | Typical use |
|-------|-------------|
| `com` | Default for general apps (wizard adds this when you type a short name) |
| `ir` | Iran-focused projects or brands |
| `io` | Personal tools and small services |
| `co` | Company / organization internal apps |
| `dev` | Development and experimental apps |

System apps shipped with Pinoox use `com_pinoox_*` (e.g. `com_pinoox_manager`).

---

## Wizard behavior (`app:create`)

When you create an app interactively:

| You type | Result |
|----------|--------|
| `my_shop` | `com_my_shop` |
| `com_acme_blog` | `com_acme_blog` |
| `io_pinoox_ai` | `io_pinoox_ai` (unchanged — already valid) |
| `IO_PINOOX_AI` | `io_pinoox_ai` (normalized to lowercase) |

---

## Database and routes

- **Route prefix** uses the app slug: `com_pinoox_manager` → `manager.`
- **Suggested table prefix** uses the same slug: `com_pinoox_manager` → `manager_` → table `manager_users`
- You may set **any custom prefix** in `app.php` — the installer only suggests the slug; it does not override your explicit choice unless there is a conflict (then it auto-adjusts, e.g. `shop_2_`)
- For 4-segment packages: `com_acme_shop_panel` → route `shop_panel.` and suggested tables `shop_panel_orders`

Set a custom prefix in source:

```php
// app.php — optional; any valid prefix is accepted
'table' => ['prefix' => 'my_legacy_'],
// or
'database' => ['prefix' => 'my_legacy_'],
```

If the slug prefix is already used by another app, the installer tries `{owner}_{app}_` then `_2`, `_3`, … automatically.

Examples:

| Package | Route prefix | Suggested table prefix |
|---------|--------------|------------------------|
| `com_pinoox_manager` | `manager.` | `manager_` |
| `io_yoosefap_ai` | `ai.` | `ai_` |
| `com_acme_shop_panel` | `shop_panel.` | `shop_panel_` |
| `com_acme_shop` (when `shop_` taken) | `shop.` | `acme_shop_` or `shop_2_` |

---

## API reference

Validation lives in `Pinoox\Component\Package\PackageName`:

| Method | Purpose |
|--------|---------|
| `isValid($name)` | Check package format |
| `normalize($name)` / `canonical($name)` | Lowercase canonical form |
| `equals($a, $b)` | Case-insensitive comparison |
| `looksLike($value)` | CLI heuristic (package vs username) |
| `appSlug($package)` | Extract route slug segment |
| `suggestedTablePrefix($package)` | Same slug as routes — default DB prefix base |
| `tablePrefixFallbacks($package)` | Longer prefixes when slug collides |

See also [app.php manifest](./app-manifest.md) and [project structure](./structure.md).
