# Baller Manager Implementation Roadmap

## Build Order (Agreed)

1. Foundation and deployment stability
2. League system core (leagues, divisions, seasons)
3. Fixtures and standings
4. Match simulation v1 (7-minute flow)
5. CPU teams and auto-fill logic
6. Matchday execution loop and persistence
7. Live ticker and realtime updates
8. Tactics and lineups
9. Transfer market and draft integration
10. Promotion/relegation and season rollover
11. Deep systems (training, fitness, injuries, scouting)
12. Extended systems (youth, loans, cup, items)
13. Community and cosmetic systems

## Current Sprint Todo

- [x] Persist roadmap and priorities
- [x] Add league and division data model
- [x] Add league_id and division_id to teams
- [x] Add one-time bootstrap command for 6 leagues x 2 divisions
- [x] Add auth flow (register, login, logout, dashboard)
- [x] Replace placeholder home with branded landing page
- [ ] Run migrations on server
- [ ] Run bootstrap command once on server
- [ ] Build league index page with standings skeleton
- [ ] Add season model and first season generator

## Bootstrap Command

Use once after migrations:

```bash
php artisan baller:bootstrap-leagues
```

Optional custom team count per division:

```bash
php artisan baller:bootstrap-leagues --teams-per-division=12
```

## Notes

- Bootstrap command is intentionally one-time and exits if divisions already exist.
- Teams created by bootstrap are CPU-managed by default.
- Division labels are synced into teams.division_name for compatibility with existing UI.
