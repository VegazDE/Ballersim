<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppFrame from '../../Layouts/AppFrame.vue';

defineProps({
    league: {
        type: Object,
        required: true,
    },
    division: {
        type: Object,
        required: true,
    },
    standings: {
        type: Array,
        required: true,
    },
    fixtures: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <Head :title="`${division.name} Standings`" />

    <AppFrame>
        <section class="rounded-3xl border border-emerald-300/30 bg-zinc-900/70 p-6 shadow-xl shadow-emerald-900/20 lg:p-8">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-300">Division Overview</p>
            <h1 class="mt-2 text-3xl font-black tracking-tight text-zinc-100">{{ division.name }}</h1>
            <p class="mt-2 text-zinc-300">{{ league.name }} | Code {{ division.code }}</p>
            <div class="mt-4 flex flex-wrap gap-3 text-sm">
                <Link :href="`/leagues/${league.id}`" class="font-semibold text-amber-300 hover:text-amber-200">Back to league</Link>
                <Link href="/leagues" class="font-semibold text-zinc-300 hover:text-zinc-200">League overview</Link>
            </div>
        </section>

        <section class="overflow-hidden rounded-2xl border border-zinc-400/35 bg-zinc-900/65 shadow-sm">
            <div class="border-b border-zinc-700 px-4 py-3">
                <h2 class="text-lg font-bold text-zinc-100">Table</h2>
            </div>
            <table class="min-w-full divide-y divide-zinc-700 text-sm">
                <thead class="bg-zinc-900/90">
                    <tr class="text-left text-zinc-300">
                        <th class="px-3 py-3 font-semibold">#</th>
                        <th class="px-4 py-3 font-semibold">Team</th>
                        <th class="px-3 py-3 font-semibold">P</th>
                        <th class="px-3 py-3 font-semibold">W</th>
                        <th class="px-3 py-3 font-semibold">D</th>
                        <th class="px-3 py-3 font-semibold">L</th>
                        <th class="px-3 py-3 font-semibold">GF</th>
                        <th class="px-3 py-3 font-semibold">GA</th>
                        <th class="px-3 py-3 font-semibold">GD</th>
                        <th class="px-3 py-3 font-semibold">Pts</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800">
                    <tr v-for="row in standings" :key="row.team_id" class="bg-zinc-900/45 text-zinc-100 hover:bg-zinc-800/65">
                        <td class="px-3 py-3 font-semibold">{{ row.position }}</td>
                        <td class="px-4 py-3 font-semibold">
                            <Link :href="`/teams/${row.team_id}`" class="text-emerald-300 hover:text-emerald-200">{{ row.team_name }}</Link>
                        </td>
                        <td class="px-3 py-3">{{ row.played }}</td>
                        <td class="px-3 py-3">{{ row.wins }}</td>
                        <td class="px-3 py-3">{{ row.draws }}</td>
                        <td class="px-3 py-3">{{ row.losses }}</td>
                        <td class="px-3 py-3">{{ row.goals_for }}</td>
                        <td class="px-3 py-3">{{ row.goals_against }}</td>
                        <td class="px-3 py-3">{{ row.goal_diff }}</td>
                        <td class="px-3 py-3 font-bold">{{ row.points }}</td>
                    </tr>
                </tbody>
            </table>
        </section>

        <section class="overflow-hidden rounded-2xl border border-zinc-400/35 bg-zinc-900/65 shadow-sm">
            <div class="border-b border-zinc-700 px-4 py-3">
                <h2 class="text-lg font-bold text-zinc-100">Fixtures</h2>
            </div>
            <table class="min-w-full divide-y divide-zinc-700 text-sm">
                <thead class="bg-zinc-900/90">
                    <tr class="text-left text-zinc-300">
                        <th class="px-4 py-3 font-semibold">MD</th>
                        <th class="px-4 py-3 font-semibold">Home</th>
                        <th class="px-4 py-3 font-semibold">Score</th>
                        <th class="px-4 py-3 font-semibold">Away</th>
                        <th class="px-4 py-3 font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800">
                    <tr v-if="fixtures.length === 0" class="bg-zinc-900/45 text-zinc-300">
                        <td class="px-4 py-5" colspan="5">No fixtures generated yet.</td>
                    </tr>
                    <tr v-for="fixture in fixtures" :key="fixture.id" class="bg-zinc-900/45 text-zinc-100 hover:bg-zinc-800/65">
                        <td class="px-4 py-3">{{ fixture.matchday }}</td>
                        <td class="px-4 py-3">
                            <Link v-if="fixture.home_team?.id" :href="`/teams/${fixture.home_team.id}`" class="text-emerald-300 hover:text-emerald-200">
                                {{ fixture.home_team.name }}
                            </Link>
                            <span v-else>-</span>
                        </td>
                        <td class="px-4 py-3 font-semibold">{{ fixture.score }}</td>
                        <td class="px-4 py-3">
                            <Link v-if="fixture.away_team?.id" :href="`/teams/${fixture.away_team.id}`" class="text-emerald-300 hover:text-emerald-200">
                                {{ fixture.away_team.name }}
                            </Link>
                            <span v-else>-</span>
                        </td>
                        <td class="px-4 py-3 uppercase text-xs tracking-wide">{{ fixture.status }}</td>
                    </tr>
                </tbody>
            </table>
        </section>
    </AppFrame>
</template>
