# Baller Manager

Transfermarkt-First Basis fuer den Echtzeit-Multiplayer-Manager (6v6), aufgesetzt mit Laravel + Inertia + Vue.

## Step 1 Scope

- Laravel 13 Grundgeruest
- Inertia.js + Vue 3 Frontend-Bootstrap
- Transfermarkt-Startmodul (Listings + Bids)
- Erste Kernentitaeten: Clubs, Teams, Players
- Grundkonfiguration fuer Redis Queue/Cache und Reverb
- Matchdauer Standard: 7 Minuten

## Tech Stack

- Backend: Laravel 13 / PHP 8.3
- Frontend: Inertia.js + Vue 3 + Tailwind 4
- Realtime: Laravel Reverb (vorbereitet)
- Queue / Cache: Redis
- Datenbank: MariaDB (via Laravel mysql driver)

## Lokaler Start

1. Composer Dependencies installieren:

```bash
composer install
```

2. JS Dependencies installieren (Node/NPM erforderlich):

```bash
npm install
```

3. Environment vorbereiten:

```bash
cp .env.example .env
php artisan key:generate
```

4. DB konfigurieren und Migrationen ausfuehren:

```bash
php artisan migrate
```

5. Development starten:

```bash
composer run dev
```

## Wichtige Konfiguration

- Zieldomain: `https://baller.juulex.de`
- Matchdauer: `BALLER_MATCH_DURATION_MINUTES=7`
- Transfermarkt zuerst: Route `/transfer-market` (zusatzlich auf `/`)

## Plesk Deployment (AWS Plesk)

1. Domain in Plesk anlegen: `baller.juulex.de`
2. PHP 8.3 aktivieren und benoetigte Extensions sicherstellen (`openssl`, `mbstring`, `pdo_mysql`, `curl`, `zip`)
3. `.env` auf Produktion setzen:
	- `APP_ENV=production`
	- `APP_DEBUG=false`
	- `APP_URL=https://baller.juulex.de`
	- MariaDB Zugangsdaten
	- Redis Zugangsdaten
4. Deploy Hook ausfuehren:

```bash
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
npm ci
npm run build
```

5. Dauerprozesse sicherstellen:
	- `php artisan queue:work --sleep=1 --tries=3 --timeout=120`
	- `php artisan reverb:start`
	- `php artisan schedule:run` minuetlich via Cron

## Naechster Schritt (Step 2)

- DB verbinden (deine MariaDB Daten)
- Seeder fuer Testligen/Teams/Spieler
- Transfermarkt-Aktionen: Listing erstellen, Gebot abgeben, Gebot annehmen/ablehnen
- Erste CPU-Transferlogik
