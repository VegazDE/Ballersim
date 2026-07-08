<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppFrame from '../../Layouts/AppFrame.vue';

defineProps({
    league: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <Head :title="league.name" />

    <AppFrame>
        <section class="rounded-3xl border border-emerald-300/30 bg-zinc-900/70 p-6 shadow-xl shadow-emerald-900/20 lg:p-8">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-300">League Detail</p>
            <h1 class="mt-2 text-3xl font-black tracking-tight text-zinc-100">{{ league.name }}</h1>
            <p class="mt-2 text-zinc-300">Tier {{ league.level }} | {{ league.teams_count }} teams</p>
            <Link href="/leagues" class="mt-4 inline-block text-sm font-semibold text-amber-300 hover:text-amber-200">
                Back to league overview
            </Link>
        </section>

        <section class="grid gap-4 md:grid-cols-2">
            <article
                v-for="division in league.divisions"
                :key="division.id"
                class="rounded-2xl border border-zinc-400/30 bg-zinc-900/65 p-5 shadow-sm"
            >
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-zinc-300">Division {{ division.code }}</p>
                <h2 class="mt-2 text-2xl font-bold text-zinc-100">{{ division.name }}</h2>
                <p class="mt-2 text-sm text-zinc-300">{{ division.teams_count }}/{{ division.teams_target }} teams</p>
                <Link
                    :href="`/leagues/${league.id}/divisions/${division.id}`"
                    class="mt-4 inline-block text-sm font-semibold text-emerald-300 hover:text-emerald-200"
                >
                    Open standings and fixtures
                </Link>
            </article>
        </section>
    </AppFrame>
</template>
