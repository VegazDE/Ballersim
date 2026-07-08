<script setup>
import { Head } from '@inertiajs/vue3';
import AppFrame from '../../Layouts/AppFrame.vue';

defineProps({
    leagues: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <Head title="Leagues" />

    <AppFrame>
        <section class="rounded-3xl border border-emerald-300/30 bg-zinc-900/70 p-6 shadow-xl shadow-emerald-900/20 lg:p-8">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-300">Competition</p>
            <h1 class="mt-2 text-3xl font-black tracking-tight text-zinc-100 lg:text-4xl">League Overview</h1>
            <p class="mt-2 text-zinc-300">Current structure includes 6 leagues with two divisions each.</p>
        </section>

        <section class="grid gap-5">
            <article
                v-for="league in leagues"
                :key="league.id"
                class="rounded-2xl border border-zinc-400/30 bg-zinc-900/65 p-5 shadow-sm"
            >
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-zinc-300">Tier {{ league.level }}</p>
                        <h2 class="text-2xl font-bold tracking-tight text-zinc-100">{{ league.name }}</h2>
                    </div>
                    <p class="rounded-full border border-zinc-400/35 px-3 py-1 text-sm font-semibold text-zinc-200">
                        {{ league.teams_count }} teams
                    </p>
                </div>

                <div class="overflow-hidden rounded-xl border border-zinc-500/35">
                    <table class="min-w-full divide-y divide-zinc-700 text-sm">
                        <thead class="bg-zinc-900/85">
                            <tr class="text-left text-zinc-300">
                                <th class="px-4 py-3 font-semibold">Division</th>
                                <th class="px-4 py-3 font-semibold">Code</th>
                                <th class="px-4 py-3 font-semibold">Current Teams</th>
                                <th class="px-4 py-3 font-semibold">Target Teams</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-800">
                            <tr v-for="division in league.divisions" :key="division.id" class="bg-zinc-900/45 text-zinc-100">
                                <td class="px-4 py-3 font-semibold">{{ division.name }}</td>
                                <td class="px-4 py-3">{{ division.code }}</td>
                                <td class="px-4 py-3">{{ division.teams_count }}</td>
                                <td class="px-4 py-3">{{ division.teams_target }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </article>
        </section>
    </AppFrame>
</template>
