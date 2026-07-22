# @spurs-cloud/admin-sdk (Laravel)

Read configuration and credentials from the **Spurs Cloud admin control plane**
instead of scattering them across each app's `.env`. Admin is the source of
truth; an app only needs the bootstrap secret to fetch the rest.

## Install

```bash
composer require spurs-cloud/admin-sdk
```

Laravel auto-discovers the service provider and the `SpursAdmin` facade.

Publish the config (optional):

```bash
php artisan vendor:publish --tag=spurs-admin-config
```

## Configure (`.env`)

```dotenv
SPURS_ADMIN_URL="http://127.0.0.1:3300"   # the admin control plane
SPURS_ADMIN_APP="accounts"                # this app's slug in the admin registry
SPURS_ADMIN_SECRET="<admin bootstrap secret>"
```

## Use

```php
use SpursCloud\Admin\Facades\SpursAdmin;

$key = SpursAdmin::get('SOME_API_KEY');   // from admin, falls back to env()
$all = SpursAdmin::all();                 // everything admin holds for this app
```

### Override Laravel config from admin

Map admin credential keys onto Laravel config paths in `config/spurs-admin.php`.
On boot, any mapped key admin returns overrides the local value — anything it
does not return keeps its `.env` value (non-breaking):

```php
'map' => [
    'INTERNAL_API_SECRET' => 'spurs.internal_secret',
    'SPURS_GEOIP_URL'     => 'spurs.geoip_url',
],
```

Values are cached (`SPURS_ADMIN_CACHE_TTL`, default 300s) and fall back safely if
admin is unreachable (`SPURS_ADMIN_FALLBACK`).
