# Pinoox 설치

[← 색인으로 돌아가기](../README.md)

이 가이드는 Pinoox 3.x 설치를 다룹니다. 시작하는 방법은 두 가지입니다:

| 경로 | 적합한 경우 |
|-------|----------|
| **A. [Pinx CLI](./pinx-cli.md)로 단일 앱** | 하나의 앱 구축 — 가장 빠른 시작, manager UI 불필요 |
| **B. 전체 플랫폼 (classic)** | 그래픽 installer와 manager로 여러 앱 호스팅 |

---

## 요구 사항

| 도구 | 버전 |
|------|---------|
| PHP | 8.2 이상 (ext-mysqli, ext-zip 포함) |
| MySQL / MariaDB | 5.7+ |
| Composer | 2.x |
| Node.js (선택) | 18+ — frontend theme 빌드에만 필요 |

---

## 경로 A — Pinx CLI로 단일 앱

[Pinx CLI](./pinx-cli.md)를 한 번 설치하고, 새 앱을 만든 뒤 실행하세요:

```bash
composer global require pinoox/pinx-cli

pinx new my-shop              # suggests com_my_shop — confirm or edit in the wizard
cd my-shop
cp .env.example .env          # set DB_* if you use a database
pinx setup                    # migrate platform + app, run seeders
pinx dev                      # http://127.0.0.1:8000
```

전역 설치 없이 프로젝트 템플릿으로:

```bash
composer create-project pinoox/app my-shop
cd my-shop
cp .env.example .env
pinx setup
pinx dev
```

PHP, env, DB, 빌드 준비 상태를 확인하려면 언제든 `pinx doctor`를 실행하세요. 일상 workflow와 command reference는 전체 [Pinx CLI 가이드](./pinx-cli.md)를 참조하세요.

---

## 경로 B — 전체 플랫폼 (classic)

### 1. 프로젝트 받기

```bash
git clone https://github.com/pinoox/pinoox.git
cd pinoox
composer install
```

또는 [GitHub](https://github.com/pinoox/pinoox)에서 최신 릴리스를 다운로드하고 압축을 풀어 `composer install`을 실행하세요.

---

### 2. 웹 서버에 배치

프로젝트 폴더를 document root에 넣으세요:

| 환경 | 예시 경로 |
|-------------|--------------|
| MAMP | `C:/MAMP/htdocs/pinoox` |
| XAMPP | `C:/xampp/htdocs/pinoox` |
| WAMP | `C:/wamp64/www/pinoox` |

document root를 **`public` 하위 폴더가 아닌** 프로젝트 루트(`index.php`가 있는 폴더)로 설정하세요.

---

### 3. Database 생성

```sql
CREATE DATABASE pinoox_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

---

### 4. Installer 실행

브라우저를 엽니다:

```
http://localhost/pinoox
```

시스템 앱 `com_pinoox_installer`가 실행됩니다. GUI 단계:

1. PHP 요구 사항 확인
2. 라이선스 동의
3. Database 자격 증명 입력
4. Admin 계정 생성
5. 설치 완료

---

### 5. 설치 후

주요 레이아웃:

```
pinoox/
├── index.php
├── pinoox              ← CLI
├── apps/               ← apps
├── vendor/pinoox/pincore/  ← core
└── config/             ← project config
```

첫 번째 앱 생성:

```bash
php pinoox app:create com_acme_blog
```

---

## 빠른 문제 해결

| 문제 | 해결 |
|---------|-----|
| Blank page | `composer install` 실행 및 PHP error log 확인 |
| 404 on sub-routes | mod_rewrite / `.htaccess` 활성화 |
| Missing extension error | php.ini에서 ext-mysqli, ext-zip 활성화 |
| Installer does not open | document root 및 runtime 폴더 쓰기 권한 확인 |

---

## 관련 문서

- [Pinx CLI (단일 앱)](./pinx-cli.md)
- [첫 번째 앱 만들기](./your-first-app.md)
- [프로젝트 구조](./structure.md)
- [Pinoox란?](../introduction/what-is-pinoox.md)

---

[← 색인으로 돌아가기](../README.md)
