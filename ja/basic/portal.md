# Portal（Facade）

[← 索引に戻る](../README.md)

Pinoox 3.x では Portal はコアサービスへの静的ゲートウェイです — View、DB、Lang などへのシンプルなアクセスのための **Facade** パターン。日常業務では **`Pinoox\Portal\*`** を使用し、独自のアプリサービスにはアプリ配下に Portal を作成します。

---

## コア Portal（よく使う）

```php
use Pinoox\Portal\View;
use Pinoox\Portal\Url;
use Pinoox\Portal\Path;
use Pinoox\Portal\Lang;
use Pinoox\Portal\Validation;
use Pinoox\Portal\Database\DB;

View::render('home', $data);
Url::link('products');
Url::forApp('com_acme_shop');
Path::get('storage/logs');
Lang::get('welcome.title');
config('payment.merchant_id');   // ヘルパー → Config Portal
Validation::validate($data, $rules);
DB::table('users')->get();
```

---

## Portal を使う理由

- コンテナを手動解決せず、短く読みやすいコード
- Controller、Flow、Component で 1 つの安定したエントリーポイント
- Portal クラスの `@method` による IDE オートコンプリート

---

## アプリサービス用 Portal

### 1. Component を作成

```php
// apps/com_acme_shop/Component/PriceCalculator.php
namespace App\com_acme_shop\Component;

class PriceCalculator
{
    public function withTax(float $price, float $rate = 0.09): float
    {
        return round($price * (1 + $rate), 2);
    }
}
```

### 2. CLI で Portal を生成

```bash
php pinoox portal:create PriceCalculator -p com_acme_shop
```

### 3. Portal クラスでバインド — `__register()` と `__bind()`

```php
// apps/com_acme_shop/Portal/PriceCalculator.php
namespace App\com_acme_shop\Portal;

use Pinoox\Component\Source\Portal;

class PriceCalculator extends Portal
{
    public static function __register(): void
    {
        self::__bind(\App\com_acme_shop\Component\PriceCalculator::class);
    }
}
```

### 4. 使用

```php
use App\com_acme_shop\Portal\PriceCalculator;

$total = PriceCalculator::withTax(100_000);
```

### 5. IDE メタデータを更新

```bash
php pinoox portal:update PriceCalculator -p com_acme_shop
```

---

## Portal vs ヘルパー

| タスク | 推奨 |
|------|-------------|
| View | `View::render()` |
| Config | `config('key')` |
| Lang | `t('key')` または `Lang::get()` |
| URL | `url('path')` または `Url::link()` |
| Path | `path('reference')` |
| Validation | `Validation::validate()` または `$request->validate()` |
| アプリサービス | `Portal/` 内のカスタム Portal |

---

## ヒント

- ビジネスロジックは `Component/` に。Portal は Facade のみ
- Component にメソッドを追加したら `portal:update` を実行
- コア Portal を直接編集しない。`apps/{package}/Portal/` で拡張

---

## 関連ドキュメント

- [App Services](../advanced/services.md)
- [Config](./config.md)
- [プロジェクト構造](../start/structure.md)

---

[← 索引に戻る](../README.md)
