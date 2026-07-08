# Baller Manager Admin Playbook

This file is the operational checklist for deployment and core league setup.

## 1. One-time server setup

1. Pull latest code:

```bash
git pull
```

2. Install backend deps:

```bash
composer install --no-dev --optimize-autoloader
```

3. Build frontend:

```bash
npm install
npm run build
```

4. Run migrations:

```bash
php artisan migrate --force
```

5. Warm caches:

```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 2. League bootstrap (run once)

Creates 6 leagues, 2 divisions each, CPU teams auto-filled.

```bash
php artisan baller:bootstrap-leagues
```

Optional:

```bash
php artisan baller:bootstrap-leagues --teams-per-division=12
```

## 3. Season generation

Create full home/away fixture list for all divisions.

```bash
php artisan baller:generate-season "Season 1" --start-date=2026-08-01
```

Default (auto name and start date):

```bash
php artisan baller:generate-season
```

## 4. Daily/after-deploy checks

1. Routes are present:

```bash
php artisan route:list --path=leagues
php artisan route:list --path=transfer-market
php artisan route:list --path=dashboard
```

2. Key pages:
- /
- /login
- /register
- /dashboard
- /leagues
- /transfer-market

3. Waitlist storage file exists:
- storage/app/waitlist-emails.txt

## 5. Troubleshooting quick fixes

1. If blade or config changes do not show:

```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

2. If frontend manifest missing:

```bash
npm run build
```

3. If DB connection fails:
- Verify .env DB_HOST/DB_DATABASE/DB_USERNAME/DB_PASSWORD
- Run `php artisan config:clear` then `php artisan config:cache`

## 6. Current build order (short)

1. League core and fixtures
2. Standings and table logic
3. Match simulation v1
4. Matchday execution loop
5. Live ticker and realtime
6. Tactics and lineups
7. Transfer and draft integration
8. Promotion/relegation and season rollover
