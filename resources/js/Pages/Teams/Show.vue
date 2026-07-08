<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppFrame from '../../Layouts/AppFrame.vue';

defineProps({
    team: {
        type: Object,
        required: true,
    },
    players: {
        type: Array,
        required: true,
    },
});

const formatCredits = (value) => new Intl.NumberFormat('de-DE').format(value ?? 0);
</script>

<template>
    <Head :title="team.name" />

    <AppFrame>
        <section class="rounded-3xl border border-emerald-300/30 bg-zinc-900/70 p-6 shadow-xl shadow-emerald-900/20 lg:p-8">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-300">Team Profile</p>
            <h1 class="mt-2 text-3xl font-black tracking-tight text-zinc-100">{{ team.name }}</h1>
            <p class="mt-3 text-zinc-300">{{ team.club }} | {{ team.league }} | {{ team.division }}</p>
            <p class="mt-2 text-sm font-semibold text-amber-300">Budget: {{ formatCredits(team.budget) }}</p>
            <p class="mt-2 text-sm text-zinc-300">Managed by: {{ team.is_cpu ? 'CPU' : 'Human Manager' }}</p>
            <Link href="/leagues" class="mt-4 inline-block text-sm font-semibold text-amber-300 hover:text-amber-200">
                Back to leagues
            </Link>
        </section>

        <section class="overflow-hidden rounded-2xl border border-zinc-400/35 bg-zinc-900/65 shadow-sm">
            <table class="min-w-full divide-y divide-zinc-700 text-sm">
                <thead class="bg-zinc-900/90">
                    <tr class="text-left text-zinc-300">
                        <th class="px-4 py-3 font-semibold">Player</th>
                        <th class="px-4 py-3 font-semibold">Position</th>
                        <th class="px-4 py-3 font-semibold">OVR</th>
                        <th class="px-4 py-3 font-semibold">Fitness</th>
                        <th class="px-4 py-3 font-semibold">Market Value</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800">
                    <tr v-for="player in players" :key="player.id" class="bg-zinc-900/45 text-zinc-100 hover:bg-zinc-800/65">
                        <td class="px-4 py-3 font-medium">{{ player.name }}</td>
                        <td class="px-4 py-3">{{ player.position }}</td>
                        <td class="px-4 py-3">{{ player.overall }}</td>
                        <td class="px-4 py-3">{{ player.fitness }}</td>
                        <td class="px-4 py-3">{{ formatCredits(player.market_value) }}</td>
                    </tr>
                </tbody>
            </table>
        </section>
    </AppFrame>
</template>
