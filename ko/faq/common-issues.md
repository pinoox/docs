# 자주 발생하는 문제

[← 색인으로 돌아가기](../README.md)

Pinoox 설치, 실행, 개발 중 자주 발생하는 오류에 대한 실용적인 해결 방법입니다. 각 섹션은 **하나의 접근 방법**을 권장합니다.

---

## `composer install` 실패

**증상:** extension 누락, PHP 버전 부족, 네트워크 timeout.

**해결:**

1. PHP 8.2+와 `mysqli`, `zip`, `mbstring`, `json` extension을 활성화하세요.
2. 설치 전 platform check를 실행하세요:

```bash
php launcher/check.php
```

3. 다시 설치하세요:

```bash
composer install --no-interaction
```

공유 호스팅에서 PATH에 `composer`가 없으면 로컬에서 vendor를 빌드한 뒤 업로드하세요.

---

## 권한 오류 (파일 접근)

**증상:** `cache/`, `storage/`, `pinker/`에 쓸 수 없음.

**해결 (Linux/macOS):**

```bash
chmod -R 775 cache storage pinker apps
chown -R www-data:www-data cache storage pinker
```

웹 서버 사용자(예: `www-data` 또는 `apache`)가 쓰기 가능 폴더에 쓸 수 있어야 합니다. Windows/MAMP에서는 프로젝트 폴더를 `Program Files` 밖에 두세요.

---

## `.htaccess` / rewrite 미작동

**증상:** `index.php`를 제외한 모든 URL에서 404; 브라우저에서 API가 JSON을 반환하지 않음.

**해결:**

1. Apache `mod_rewrite`를 활성화하세요.
2. DocumentRoot에 `AllowOverride All`을 설정하세요.
3. 프로젝트 루트에 `.htaccess`가 있는지 확인하세요.
4. 빠른 테스트: `http://localhost/pinoox/api/v1/ping` — JSON이 보이면 rewrite가 작동합니다.

nginx에서는 `.htaccess` 대신 server config에 `try_files`와 `index.php` 규칙을 작성하세요.

---

## Database 연결 실패

**증상:** `SQLSTATE[HY000] [2002] Connection refused` 또는 access denied.

**해결:**

1. MySQL/MariaDB가 실행 중인지 확인하세요.
2. `config/database.config.php` 또는 `.env`의 값을 확인하세요:

```env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pinoox_db
DB_USERNAME=root
DB_PASSWORD=
```

3. 사전에 database를 생성하세요 (`CREATE DATABASE ... utf8mb4`).
4. cPanel에서는 host가 `localhost`가 아닐 수 있습니다 — 패널의 hostname을 사용하세요.

---

## Pinker rebuild 필요

**증상:** config 또는 route가 오래됨; `app.php` 변경이 적용되지 않음.

**해결:**

```bash
php pinoox pinker:rebuild com_my_shop
# or alias:
php pinoox bake com_my_shop

# all apps:
php pinoox pinker:rebuild all
```

route, config 변경 후 또는 production 배포 후에는 보통 rebuild가 필요합니다.

---

## Route not found (endpoint 404)

**증상:** 코드에 route가 정의되어 있지만 404가 발생.

**해결:**

1. route 파일이 `apps/{package}/routes/`에 있고 `app.php` → `router.routes`에 나열되어 있는지 확인하세요.
2. URL을 앱 접두사(`app:router`)와 일치시키세요:

```bash
php pinoox app:router
php pinoox route:actions com_my_shop
```

3. Pinker rebuild를 실행하세요 (위 참조).
4. 올바른 HTTP method를 사용하세요 (`GET` vs `POST`).

---

## 404 — 앱이 resolve되지 않음

**증상:** 기본 페이지 또는 404; 잘못된 앱이 로드됨.

**해결:**

1. path/host 매핑을 확인하세요:

```bash
php pinoox app:resolve --host=localhost --path=/shop
php pinoox app:domain
php pinoox app:router
```

2. `config/domain.config.php`(또는 관련 map)에서 host와 path를 올바르게 설정하세요.
3. 앱의 `app.php`에서 `'enable' => true`인지 확인하세요.
4. 앱 폴더 이름은 `app.php`의 `'package'`와 같아야 합니다 (예: `com_my_shop`).

---

## 테스트 실패

```bash
php pinoox test com_my_shop
```

- 별도 DB를 가진 `.env.testing`
- migration 실행: `php pinoox migrate com_my_shop`
- `fakeApp()` 후 → `deleteFakeApp()`

자세한 내용: [테스트 시작하기](../test/getting-started.md)

---

## 관련 문서

- [Pinoox 설치](../start/installing-pinoox.md)
- [프로젝트 구조](../start/structure.md)
- [Router](../basic/routers.md)
- [Config](../basic/config.md)
- [Pinoox Baker (Pinker)](../advanced/pinker.md)
- [Database 시작하기](../database/getting-started.md)
- [지원 문의](./contact-support.md)

---

[← 색인으로 돌아가기](../README.md)
