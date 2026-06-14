# boot.php and events

[← 색인으로 돌아가기](../README.md)

`routes/` 외에 **`boot.php`**에서 route, API endpoint, Flow, schedule, listener를 등록할 수 있습니다 — **plugin**, micro-module, host app(예: manager) hook에 유용합니다.

각 app은 `apps/{package}/boot.php`를 둘 수 있습니다. 파일은 `AppRegister`를 받는 closure를 반환하며 요청 처리 **전**에 실행됩니다.

---

## 라이프사이클

```
HTTP request
  → BootPipeline (composer → loader → boot.global → app.boot → container → …)
  → AppBootstrap::ensure($package)
  → include boot.php → callable($register)
  → commit registries → integrate (flows, listeners, events)
  → router / API loaders apply registered entries
```

### pipeline 단계

| 단계 | 목적 |
|------|------|
| `boot.global` | 매 요청마다 `boot-global => true` app boot |
| `app.boot` | 활성 route app boot (`extends` extender 포함) |

### boot 이벤트

| 이름 | 시점 |
|------|------|
| `app.booting` / `app.booting.{package}` | commit 전 |
| `app.booted` / `app.booted.{package}` | integrate 후 |
| `app.routes` / `app.routes.{package}` | web route 적용 시 |
| `app.api` / `app.api.{package}` | API registry 구성 시 |

`boot.php`에서 listen:

```php
use Pinoox\Component\AppEvent\AppEventNames;

$register->listen(
    AppEventNames::package(AppEventNames::BOOTED, $register->package()),
    $listener,
);
```

### 코어 request 이벤트

매 HTTP 요청마다 프레임워크가 자동 dispatch (`AppCoreEventSubscriber`):

| 이름 | 시점 | package 변형 | 이름 채널 |
|------|------|--------------|-----------|
| `app.route.matched` | route match 후 | `app.route.matched.{package}` | `app.route.{routeName}` 또는 `app.api.{routeName}` |
| `app.controller` | controller 실행 전 | `app.controller.{package}` | `app.controller.{Class}.{method}` |
| `app.response` | response 전송 전 | `app.response.{package}` | — |
| `app.exception` | 처리되지 않은 exception | `app.exception.{package}` | — |
| `app.terminate` | response 전송 후 | `app.terminate.{package}` | — |

```php
use Pinoox\Component\AppEvent\AppEventNames;
use Pinoox\Component\AppEvent\AppRouteMatchedEvent;

$register->listen(AppEventNames::ROUTE_MATCHED, function (AppRouteMatchedEvent $event): void {
    // $event->request, $event->route, $event->routeName(), $event->isApi()
});

$register->listen(
    AppEventNames::route('app.run'),
    function (AppRouteMatchedEvent $event): void {},
);

$register->listen(
    AppEventNames::package(AppEventNames::CONTROLLER, $register->package()),
    $listener,
);
```

간단한 hook은 **watch** (`onRoute`, `onApi`, …), 전체 제어는 코어 이벤트 **listen**.

---

## 세 가지 앱 mode

| Mode | Config | Behavior |
|------|--------|----------|
| **Route only** | `router.routes` only | 앱 URL이 활성일 때 실행 |
| **Boot global** | `boot-global => true` | 모든 HTTP request에서 boot |
| **Boot + Route** | `boot.php` + routes | Default scaffold |

Host app의 plugin:

```php
'extends' => ['com_host_app'],
```

plugin은 host가 boot할 때만 boot (global보다 가벼움).

---

## boot용 `app.php` 키

`apps/{package}/app.php`의 이 키들은 **언제** `boot.php`가 실행되고 캐시되는지 제어합니다. boot 파이프라인 설정이며 `boot.php`를 대체하지 않습니다.

### boot 파일 (`boot`)

| 값 | 기본값 | 동작 |
|----|--------|------|
| `true` | 예 | 이 app boot 시 `boot.php` 실행 |
| `false` | | boot 없음 — route만 |
| `'path/custom.php'` | | app 루트 기준 다른 파일 |

파일은 **callable**을 반환해야 합니다. `true`인데 파일이 없으면 조용히 건너뜁니다.

### 전역 plugin (`boot-global`)

| 값 | 기본값 | 동작 |
|----|--------|------|
| `false` | 예 | 이 app이 활성일 때만 boot |
| `true` | | **모든 HTTP 요청**에서 boot |

### 호스트 plugin (`extends`)

| 값 | 기본값 | 동작 |
|----|--------|------|
| `[]` | 예 | 일반 app |
| `['com_host_app']` | | 호스트 활성 시 **먼저** boot |

### 추가 등록 (`startup`)

`app.php`의 optional callable. `boot.php` **이후** 같은 API로 실행.

### boot cache (`cache`)

opt-in: `cache.enabled` = `true`. 배포 후: `php pinoox cache:build {package}`.

### 빠른 선택

| 목표 | 설정 |
|------|------|
| 일반 app | `'boot' => true` |
| route만 | `'boot' => false` |
| 전역 plugin | `'boot-global' => true` |
| 호스트 plugin | `'extends' => ['com_host_app']` |

---

## 기본 boot.php

```php
<?php

use Pinoox\Component\AppEvent\AppRegister;
use Pinoox\Component\Http\Api\ApiResponse;

return function (AppRegister $register): void {
    $register->apiRoute([
        'method' => 'GET',
        'uri' => '/health',
        'action' => fn () => ApiResponse::success(['status' => 'ok']),
        'name' => 'health',
    ]);

    $register->when('com_host_app', function (AppRegister $host) {
        $host->apiRoute([
            'method' => 'GET',
            'uri' => '/acme/status',
            'action' => fn () => ApiResponse::success(['status' => 'ok']),
            'name' => 'acme.status',
            'flow' => ['host.auth'],
        ]);
    });
};
```

---

## AppRegister — 자주 쓰는 method

| Method | Purpose |
|--------|---------|
| `web(callable)` | builder로 route 등록 |
| `route([...])` | Single web route |
| `api([manifest])` | Full API manifest |
| `apiRoute([...])` | Single API endpoint |
| `action('name', handler)` | Named Action |
| `flowAlias(['auth' => AuthFlow::class])` | Flow alias |
| `schedule(callable)` | Scheduled task |
| `listen('event', listener)` | Event listener |
| `subscribe(SubscriberClass::class)` | Symfony subscriber |
| `when('com_host', fn)` | 다른 앱 boot 시 hook |

---

## Theme — context, 상속, boot hook

`apps/{package}/theme/{name}/` 폴더. **`app.php`**에서 active theme, **`boot.php`**에서 runtime hook.

### `app.php` 키

| 키 | 용도 |
|----|------|
| `theme` | active theme 폴더 |
| `theme-context` / `theme-contexts` | 여러 theme |
| `theme-extends` | 상속 |
| `path-theme` | custom 경로 |
| `frontend` | Vite profile, entry, manifest |

```php
'theme-context' => 'site',
'theme-contexts' => [
    'site'  => ['theme' => 'site'],
    'panel' => ['theme' => 'panel'],
    'kids'  => ['theme' => 'kids', 'extends' => 'site'],
],
'alias' => array_merge(
    ['auth' => AuthFlow::class],
    theme_flow_aliases(['site', 'panel', 'kids']),
),
```

Routes: `flows: ['auth', 'theme.panel']`. `theme/{name}/`: `theme.php`, Twig, `functions.php`, `frontend.config.php`, `src/` / `dist/`.

[Views](../basic/views.md), [Twig](../basic/templates.md), [app.php](../start/app-manifest.md) 참고.

### `boot.php`에서

**`onTheme`** 또는 **listen** / **watch**:

```php
use Pinoox\Component\AppEvent\AppWatchContext;
use Pinoox\Portal\View;

$register->onTheme('panel', function (AppWatchContext $ctx): void {
    View::set('layout', 'compact');
});
```

Controller: `View::changeTheme('panel')`, `ThemeContext::activate('panel')`, `within_theme(...)`.

```bash
php pinoox theme:frontend build {package}
php pinoox cache:build {package} --only=twig
```

---

## Event portal

```php
use Pinoox\Portal\Event;

Event::dispatch($event, OrderPlaced::NAME);
Event::listen(OrderPlaced::NAME, SendOrderEmail::class);
```

Controller에서 mail 분리는 [Email](./mail.md) 참조.

**Flow** = Controller 전 (미들웨어). **Event** = action 후 (side effect).

---

## Helper

```php
use Pinoox\Portal\AppBoot;

AppBoot::ensure();
AppBoot::booted('com_acme');
app_boot();
```

---

## Boot cache

`app.php`의 `cache.stores` 아래 `'boot' => true`가 Pinker로 boot를 bake — [Pinker](./pinker.md) 참조.

---

## 관련 문서

- [Schedule](./schedule.md)
- [Flows](../basic/flows.md)
- [Router](../basic/routers.md)
- [프로젝트 구조](../start/structure.md)

---

[← 색인으로 돌아가기](../README.md)
