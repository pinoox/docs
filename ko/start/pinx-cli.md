# Pinx CLI (단일 앱 프로젝트)

[← 색인으로 돌아가기](../README.md)

**[Pinx CLI](https://github.com/pinoox/pinx-cli)**는 **단일 앱** Pinoox 프로젝트용 개발자 CLI입니다 — multi-app manager 없이 스캐폴딩, 실행, migrate, 빌드, `.pinx` package 배포를 수행합니다.

`pinoox/pincore`와 `pinoox/app` 템플릿 위에 구축됩니다. 프로젝트 루트 **가** 앱입니다: 하나의 `app.php`, 하나의 package, 하나의 workflow.

> classic multi-app 플랫폼 설치는 대신 [`php pinoox`](./cli-reference.md)를 사용하세요.

---

## 빠른 시작

Pinx를 한 번 설치하고, 새 앱을 만든 뒤 실행하세요:

```bash
composer global require pinoox/pinx-cli

pinx new my-shop              # suggests com_my_shop — confirm or edit in the wizard
cd my-shop
cp .env.example .env          # set DB_* if you use a database
pinx setup                    # migrate platform + app, run seeders
pinx dev                      # http://127.0.0.1:8000
```

`pinx`를 찾을 수 없으면 Composer global `bin`을 `PATH`에 추가하세요:

- Linux / macOS: `~/.composer/vendor/bin` 또는 `~/.config/composer/vendor/bin`
- Windows: `%APPDATA%\Composer\vendor\bin`

| Step | What it does |
|------|--------------|
| `composer global require` | 머신에 `pinx` command 설치 |
| `pinx new my-shop` | `pinoox/app`에서 스캐폴딩; wizard가 3-part package 제안 (예: `com_my_shop`) |
| `.env` | Database 및 프로젝트 경로 — `.env.example`에서 복사 |
| `pinx setup` | 한 번에: platform migrations → app migrations → seeders |
| `pinx dev` | PHP dev server; frontend stack이 설정되면 Vite도 시작 |

Package 이름은 `com_{vendor}_{name}` 형식 — 예: `com_acme_shop`, `ir_yekdo_app`. 이미 빈 폴더 안에 있나요? `pinx new` 대신 `pinx init`을 사용하세요.

**`setup` 전 선택 확인:** `pinx doctor`가 PHP, layout, env, DB, 빌드 준비 상태를 보고합니다.

---

## 대안: `composer create-project`

전역 설치 없음 — 템플릿에 프로젝트 내부 `bin/pinx`가 포함됩니다:

```bash
composer create-project pinoox/app my-shop
cd my-shop
cp .env.example .env
pinx setup
pinx dev
```

---

## 단일 앱의 차이점

Classic Pinoox 설치는 `apps/` 아래 여러 앱을 두고 runtime에 하나를 선택합니다. **단일 앱**은 이를 단순화합니다:

- 프로젝트 루트의 `app.php`에 package identity와 pinx 설정
- `Controller/`, `Model/`, `routes/`, `theme/`가 루트에 위치 — `apps/{package}/` 안이 아님
- `platform/`에 로컬 routing과 launcher config (`.pinx` 빌드에서 제외)
- Pinx는 항상 **여러분의** 앱을 대상 — package picker, manager UI 없음

```
my-shop/                    ← project root = app root
├── app.php                 ← package, version, pinx.sign, frontend.stack
├── Controller/ Model/ routes/ theme/
├── platform/               ← dev host + deploy layer (local only)
├── bin/pinx                ← project-local CLI entry
└── vendor/pinoox/pincore   ← framework
```

---

## 설치 옵션

| Where | How | When to use |
|-------|-----|-------------|
| **Global** | `composer global require pinoox/pinx-cli` | 권장 — 어디서든 `pinx new`, `pinx init` |
| **Per project** | `pinoox/app`의 `bin/pinx` | `composer create-project` 후 — 전역 설치 불필요 |

```bash
pinx -v          # CLI version (e.g. pinx-cli 1.1.7)
pinx list        # grouped command overview
pinx help setup  # detail for one command
```

---

## 일상 workflow

```bash
pinx dev                    # local server (+ Vite when app.php → frontend.stack is set)
pinx dev --open             # open browser after start
pinx dev --no-frontend      # PHP only

pinx migrate                # run app migrations (--platform runs platform first)
pinx migrate:st             # migration status
pinx migrate:cr create_products_table

pinx make controller ProductController
pinx make model ProductModel
pinx make migration create_products_table
pinx make portal ShopService

pinx routes                 # list named actions (--validate, --json)
pinx test                   # run app tests (Pest)
```

**Frontend** (`theme/`가 Vue/React + Vite 사용 시):

```bash
pinx fe:info                # stack, npm scripts, paths
pinx fe:i                   # npm install
pinx fe:d                   # Vite dev server
pinx fe:b                   # production build
pinx fe:sc --stack=vue      # scaffold starter files
```

**Dependencies:**

```bash
pinx deps:st                # Composer + npm status
pinx deps:i                 # install all
pinx deps:up                # update all
```

**Pinker** (build cache):

```bash
pinx pinker:st              # cache vs source
pinx pinker:rb              # rebuild
pinx pinker:df              # diff
```

---

## Production 배포

전체 Pinoox 플랫폼(Manager → Applications)에 설치할 `.pinx` package 빌드:

```bash
pinx build                  # → export/*.pinx
pinx build -o /tmp/shop.pinx
pinx release --bump=patch   # bump version in app.php + build
pinx release --sign         # sign when key is configured in app.php → pinx.sign
```

`pinx build`는 합리적인 기본값 적용 (`vendor/`, `bin/`, `.env`, `platform/`, dev tooling 제외). 필요할 때만 `app.php`에서 override:

```php
'build' => [
    'exclude' => ['my-private-notes/'],
    'composer' => false,
],
'pinx' => [
    'sign' => [
        'enabled' => false,
        'key' => null,
        'key_id' => null,
    ],
],
```

---

## `pinx doctor`

Doctor는 구조화된 진단을 실행하고 실패 시 fix command를 제안합니다:

| Group | Checks |
|-------|--------|
| **Project** | `app.php`, package identity, `platform/` layout |
| **Runtime** | PHP version (≥ 8.1), extensions, writable paths |
| **Dependencies** | Composer vendor, optional Node/npm |
| **Environment** | `.env` presence and key variables |
| **Database** | Connection (skippable with `--skip-db`) |
| **Frontend** | Theme stack, `package.json` (skippable with `--skip-frontend`) |
| **Build** | Export readiness, icon, version fields |

```bash
pinx doctor
pinx doctor --skip-db
pinx doctor --json          # CI-friendly report
pinx doctor --no-fixes      # hide suggested commands
```

---

## Command reference

섹션별 개요는 `pinx list`를 실행하세요. shorthand alias는 대괄호에 표시됩니다.

### Project

| Command | Aliases | Description |
|---------|---------|-------------|
| `new` | — | `pinoox/app`에서 스캐폴딩 (wizard 또는 flags) |
| `init` | — | 현재 디렉터리 초기화 (`--force`로 덮어쓰기) |
| `setup` | — | DB: migrate platform + app, then seed |
| `doctor` | `dr` | Health check — `--json`, `--skip-db`, `--skip-frontend` |
| `info` | `inf` | `app.php`의 metadata 표시 |

### Development

| Command | Description |
|---------|-------------|
| `dev` | Dev server; `frontend.stack`이 vue/react이면 Vite |

### Database

| Command | Aliases | Description |
|---------|---------|-------------|
| `migrate:run` | `migrate` | App migrations 실행 (`--platform`이면 platform 먼저) |
| `migrate:status` | `migrate:st` | Migration status |
| `migrate:rollback` | `migrate:rb` | Last batch rollback (`--ignore-fk`) |
| `migrate:create <name>` | `migrate:cr` | Migration 파일 생성 |
| `migrate:platform` | `migrate:pl` | Platform migrations만 |
| `seeder:run` | `seed` | Seeders 실행 (`-c` class) |

### Patches

| Command | Aliases | Description |
|---------|---------|-------------|
| `patch:run` | `patch` | Pending patches 실행 |
| `patch:status` | `patch:st` | Patch status |
| `patch:rollback` | `patch:rb` | Last patch batch rollback |

### Build & release

| Command | Aliases | Description |
|---------|---------|-------------|
| `build` | `bld` | `.pinx` package 빌드 |
| `release` | `rel` | Version bump + build (`--bump`, `--sign`) |

### Scaffolding

| Command | Aliases | Description |
|---------|---------|-------------|
| `make <type> <name>` | `mk` | controller, model, migration, patch, portal, form-request, seeder, test |

### Routes

| Command | Description |
|---------|-------------|
| `route:actions` / `routes` | Named actions 목록 (`--validate`, `--json`) |

### Dependencies

| Command | Aliases | Description |
|---------|---------|-------------|
| `deps:status` | `deps:st` | Composer + npm status |
| `deps:install` | `deps:i` | Dependencies 설치 |
| `deps:update` | `deps:up` | Dependencies 업데이트 |

### Frontend

| Command | Aliases | Description |
|---------|---------|-------------|
| `fe:info` | `fe:inf` | Theme stack and npm scripts |
| `fe:install` | `fe:i` | npm install |
| `fe:build` | `fe:b` | Production build |
| `fe:dev` | `fe:d` | Vite dev server |
| `fe:scaffold` | `fe:sc` | Starter files (`--stack=vue\|react\|twig`) |

### Schedule

| Command | Aliases | Description |
|---------|---------|-------------|
| `schedule:list` | `sched:ls` | `schedule.php`의 cron tasks 목록 |
| `schedule:run` | `sched:run` | Due tasks 실행 (`--dry-run`) |

### Pinker

| Command | Aliases | Description |
|---------|---------|-------------|
| `pinker:status` | `pinker:st` | Cache vs source |
| `pinker:rebuild` | `pinker:rb` | Cache rebuild |
| `pinker:diff` | `pinker:df` | Differences 표시 |
| `pinker:clear` | `pinker:cl` | Cache 지우기 |
| `pinker:overrides` | `pinker:ov` | Overrides 목록 |

### Quality & docs

| Command | Description |
|---------|-------------|
| `test` / `pest` | App tests 실행 (`--unit`, `--feature`) |
| `api:docs` | REST API documentation |
| `graphql:docs` | GraphQL schema documentation |

### Meta

| Command | Aliases | Description |
|---------|---------|-------------|
| `list` | — | Grouped command overview |
| `version` | `ver` | CLI version |

---

## App detection

Pinx는 현재 작업 디렉터리에서 위로 올라가며 유효한 단일 앱 프로젝트를 찾습니다:

1. `app.php`가 존재하고 비어 있지 않은 `package` key를 가진 array를 반환
2. `composer.json`에 `pinoox/pincore`가 required이거나 `vendor/pinoox/pincore`가 존재

환경 변수로 감지된 package를 override:

| Variable | Purpose |
|----------|---------|
| `PINX_PACKAGE` | CLI target package 강제 |
| `PINOOX_DEV_APP` | `PINX_PACKAGE` alias |
| `PINX_DEV=1` | Dev mode (pinx가 pincore에 위임할 때 자동 설정) |

---

## 요구 사항

- **PHP** ≥ 8.1 (`pinoox/pincore`가 요구하는 extensions 포함)
- **Composer** 2.x
- **Node.js** + npm — Vite/Vue/React frontend 사용 시에만
- **Database** — MySQL/MariaDB 또는 `.env`가 설정한 DB (static/Twig-only 앱에서는 선택)

---

## 관련 문서

- [Pinoox 설치](./installing-pinoox.md)
- [Pinoox CLI 참조 (multi-app)](./cli-reference.md)
- [첫 번째 앱 만들기](./your-first-app.md)
- [app.php manifest](./app-manifest.md)

---

[← 색인으로 돌아가기](../README.md)
