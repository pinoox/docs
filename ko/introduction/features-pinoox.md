# Pinoox 기능

[← 색인으로 돌아가기](../README.md)

Pinoox 3.x는 모듈형 PHP 생태계를 위해 설계되었습니다. 하나의 공유 코어 위에 여러 독립 앱, CLI 스캐폴딩, HTTP·데이터베이스·테마·인증을 위한 내장 도구를 제공합니다.

---

## HMVC 아키텍처와 독립 앱

`apps/{package}/` 아래 각 앱은 완전한 MVC 구조를 갖습니다:

| 계층 | 예시 경로 |
|-------|--------------|
| Controller | `Controller/MainController.php` |
| Model | `Model/PostModel.php` |
| View (Twig) | `theme/default/home.twig` |
| Route | `routes/web.php`, `routes/actions.php` |
| Flow (미들웨어) | `Flow/AuthFlow.php` |

한 앱을 추가하거나 비활성화해도 다른 앱에는 영향을 주지 않습니다.

---

## CLI와 빠른 개발

프로젝트 루트에서:

```bash
composer install
php pinoox app:create com_acme_blog
php pinoox controller:create PostController com_acme_blog
php pinoox migrate
```

CLI는 표준 폴더 레이아웃, `app.php`, 초기 route 파일을 생성합니다.

---

## Routing과 Named Actions

URL 경로와 논리적 handler를 분리합니다:

```php
// routes/actions.php
action('welcome', [MainController::class, 'home']);

// routes/web.php
get('/', '@welcome')->name('home');
```

이 패턴은 리팩터링과 테스트를 더 쉽게 만듭니다.

---

## Flow (미들웨어)

요청이 Controller에 도달하기 전에 Flow가 실행됩니다 — 인증, 권한, 로깅 등:

```php
get('panel', '@dashboard')->flows(['auth'])->name('panel');
```

Flow 별칭은 `app.php`에 등록하세요.

---

## View와 테마

- Twig 템플릿은 `theme/{themeName}/`에 위치
- **`View::render()`**로 렌더링
- 테마에서 Vite로 SPA 지원 (Vue/React)

---

## Database와 Eloquent

- `DB` Portal을 통한 Query Builder와 Eloquent
- 각 앱의 `database/migrations/`에 migration과 seeder
- package 이름 기반 테이블 접두사 (예: `com_acme_blog_posts`)

---

## API와 JSON 응답

**`ApiController`**를 확장하고 표준 envelope을 사용합니다:

```php
return $this->ok($items);
return $this->fail('NOT_FOUND', 'Item not found.', status: 404);
```

---

## 국제화

`lang/{locale}/*.lang.php`의 번역 파일 — 다국어 앱에 적합합니다.

---

## 관련 문서

- [Pinoox란?](./what-is-pinoox.md)
- [Pinoox 설치](../start/installing-pinoox.md)
- [Router](../basic/routers.md)
- [Flow](../basic/flows.md)

---

[← 색인으로 돌아가기](../README.md)
