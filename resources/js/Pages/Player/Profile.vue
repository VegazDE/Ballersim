<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppFrame from '../../Layouts/AppFrame.vue';

defineProps({
    player: {
        type: Object,
        required: true,
    },
});

const formatCredits = (value) => new Intl.NumberFormat('de-DE').format(value ?? 0);
</script>

<template>
    <Head :title="`Player: ${player.name}`" />

    <AppFrame>
        <section class="rounded-3xl border border-emerald-300/30 bg-zinc-900/70 p-6 shadow-xl shadow-emerald-900/20 lg:p-8">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-300">Player Profile</p>
            <h1 class="mt-2 text-3xl font-black tracking-tight text-zinc-100">{{ player.name }}</h1>
            <p class="mt-3 text-zinc-300">{{ player.team.club }} | {{ player.team.league }} | {{ player.team.division }}</p>
            <Link href="/my-team" class="mt-4 inline-block text-sm font-semibold text-amber-300 hover:text-amber-200">
                Back to team profile
            </Link>
        </section>

        <section class="grid gap-4 md:grid-cols-4">
            <article class="rounded-xl border border-zinc-400/30 bg-zinc-900/65 p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-zinc-300">Position</p>
                <p class="mt-2 text-2xl font-bold text-zinc-100">{{ player.position }}</p>
            </article>

            <article class="rounded-xl border border-zinc-400/30 bg-zinc-900/65 p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-zinc-300">Overall</p>
                <p class="mt-2 text-2xl font-bold text-zinc-100">{{ player.overall }}</p>
            </article>

            <article class="rounded-xl border border-zinc-400/30 bg-zinc-900/65 p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-zinc-300">Fitness</p>
                <p class="mt-2 text-2xl font-bold text-zinc-100">{{ player.fitness }}</p>
            </article>

            <article class="rounded-xl border border-zinc-400/30 bg-zinc-900/65 p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-zinc-300">Market Value</p>
                <p class="mt-2 text-2xl font-bold text-zinc-100">{{ formatCredits(player.market_value) }}</p>
            </article>
        </section>
    </AppFrame>
</template>
