# Pinoox CLI 참조

[← 색인으로 돌아가기](../README.md)

모든 command는 **프로젝트 루트**에서 실행하세요:

```bash
php pinoox
php pinoox list
php pinoox help migrate
```

package가 필요한데 생략하면 Pinoox가 대화형 picker를 표시합니다.

> **단일 앱** 프로젝트는 독립 [Pinx CLI](./pinx-cli.md) (`pinx dev`, `pinx setup`, `pinx build`, …)를 사용하세요.

---

## 자주 쓰는 alias

| Alias | Command |
|-------|---------|
| `mg` | `migrate` |
| `mg:create` | `migrate:create` |
| `patch` | `patch:run` |
| `seed` | `seeder:run` |
| `cb` | `cache:build` |
| `cc` | `cache:clear` |
| `bake` | `pinker:rebuild` |
| `apps` | `app:list` |
| `make:app` | `app:create` |
| `router` | `app:router` |
| `routes` | `route:actions` |

---

## Apps

| Command | Purpose |
|---------|---------|
| `app:create {package}` | 앱 스캐폴딩 (`--simple`, `--stack`, `--profile`) |
| `app:list` | 앱 목록 |
| `app:delete` | 앱 제거 |
| `app:router set /path {package}` | URL 매핑 |
| `app:domain` | Host → app map |
| `app:resolve` | 활성 앱 디버그 |

---

## Scaffolding

| Command | Output |
|---------|--------|
| `controller:create` | `Controller/` |
| `model:create` | `Model/` |
| `portal:create` | `Portal/` |
| `form-request:create` | FormRequest class |
| `seeder:create` | `database/seed/` |
| `test:create` | Pest file |
| `theme:frontend` | Frontend tooling (Vue/React/Twig) |

---

## Database

| Command | Purpose |
|---------|---------|
| `migrate {package}` | migration 실행 (app, `platform`, `pincore`) |
| `migrate:create` | 새 migration 파일 |
| `migrate:status` / `migrate:rollback` | 상태 / rollback |
| `seeder:run` | seeder 실행 |
| `patch:create` / `patch:run` / `patch:status` / `patch:rollback` | [Patches](../database/patches.md) |
| `query` | Raw SQL (debug) |

---

## Cache & Pinker

| Command | Purpose |
|---------|---------|
| `cache:build` / `cache:clear` | Runtime cache |
| `pinker:status` / `pinker:rebuild` / `pinker:diff` / `pinker:clear` | [Pinker](../advanced/pinker.md) |
| `reset` | Pinker + config reset |

---

## Schedule

| Command | Purpose |
|---------|---------|
| `schedule:list` | cron task 목록 |
| `schedule:run` | due task 실행 |

[Schedule](../advanced/schedule.md) 참조.

---

## Router

| Command | Purpose |
|---------|---------|
| `route:actions {package}` | Named Actions 목록 |

---

## Pinx packaging

| Command | Purpose |
|---------|---------|
| `pinx:build` | `.pinx` package 빌드 |
| `pinx:install` | package 설치 |
| `pinx:info` | Metadata |
| `wizard:list` / `wizard:install` | Install wizard |

---

## Development

| Command | Purpose |
|---------|---------|
| `test` | Pest tests |
| `serve` | Built-in dev server |
| `log:view` / `log:clear` | Logs |
| `deps` | Composer/npm across apps |
| `version` / `mode:show` | Version / runtime mode |

---

## Package argument

| Value | Meaning |
|-------|---------|
| `com_my_shop` | Specific app |
| `platform` | Platform migrations/patches/seeders |
| `pincore` | Framework core |
| `all` | All apps (cache/pinker) |

---

## 관련 문서

- [첫 번째 앱 만들기](./your-first-app.md)
- [Migrations](../database/migrations.md)
- [Patches](../database/patches.md)

---

[← 색인으로 돌아가기](../README.md)
