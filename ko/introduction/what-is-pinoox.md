# Pinoox란?

[← 색인으로 돌아가기](../README.md)

Pinoox는 HMVC 아키텍처와 **앱(app)** 개념을 기반으로 구축된 현대적인 오픈소스 PHP 프레임워크(3.x)입니다. 모듈형 웹 개발을 간단하게 만듭니다. 각 앱은 `apps/{package}/` 아래의 독립적인 MVC 단위이며, 공유 프레임워크 코어는 `vendor/pinoox/pincore/`에 위치합니다.

---

## 앱 중심 아키텍처

단일 Pinoox 설치 환경에서 여러 독립 앱이 나란히 실행됩니다:

```
{project_root}/
├── index.php              ← 웹 진입점
├── pinoox                 ← CLI 진입점
├── composer.json
├── vendor/pinoox/pincore/ ← 프레임워크 코어 (코어 변경 시에만 편집)
└── apps/
    ├── com_pinoox_manager/
    └── com_example_blog/  ← 여러분의 앱
```

- **프로젝트** — `index.php`와 `apps/`를 포함하는 폴더(폴더 이름은 중요하지 않음).
- **앱** — 자체 컨트롤러(Controller), 모델(Model), 라우트, 테마, 설정을 갖춘 완전한 모듈.
- **코어** — 공유 엔진(라우터, HTTP, 데이터베이스, Twig, CLI 등).

비즈니스 로직은 `vendor/pinoox/pincore/`가 아닌 `apps/`에 작성하세요.

---

## HTTP 요청 생명주기

```
브라우저 → index.php → 부트스트랩 (bootstrap)
      → 활성 앱 결정 (도메인 또는 URL 접두사)
      → app.php 및 routes/ 로드
      → Flows → Controller → Model (선택) → View 또는 JSON
```

---

## 앱 이름 규칙

권장 패키지 형식:

```
com_{vendor}_{name}
```

예: `com_acme_shop` — 폴더 이름, `app.php`의 `package` 값, 네임스페이스 세그먼트가 모두 일치해야 합니다.

---

## 적합한 사용 사례

- 각 섹션을 별도 앱으로 구성할 수 있는 다중 섹션 사이트 및 관리 패널
- 모듈을 독립적으로 개발, 테스트, 유지보수하려는 팀
- Composer와 통합 CLI(`php pinoox …`)를 사용하는 PHP 8.1+ 프로젝트

---

## 관련 문서

- [Pinoox 기능](./features-pinoox.md)
- [Pinoox 설치](../start/installing-pinoox.md)
- [첫 번째 앱 만들기](../start/your-first-app.md)
- [Notes API 실습 가이드](../examples/simple-api-app.md)
- [전화번호부 실습 가이드](../examples/phonebook-app.md)
- [문의 양식 실습 가이드](../examples/contact-form-app.md)
- [프로젝트 구조](../start/structure.md)

---

[← 색인으로 돌아가기](../README.md)
