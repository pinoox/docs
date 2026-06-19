# 지원 문의

[← 색인으로 돌아가기](../README.md)

[자주 발생하는 문제](./common-issues.md)를 검토한 뒤에도 막히는 문제가 있으면 아래 공식 채널을 이용하세요. 지원에 문의하기 전에 Pinoox 버전, PHP 버전, 오류 메시지, 재현 단계를 준비하세요.

---

## 일반 지원

**Email:** [support@pinoox.com](mailto:support@pinoox.com)

다음에 적합합니다:

- 설치 및 배포 질문
- 예상치 못한 프레임워크 동작
- HMVC 및 앱 아키텍처 안내

이메일에 다음을 포함하세요:

1. Pinoox 버전 (`composer.json` → `version` 또는 git tag)
2. PHP 버전 (`php -v`)
3. OS 및 웹 서버 (Apache/nginx, MAMP, cPanel, …)
4. 전체 오류 텍스트 또는 스크린샷
5. 최소 재현 단계

---

## GitHub Issues

확인된 버그, 기능 요청, 공개 기술 논의:

**Repository:** [github.com/pinoox/pinoox](https://github.com/pinoox/pinoox/issues)

새 issue를 열기 전에:

- 중복 issue 검색
- 최신 stable/beta 릴리스에서 테스트
- `pincore` 관련이면 `pinoox/pincore` 패키지도 확인

권장 issue 템플릿:

```markdown
## Environment
- Pinoox: 3.1-beta
- PHP: 8.2.x
- OS: Windows / Linux

## Expected
...

## Actual
...

## Steps to reproduce
1. ...
2. ...
```

---

## 보안 리포트

**Email:** [security@pinoox.com](mailto:security@pinoox.com)

보안 취약점 전용 — SQL injection, auth bypass, RCE, secret exposure.

- 패치가 준비될 때까지 세부 내용을 공개(GitHub issue)하지 마세요
- 가능하면 최소 PoC와 영향 설명을 포함하세요

---

## 코드 기여

PR 및 프레임워크 개발:

- [기여하기](../introduction/contributions.md)
- Fork → branch → test (`php pinoox test`) → Pull Request

---

## 셀프 헬프 자료

| 주제 | 문서 |
|-------|-----|
| Installation | [installing-pinoox.md](../start/installing-pinoox.md) |
| First app | [your-first-app.md](../start/your-first-app.md) |
| Common issues | [common-issues.md](./common-issues.md) |
| Testing | [getting-started.md](../test/getting-started.md) |

**Website:** [pinoox.com](https://www.pinoox.com/)

---

## 관련 문서

- [자주 발생하는 문제](./common-issues.md)
- [Pinoox란?](../introduction/what-is-pinoox.md)
- [기여하기](../introduction/contributions.md)
- [Pinoox 설치](../start/installing-pinoox.md)

---

[← 색인으로 돌아가기](../README.md)
