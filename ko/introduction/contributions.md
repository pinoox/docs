# Pinoox에 기여하기

[← 색인으로 돌아가기](../README.md)

Pinoox는 오픈소스 프로젝트입니다. 버그 리포트부터 pull request까지 여러분의 기여는 프레임워크와 문서를 개선하는 데 도움이 됩니다.

---

## 기여 방법

| 유형 | 설명 |
|------|-------------|
| 버그 리포트 | 재현 단계가 포함된 GitHub Issue |
| 기능 요청 | 사용 사례를 설명하는 Issue |
| Pull Request | 적절한 저장소의 버그 수정 또는 기능 |
| Documentation | `docs/` 아래 파일 개선 (한국어 또는 English) |
| 오픈소스 앱 | 커뮤니티를 위한 Pinoox 앱 공개 |

---

## 버그 리포트

Issue를 열 때 다음을 포함하세요:

1. **제목** — 문제의 짧은 요약
2. **재현 단계** — 단계별 재현 방법
3. **기대 동작** vs **실제 동작**
4. **환경** — PHP 버전, Pinoox/pincore 버전, 운영체제
5. **샘플 코드** — 가능할 때

[Pinoox GitHub Issues](https://github.com/pinoox/pinoox/issues)

---

## Pull requests

### 저장소

- **pinoox/pinoox** — 샘플 프로젝트, 시스템 앱, launcher
- **pinoox/pincore** — 프레임워크 코어 (`vendor/pinoox/pincore/`)

코어 변경은 프로젝트의 로컬 `vendor/` 복사본만이 아니라 pincore로 보내세요.

### 브랜치 전략 (3.x)

- **버그 수정** → 현재 stable 브랜치 (예: `3.x`)
- **작고 호환 가능한 기능** → 동일 stable 브랜치
- **호환성을 깨는 변경 또는 주요 변경** → `master` / next-version 브랜치

### 코드 표준

- 코드 스타일: [PSR-12](https://www.php-fig.org/psr/psr-12/)
- autoloading: [PSR-4](https://www.php-fig.org/psr/psr-4/)
- PHP 8.2+
- 명확하고 명령형 commit 메시지 (예: `Fix route validation for missing actions`)

---

## 보안

보안 취약점은 **비공개로** 보고하세요:

`security@pinoox.com`

---

## 연락처

- Support: `support@pinoox.com`
- [GitHub repository](https://github.com/pinoox/pinoox)

---

## 관련 문서

- [Pinoox란?](./what-is-pinoox.md)
- [Pinoox 기능](./features-pinoox.md)

---

[← 색인으로 돌아가기](../README.md)
