# Gerenciamento de Arquivos

[← Voltar ao índice](../README.md)

Upload e armazenamento no Pinoox 3.x passam por um único portal: **`Pinoox\Portal\File`**. Os metadados ficam em `pincore_file` (ou em um escopo transport compartilhado) e os arquivos físicos no disco (local, S3, …).

---

## Ponto de entrada

```php
use Pinoox\Portal\File;
```

| Necessidade | API |
|------|-----|
| Upload + registro no BD + URL | `File::upload(...)->save()` |
| Buscar / excluir / URL | `File::find()`, `File::url()`, `File::remove()` |
| Acesso direto ao disco | `File::storage()->put(...)` |

Não use `Storage::` diretamente para uploads de usuários — prefixo, disco e URL permanecem consistentes com `File::`.

---

## Configuração no app.php

```php
return [
    'transport' => [
        'file_storage' => 'platform',   // ou 'local'
    ],
    'filesystem' => [
        'disk' => 'local',
        'default_access' => 'public',
        'thumb_width' => 512,
        'thumb_height' => 512,
    ],
];
```

Discos globais em `config/filesystems.config.php` e `.env`:

```env
FILESYSTEM_DISK=local
AWS_ACCESS_KEY_ID=...
AWS_BUCKET=...
AWS_URL=https://cdn.example.com
```

---

## Upload com registro no banco de dados

```php
$result = File::upload('avatar')
    ->to('avatar')                  // → storage/apps/{package}/avatar
    ->group('avatar')
    ->thumb()
    ->maxSize('2MB')
    ->extensions('jpg,jpeg,png,webp')
    ->save();

if ($result->success) {
    $fileId = $result->id;
    $url = $result->url;
    $thumb = $result->thumb;
}
```

---

## A partir do Request

```php
$result = $request->store('photo', 'gallery')
    ->group('gallery')
    ->thumb(256, 256)
    ->save();
```

---

## Anexar a um model

```php
$result = File::upload('cover')
    ->to('posts')
    ->group('post_cover')
    ->attach($post, 'cover_id')
    ->save();
```

Substituir um arquivo anterior:

```php
$result = File::upload('avatar')
    ->to('avatar')
    ->group('avatar')
    ->replaceOn($user, 'avatar_id')
    ->thumb()
    ->save();
```

---

## Somente disco (sem BD)

```php
$result = File::upload('file')
    ->to('packages')
    ->diskOnly()
    ->save();

if ($result->success) {
    $path = $result->path;
}
```

---

## Ler e excluir

```php
$record = File::find($fileId);
$url = File::url($fileId);
$thumb = File::thumb($fileId);
$list = File::listByGroup('avatar');

File::remove($fileId);
```

---

## UploadBuilder — métodos principais

| Método | Descrição |
|--------|-------------|
| `to($dir)` | Pasta de destino |
| `group($name)` | Grupo lógico no BD |
| `thumb($w, $h)` | Miniatura da imagem |
| `maxSize('2MB')` | Tamanho máximo do arquivo |
| `extensions('jpg,png')` | Extensões permitidas |
| `disk('s3')` | Sobrescrever o disco |
| `attach($model, $column)` | Definir FK após o upload |
| `replaceOn($model, $column)` | Remover o antigo + enviar o novo |
| `save()` | Executar → `UploadResult` |

---

## UploadResult

```php
$result->success;   // bool
$result->id;        // file_id
$result->url;       // file_link
$result->thumb;     // thumb_link
$result->path;      // caminho absoluto
$result->record;    // FileModel
$result->error;     // mensagem de erro
```

---

## S3

```php
// app.php
'filesystem' => ['disk' => 's3'],

// ou por upload
File::upload('doc')->to('docs')->disk('s3')->save();
```

Arquivos privados no S3:

```php
$url = File::storage('s3')->temporaryUrl('private/doc.pdf', now()->addHour());
```

---

## Dicas

- Valide no FormRequest antes de `File::upload()`.
- `user_id` é preenchido a partir de `Auth::id()`.
- Com `transport.file_storage => platform`, os arquivos são compartilhados entre os apps da plataforma.

---


---

## Arquivos grandes

For files that exceed `upload_max_filesize` or need resume/progress, use the **[Pinion](./pinion.md)** protocol. Pinion stages chunks under `storage/pinion`, then on `complete` publishes to your app disk (local or S3) via `Portal\File` when `mode` is `auto` or `storage`.

```javascript
import { uploadFile } from '@pinooxhq/pinion-client';

await uploadFile(file, {
  baseURL: '/api/v1/upload',
  unwrapPreset: 'pinoox',
});
```

---

## Documentação relacionada

- [protocolo Pinion](./pinion.md)
- [Gerenciamento de usuários](./user-management.md)
- [Transport](./transport.md)
- [Validação](../basic/validation.md)
- [Tutorial de galeria de imagens](../examples/gallery-app.md)

---

[← Voltar ao índice](../README.md)
