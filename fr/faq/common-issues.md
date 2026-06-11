# Problèmes courants

[← Retour à l'index](../README.md)

Corrections pratiques pour les erreurs fréquentes lors de l'installation, de l'exécution et du développement sur Pinoox. Chaque section recommande **une approche**.

---

## Échec de `composer install`

**Symptômes :** extension manquante, version PHP trop basse ou timeout réseau.

**Correction :**

1. Activez PHP 8.1+ et les extensions `mysqli`, `zip`, `mbstring`, `json`.
2. Exécutez la vérification plateforme avant l'installation :

```bash
php launcher/check.php
```

3. Réinstallez :

```bash
composer install --no-interaction
```

Sur un hébergement mutualisé, si `composer` n'est pas dans le PATH, construisez vendor en local et uploadez-le.

---

## Erreurs de permissions (accès aux fichiers)

**Symptômes :** impossible d'écrire dans `cache/`, `storage/`, `pinker/`.

**Correction (Linux/macOS) :**

```bash
chmod -R 775 cache storage pinker apps
chown -R www-data:www-data cache storage pinker
```

L'utilisateur du serveur web (ex. `www-data` ou `apache`) doit pouvoir écrire dans les dossiers inscriptibles. Sous Windows/MAMP, gardez le dossier du projet hors de `Program Files`.

---

## `.htaccess` / rewrite ne fonctionne pas

**Symptômes :** 404 sur toutes les URL sauf `index.php` ; l'API ne renvoie pas de JSON dans le navigateur.

**Correction :**

1. Activez Apache `mod_rewrite`.
2. Définissez `AllowOverride All` pour le DocumentRoot.
3. Vérifiez que `.htaccess` existe à la racine du projet.
4. Test rapide : `http://localhost/pinoox/api/v1/ping` — si vous voyez du JSON, le rewrite fonctionne.

Sur nginx, écrivez les règles `try_files` et `index.php` dans la config serveur au lieu de `.htaccess`.

---

## Échec de connexion à la base de données

**Symptômes :** `SQLSTATE[HY000] [2002] Connection refused` ou accès refusé.

**Correction :**

1. Vérifiez que MySQL/MariaDB est en cours d'exécution.
2. Contrôlez les valeurs dans `config/database.config.php` ou `.env` :

```env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pinoox_db
DB_USERNAME=root
DB_PASSWORD=
```

3. Créez la base au préalable (`CREATE DATABASE ... utf8mb4`).
4. Sur cPanel, l'hôte peut ne pas être `localhost` — utilisez le nom d'hôte du panneau.

---

## Rebuild Pinker requis

**Symptômes :** config ou routes obsolètes ; les changements dans `app.php` ne sont pas appliqués.

**Correction :**

```bash
php pinoox pinker:rebuild com_my_shop
# ou alias :
php pinoox bake com_my_shop

# toutes les apps :
php pinoox pinker:rebuild all
```

Après modification des routes, de la config ou déploiement en production, un rebuild est généralement requis.

---

## Route introuvable (404 sur l'endpoint)

**Symptômes :** la route est définie dans le code mais vous obtenez 404.

**Correction :**

1. Vérifiez que le fichier de routes est dans `apps/{package}/routes/` et listé dans `app.php` → `router.routes`.
2. Faites correspondre l'URL avec le préfixe de l'app (`app:router`) :

```bash
php pinoox app:router
php pinoox route:actions com_my_shop
```

3. Exécutez un rebuild Pinker (voir ci-dessus).
4. Utilisez la bonne méthode HTTP (`GET` vs `POST`).

---

## 404 — app non résolue

**Symptômes :** page par défaut ou 404 ; mauvaise app chargée.

**Correction :**

1. Vérifiez le mapping chemin/hôte :

```bash
php pinoox app:resolve --host=localhost --path=/shop
php pinoox app:domain
php pinoox app:router
```

2. Définissez correctement l'hôte et le chemin dans `config/domain.config.php` (ou la carte concernée).
3. Assurez `'enable' => true` dans le `app.php` de l'app.
4. Le nom du dossier de l'app doit égaler `'package'` dans `app.php` (ex. `com_my_shop`).

---

## Échec des tests

```bash
php pinoox test com_my_shop
```

- `.env.testing` avec une DB séparée
- migrations exécutées : `php pinoox migrate com_my_shop`
- après `fakeApp()` → `deleteFakeApp()`

Détails : [Premiers pas avec les tests](../test/getting-started.md)

---

## Documentation associée

- [Installer Pinoox](../start/installing-pinoox.md)
- [Structure du projet](../start/structure.md)
- [Router](../basic/routers.md)
- [Config](../basic/config.md)
- [Pinoox Baker (Pinker)](../advanced/pinker.md)
- [Premiers pas base de données](../database/getting-started.md)
- [Contacter le support](./contact-support.md)

---

[← Retour à l'index](../README.md)
