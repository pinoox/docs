# Model Serialization

[← 索引に戻る](../README.md)

Serialization は Eloquent Model を配列または JSON に変換します — API、Cache、ログ向け。Pinoox 3.x は Illuminate の動作に従い、`$hidden`、`$visible`、`$appends` が主要な役割を果たします。

---

## toArray

```php
$post = PostModel::find(1);
$array = $post->toArray();
```

---

## toJson

```php
$json = $post->toJson();
$json = $post->toJson(JSON_UNESCAPED_UNICODE);
```

---

## $hidden — 出力から除外

```php
class UserModel extends Model
{
    protected $hidden = ['password', 'session_id', 'app'];
}
```

```php
$user->toArray();   // password は省略
```

---

## $visible — ホワイトリスト

```php
protected $visible = ['post_id', 'title', 'status', 'created_at'];
```

`$visible` が設定されると、`toArray()` / `toJson()` ではそれらのフィールドのみ表示されます。

---

## makeHidden / makeVisible（一時的）

```php
$user->makeHidden(['email'])->toArray();
$user->makeVisible(['password'])->toArray();   // 管理者のみ — 慎重に使用
```

---

## $appends — 仮想属性

```php
protected $appends = ['full_name'];

protected function fullName(): \Illuminate\Database\Eloquent\Casts\Attribute
{
    return \Illuminate\Database\Eloquent\Casts\Attribute::make(
        get: fn () => "{$this->fname} {$this->lname}",
    );
}
```

---

## 出力内のリレーション

```php
$post = PostModel::with('comments')->find(1);
$array = $post->toArray();
// 'comments' => [...] キーを含む
```

Eager loading なしでは、リレーションは lazy load されます。

---

## Collection 上の setHidden

```php
PostModel::all()->each->makeHidden(['body']);
```

---

## ApiResource vs toArray

| アプローチ | ユースケース |
|----------|----------|
| `toArray()` / `toJson()` | デバッグ、内部 Cache、エクスポート |
| `ApiResource` | 公開 API — 精密なフィールドとネスト形状の制御 |

公開エンドポイントには **ApiResource** を優先してください。

---

## Serialization 内の Cast

```php
protected $casts = ['metadata' => 'array', 'paid_at' => 'datetime'];
```

`metadata` は配列、`paid_at` は JSON 内で ISO 文字列になります。

---

## ヒント

- すべての機密フィールドに `$hidden` を設定。
- `$guarded` / `$fillable` は Serialization とは別（一括代入）。
- ログにはパスワードなしの `toArray()` を使用。

---

## 関連ドキュメント

- [Mutators と Casts](./mutators-casts.md)
- [API Resources](./api-resources.md)
- [Collections](./collections.md)

---

[← 索引に戻る](../README.md)
