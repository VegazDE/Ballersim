<script setup>
import { computed, reactive } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppFrame from '../../Layouts/AppFrame.vue';

const props = defineProps({
    fixtures: {
        type: Array,
        required: true,
    },
    filters: {
        type: Object,
        required: true,
    },
    options: {
        type: Object,
        required: true,
    },
    default_team: {
        type: Object,
        default: null,
    },
});

const localFilters = reactive({
    season_id: props.filters.season_id ?? null,
    league_id: props.filters.league_id ?? null,
    division_id: props.filters.division_id ?? null,
    team_id: props.filters.team_id ?? null,
    status: props.filters.status ?? null,
    venue: props.filters.venue ?? null,
    window_days: props.filters.window_days ?? null,
});

const selectedLeague = computed(() => {
    const id = Number(localFilters.league_id);

    return props.options.leagues.find((league) => league.id === id) ?? null;
});

const divisionOptions = computed(() => selectedLeague.value?.divisions ?? []);

const selectedDivision = computed(() => {
    const id = Number(localFilters.division_id);

    return divisionOptions.value.find((division) => division.id === id) ?? null;
});

const teamOptions = computed(() => {
    if (selectedDivision.value) {
        return selectedDivision.value.teams;
    }

    if (selectedLeague.value) {
        return selectedLeague.value.divisions.flatMap((division) => division.teams);
    }

    return props.options.leagues.flatMap((league) => league.divisions.flatMap((division) => division.teams));
});

const hasOwnTeamDefault = computed(() => Boolean(props.default_team?.id));

const formatKickoff = (isoDate) => {
    if (!isoDate) {
        return '-';
    }

    return new Date(isoDate).toLocaleString('de-DE', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const applyFilters = () => {
    const query = {};

    if (localFilters.season_id) {
        query.season_id = localFilters.season_id;
    }

    if (localFilters.league_id) {
        query.league_id = localFilters.league_id;
    }

    if (localFilters.division_id) {
        query.division_id = localFilters.division_id;
    }

    if (localFilters.team_id) {
        query.team_id = localFilters.team_id;
    }

    if (localFilters.status) {
        query.status = localFilters.status;
    }

    if (localFilters.venue) {
        query.venue = localFilters.venue;
    }

    if (localFilters.window_days) {
        query.window_days = localFilters.window_days;
    }

    router.get('/spielplan', query, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

const onLeagueChange = () => {
    localFilters.division_id = null;
    localFilters.team_id = null;
};

const onDivisionChange = () => {
    localFilters.team_id = null;
};

const resetToDefault = () => {
    localFilters.season_id = props.filters.season_id;
    localFilters.status = null;
    localFilters.venue = null;
    localFilters.window_days = null;

    if (props.default_team) {
        localFilters.league_id = props.default_team.league_id;
        localFilters.division_id = props.default_team.division_id;
        localFilters.team_id = props.default_team.id;
    } else {
        localFilters.league_id = null;
        localFilters.division_id = null;
        localFilters.team_id = null;
    }

    applyFilters();
};

const setQuickFilter = (mode) => {
    if (!props.default_team) {
        return;
    }

    localFilters.league_id = props.default_team.league_id;
    localFilters.division_id = props.default_team.division_id;
    localFilters.team_id = props.default_team.id;
    localFilters.status = null;

    if (mode === 'home') {
        localFilters.venue = 'home';
        localFilters.window_days = null;
    }

    if (mode === 'away') {
        localFilters.venue = 'away';
        localFilters.window_days = null;
    }

    if (mode === 'next7') {
        localFilters.venue = null;
        localFilters.window_days = 7;
    }

    applyFilters();
};
</script>

<template>
    <Head title="Spielplan" />

    <AppFrame>
        <section class="rounded-3xl border border-emerald-300/30 bg-zinc-900/70 p-6 shadow-xl shadow-emerald-900/20 lg:p-8">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-300">Match Schedule</p>
            <h1 class="mt-2 text-3xl font-black tracking-tight text-zinc-100">Spielplan</h1>
            <p class="mt-3 text-zinc-300">
                Filtere nach Liga, Staffel und Team.
                <span v-if="hasOwnTeamDefault" class="font-semibold text-amber-300">Standardansicht: dein eigenes Team.</span>
            </p>
        </section>

        <section class="rounded-2xl border border-zinc-400/30 bg-zinc-900/65 p-5 shadow-sm">
            <div class="grid gap-4 md:grid-cols-7">
                <label class="space-y-2 text-sm">
                    <span class="font-semibold text-zinc-200">Saison</span>
                    <select v-model="localFilters.season_id" class="w-full rounded-lg border border-zinc-600 bg-zinc-900 px-3 py-2 text-zinc-100">
                        <option :value="null">Alle</option>
                        <option v-for="season in options.seasons" :key="season.id" :value="season.id">{{ season.name }}</option>
                    </select>
                </label>

                <label class="space-y-2 text-sm">
                    <span class="font-semibold text-zinc-200">Liga</span>
                    <select
                        v-model="localFilters.league_id"
                        class="w-full rounded-lg border border-zinc-600 bg-zinc-900 px-3 py-2 text-zinc-100"
                        @change="onLeagueChange"
                    >
                        <option :value="null">Alle</option>
                        <option v-for="league in options.leagues" :key="league.id" :value="league.id">{{ league.name }}</option>
                    </select>
                </label>

                <label class="space-y-2 text-sm">
                    <span class="font-semibold text-zinc-200">Staffel</span>
                    <select
                        v-model="localFilters.division_id"
                        class="w-full rounded-lg border border-zinc-600 bg-zinc-900 px-3 py-2 text-zinc-100"
                        @change="onDivisionChange"
                    >
                        <option :value="null">Alle</option>
                        <option v-for="division in divisionOptions" :key="division.id" :value="division.id">{{ division.name }}</option>
                    </select>
                </label>

                <label class="space-y-2 text-sm">
                    <span class="font-semibold text-zinc-200">Team</span>
                    <select v-model="localFilters.team_id" class="w-full rounded-lg border border-zinc-600 bg-zinc-900 px-3 py-2 text-zinc-100">
                        <option :value="null">Alle</option>
                        <option v-for="team in teamOptions" :key="team.id" :value="team.id">{{ team.name }}</option>
                    </select>
                </label>

                <label class="space-y-2 text-sm">
                    <span class="font-semibold text-zinc-200">Status</span>
                    <select v-model="localFilters.status" class="w-full rounded-lg border border-zinc-600 bg-zinc-900 px-3 py-2 text-zinc-100">
                        <option :value="null">Alle</option>
                        <option v-for="statusOption in options.statuses" :key="statusOption" :value="statusOption">{{ statusOption }}</option>
                    </select>
                </label>

                <label class="space-y-2 text-sm">
                    <span class="font-semibold text-zinc-200">Ort</span>
                    <select v-model="localFilters.venue" class="w-full rounded-lg border border-zinc-600 bg-zinc-900 px-3 py-2 text-zinc-100">
                        <option :value="null">Heim + Auswärts</option>
                        <option v-for="venue in options.venues" :key="venue" :value="venue">{{ venue }}</option>
                    </select>
                </label>

                <label class="space-y-2 text-sm">
                    <span class="font-semibold text-zinc-200">Zeitraum</span>
                    <select v-model="localFilters.window_days" class="w-full rounded-lg border border-zinc-600 bg-zinc-900 px-3 py-2 text-zinc-100">
                        <option :value="null">Komplette Saison</option>
                        <option v-for="windowOption in options.windows" :key="windowOption" :value="windowOption">Nächste {{ windowOption }} Tage</option>
                    </select>
                </label>
            </div>

            <div class="mt-4 flex flex-wrap gap-3">
                <button
                    type="button"
                    class="rounded-lg bg-emerald-400 px-4 py-2 text-sm font-bold text-zinc-950 transition hover:bg-emerald-300"
                    @click="applyFilters"
                >
                    Filter anwenden
                </button>
                <button
                    type="button"
                    class="rounded-lg border border-zinc-500 px-4 py-2 text-sm font-semibold text-zinc-100 transition hover:border-zinc-300"
                    @click="resetToDefault"
                >
                    Zur Standardansicht
                </button>

                <button
                    type="button"
                    class="rounded-lg border border-emerald-500/60 px-4 py-2 text-sm font-semibold text-emerald-300 transition enabled:hover:bg-emerald-500/10 disabled:cursor-not-allowed disabled:opacity-50"
                    :disabled="!hasOwnTeamDefault"
                    @click="setQuickFilter('home')"
                >
                    Eigene Heimspiele
                </button>

                <button
                    type="button"
                    class="rounded-lg border border-emerald-500/60 px-4 py-2 text-sm font-semibold text-emerald-300 transition enabled:hover:bg-emerald-500/10 disabled:cursor-not-allowed disabled:opacity-50"
                    :disabled="!hasOwnTeamDefault"
                    @click="setQuickFilter('away')"
                >
                    Eigene Auswärtsspiele
                </button>

                <button
                    type="button"
                    class="rounded-lg border border-amber-500/60 px-4 py-2 text-sm font-semibold text-amber-300 transition enabled:hover:bg-amber-500/10 disabled:cursor-not-allowed disabled:opacity-50"
                    :disabled="!hasOwnTeamDefault"
                    @click="setQuickFilter('next7')"
                >
                    Eigene Spiele nächste 7 Tage
                </button>
            </div>
        </section>

        <section class="overflow-hidden rounded-2xl border border-zinc-400/35 bg-zinc-900/65 shadow-sm">
            <table class="min-w-full divide-y divide-zinc-700 text-sm">
                <thead class="bg-zinc-900/90">
                    <tr class="text-left text-zinc-300">
                        <th class="px-3 py-3 font-semibold">Datum</th>
                        <th class="px-3 py-3 font-semibold">MD</th>
                        <th class="px-3 py-3 font-semibold">Liga</th>
                        <th class="px-3 py-3 font-semibold">Staffel</th>
                        <th class="px-3 py-3 font-semibold">Heim</th>
                        <th class="px-3 py-3 font-semibold">Ergebnis</th>
                        <th class="px-3 py-3 font-semibold">Auswärts</th>
                        <th class="px-3 py-3 font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800">
                    <tr v-if="fixtures.length === 0" class="bg-zinc-900/45 text-zinc-300">
                        <td colspan="8" class="px-4 py-5">Keine Spiele mit diesen Filtern gefunden.</td>
                    </tr>
                    <tr v-for="fixture in fixtures" :key="fixture.id" class="bg-zinc-900/45 text-zinc-100 hover:bg-zinc-800/65">
                        <td class="px-3 py-3">{{ formatKickoff(fixture.kickoff_at) }}</td>
                        <td class="px-3 py-3">{{ fixture.matchday }}</td>
                        <td class="px-3 py-3">{{ fixture.league?.name ?? '-' }}</td>
                        <td class="px-3 py-3">
                            <Link
                                v-if="fixture.league?.id && fixture.division?.id"
                                :href="`/leagues/${fixture.league.id}/divisions/${fixture.division.id}`"
                                class="text-emerald-300 hover:text-emerald-200"
                            >
                                {{ fixture.division?.name ?? '-' }}
                            </Link>
                            <span v-else>-</span>
                        </td>
                        <td class="px-3 py-3 font-semibold">
                            <Link v-if="fixture.home_team?.id" :href="`/teams/${fixture.home_team.id}`" class="text-emerald-300 hover:text-emerald-200">
                                {{ fixture.home_team?.name ?? '-' }}
                            </Link>
                            <span v-else>-</span>
                        </td>
                        <td class="px-3 py-3 font-semibold">{{ fixture.score }}</td>
                        <td class="px-3 py-3 font-semibold">
                            <Link v-if="fixture.away_team?.id" :href="`/teams/${fixture.away_team.id}`" class="text-emerald-300 hover:text-emerald-200">
                                {{ fixture.away_team?.name ?? '-' }}
                            </Link>
                            <span v-else>-</span>
                        </td>
                        <td class="px-3 py-3 uppercase text-xs tracking-wide">{{ fixture.status }}</td>
                    </tr>
                </tbody>
            </table>
        </section>
    </AppFrame>
</template>
