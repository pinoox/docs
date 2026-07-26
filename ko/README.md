# Pinoox 문서

Pinoox 플랫폼(PHP 8.2+, HMVC 아키텍처)에서 앱을 구축하기 위한 공식 개발자 문서입니다.

각 가이드는 실용 예제와 함께 **하나의 권장 방법**을 설명합니다. 아래 섹션을 선택하거나 주제별로 찾아보세요.

**언어:** [English](../en/README.md) · [فارسی](../fa/README.md) · [العربية](../ar/README.md) · [中文](../zh/README.md) · [日本語](../ja/README.md) · [한국어](./README.md) · [Türkçe](../tr/README.md) · [Español](../es/README.md) · [हिन्दी](../hi/README.md) · [Français](../fr/README.md) · [Русский](../ru/README.md) · [Português](../pt/README.md) · [Deutsch](../de/README.md)

---

### 소개

#### [Pinoox란?](./introduction/what-is-pinoox.md)
#### [Pinoox 기능](./introduction/features-pinoox.md)
#### [Pinoox에 기여하기](./introduction/contributions.md)

### 시작하기

#### [Pinoox 설치](./start/installing-pinoox.md)
#### [첫 번째 앱 만들기](./start/your-first-app.md)
#### [프로젝트 구조](./start/structure.md)
#### [Pinoox CLI 참조](./start/cli-reference.md)
#### [Pinx CLI (단일 앱 프로젝트)](./start/pinx-cli.md)
#### [app.php 매니페스트 참조](./start/app-manifest.md)

### 실습 워크스루

#### [워크스루: Notes API 앱](./examples/simple-api-app.md)
#### [워크스루: Phonebook 웹 앱](./examples/phonebook-app.md)
#### [워크스루: Contact form 앱](./examples/contact-form-app.md)
#### [워크스루: Simple blog 앱](./examples/blog-app.md)
#### [워크스루: Task board (Todo)](./examples/task-board-app.md)
#### [워크스루: Image gallery 앱](./examples/gallery-app.md)
#### [워크스루: Vue SPA 패널](./examples/vue-spa-app.md)
#### [워크스루: React SPA 패널](./examples/react-spa-app.md)
#### [워크스루: Vite hybrid (Twig + JS widget)](./examples/vite-hybrid-app.md)

### 핵심 개념

#### [Router](./basic/routers.md)
#### [Controller](./basic/controllers.md)
#### [Flow (미들웨어)](./basic/flows.md)
#### [HTTP Request](./basic/requests.md)
#### [HTTP Response](./basic/responses.md)
#### [URL 및 링크 생성](./basic/url.md)
#### [File Path](./basic/path.md)
#### [Validation](./basic/validation.md)
#### [Views](./basic/views.md)
#### [Twig Templates](./basic/templates.md)
#### [Portal (Facade)](./basic/portal.md)
#### [Config](./basic/config.md)
#### [Language and Translation](./basic/language.md)

### 고급 주제

#### [Pinker and Cache](./advanced/pinker.md)
#### [Patches (data updates)](./advanced/patches.md)

#### [App Services (Component + Portal)](./advanced/services.md)
#### [Global Helpers](./advanced/helpers.md)
#### [Sending Email](./advanced/mail.md)
#### [HTTP Client](./advanced/http-client.md)
#### [User Management](./advanced/user-management.md)
#### [File Management](./advanced/file-management.md)
#### [Pinion 프로토콜](./advanced/pinion.md)
#### [Token Management](./advanced/token-management.md)
#### [Access & permissions](./advanced/access-permissions.md)
#### [Transport (shared resources)](./advanced/transport.md)
#### [boot.php and events](./advanced/boot-and-events.md)
#### [Scheduling (cron)](./advanced/schedule.md)

### Database

#### [Database 시작하기](./database/getting-started.md)
#### [Query Builder](./database/query-builder.md)
#### [Pagination](./database/pagination.md)
#### [Migrations](./database/migrations.md)

### Eloquent ORM

#### [Eloquent ORM 시작하기](./eloquent-orm/getting-started.md)
#### [Eloquent Relationships](./eloquent-orm/relationships.md)
#### [Eloquent Collections](./eloquent-orm/collections.md)
#### [Mutators and Casts](./eloquent-orm/mutators-casts.md)
#### [API Resources](./eloquent-orm/api-resources.md)
#### [Model Serialization](./eloquent-orm/serialization.md)
#### [Test Data — Seeders](./eloquent-orm/factories.md)

### Testing

#### [Pinoox 테스트 시작하기](./test/getting-started.md)
#### [Pinoox HTTP 테스트](./test/http-tests.md)
#### [Pinoox Console 테스트](./test/console-tests.md)
#### [Pinoox Browser (HTML) 테스트](./test/browser-tests.md)
#### [Pinoox Database 테스트](./test/database.md)
#### [Pinoox Serialization 테스트](./test/serialization.md)
#### [Pinoox Mocking](./test/mocking.md)

### FAQ

#### [자주 발생하는 문제](./faq/common-issues.md)
#### [지원 문의](./faq/contact-support.md)

---

### Source
**예제 소스:** [docs/source/](../source/) — 모든 워크스루의 전체 코드

기본을 읽은 뒤 실습 코드가 필요할 때 단계별 실제 앱 가이드를 따르세요.

---

### 문서 읽는 방법

1. Pinoox가 처음이면 **소개**와 **시작하기**부터.
2. **실습 워크스루** — JSON API와 간단한 웹사이트를 단계별로 구축.
3. route, Controller, view를 만들며 **핵심 개념** 읽기.
4. persistence 추가 시 **Database**와 **Eloquent ORM** 사용.
5. auth, file, Pinker, 공유 service는 **고급 주제** 참조.
6. production 배포 전 **Testing** 활용.

모든 앱 코드는 `apps/{package}/` 아래. 프레임워크 코어는 `vendor/pinoox/pincore/` — 앱 비즈니스 로직은 여기에 두지 마세요.
