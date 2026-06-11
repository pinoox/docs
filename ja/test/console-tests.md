# Pinoox での Console テスト

[← 索引に戻る](../README.md)

Pinoox CLI コマンド（`php pinoox ...`）をテストするには、Pest テストで `Symfony\Component\Process\Process` を使用します。出力と終了コードをアサート — ターミナルテストの推奨アプローチです。

---

## 前提条件

Symfony Console Process はプロジェクト依存関係に既に含まれています。アプリまたはコアの `Feature` または `Unit` フォルダにテストを記述してください。

---

## migrate コマンドのテスト

```php
// apps/com_my_shop/tests/Feature/MigrateCommandTest.php

use Symfony\Component\Process\Process;

it('runs migrate for the app', function () {
    $root = dirname(__DIR__, 4);

    $process = new Process(
        ['php', 'pinoox', 'migrate', appPackage()],
        $root
    );

    $process->run();

    expect($process->isSuccessful())->toBeTrue()
        ->and($process->getOutput())->toContain('Migrated');
});
```

---

## カスタムアプリコマンドのテスト

アプリコマンドは `apps/{package}/Terminal/` にあり、`php pinoox` 経由で検出されます:

```php
it('runs custom report command', function () {
    $root = dirname(__DIR__, 4);

    $process = new Process(
        ['php', 'pinoox', 'report:daily', '-p', appPackage()],
        $root
    );

    $process->run();

    expect($process->getExitCode())->toBe(0);
});
```

---

## 失敗終了のテスト

```php
it('fails when package is missing', function () {
    $root = dirname(__DIR__, 4);

    $process = new Process(
        ['php', 'pinoox', 'migrate', 'com_nonexistent'],
        $root
    );

    $process->run();

    expect($process->isSuccessful())->toBeFalse();
});
```

---

## 対話式コマンド — 避ける

ユーザー入力を促すコマンドでは、テストで完全な引数を渡して対話実行を避けてください:

```bash
# ✅ テスト内
php pinoox migrate com_my_shop

# ❌ テスト内 — ユーザー選択を待つ
php pinoox migrate
```

---

## テストの実行

```bash
php pinoox test com_my_shop -f MigrateCommand
vendor/bin/pest --filter=MigrateCommand
```

---

## ヒント

1. `$root` をプロジェクトルート（`pinoox` と `index.php` がある場所）に向ける。
2. CI では migrate に長いタイムアウトを設定: `$process->setTimeout(120)`。
3. Command クラス内の純粋ロジックにはモック依存関係付き **Unit テスト** を使用。Process は E2E CLI 統合専用。

---

## 関連ドキュメント

- [テストはじめに](./getting-started.md)
- [Mocking](./mocking.md)
- [Migrations](../database/migrations.md)
- [Pinoox Baker（Pinker）](../advanced/pinker.md)
- [プロジェクト構造](../start/structure.md)

---

[← 索引に戻る](../README.md)
