<script setup>
import { Head } from '@inertiajs/vue3';
import AppFrame from '../../Layouts/AppFrame.vue';

defineProps({
    listings: {
        type: Array,
        required: true,
    },
});

const formatCredits = (value) => new Intl.NumberFormat('de-DE').format(value ?? 0);
</script>

<template>
    <Head title="Transfer Market" />

    <AppFrame>
        <section class="rounded-3xl border border-emerald-300/30 bg-zinc-900/70 p-6 shadow-xl shadow-emerald-900/20 lg:p-8">
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-emerald-300">Transfer Market</p>
            <h1 class="mt-2 text-3xl font-bold tracking-tight text-zinc-100 lg:text-4xl">Open Listings</h1>
            <p class="mt-3 max-w-3xl text-zinc-300">
                Active listings from all managed clubs. This module is ready for the next step: bidding and acceptance flow.
            </p>
        </section>

        <section class="overflow-hidden rounded-2xl border border-zinc-400/35 bg-zinc-900/65 shadow-sm">
            <table class="min-w-full divide-y divide-zinc-700 text-sm">
                <thead class="bg-zinc-900/90">
                    <tr class="text-left text-zinc-300">
                            <th class="px-4 py-3 font-semibold">Spieler</th>
                            <th class="px-4 py-3 font-semibold">Position</th>
                            <th class="px-4 py-3 font-semibold">OVR</th>
                            <th class="px-4 py-3 font-semibold">Verkaeufer</th>
                            <th class="px-4 py-3 font-semibold">Abloese</th>
                            <th class="px-4 py-3 font-semibold">Mindestgebot</th>
                        </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800">
                    <tr v-if="listings.length === 0">
                        <td class="px-4 py-8 text-center text-zinc-400" colspan="6">
                                Noch keine aktiven Listings vorhanden.
                        </td>
                    </tr>
                    <tr v-for="listing in listings" :key="listing.id" class="bg-zinc-900/45 text-zinc-100 hover:bg-zinc-800/65">
                        <td class="px-4 py-3 font-medium">{{ listing.player?.name ?? 'Unbekannt' }}</td>
                        <td class="px-4 py-3">{{ listing.player?.position ?? '-' }}</td>
                        <td class="px-4 py-3">{{ listing.player?.overall ?? '-' }}</td>
                        <td class="px-4 py-3">{{ listing.seller_team ?? '-' }}</td>
                        <td class="px-4 py-3">{{ formatCredits(listing.asking_price) }}</td>
                        <td class="px-4 py-3">{{ listing.minimum_bid ? formatCredits(listing.minimum_bid) : '-' }}</td>
                    </tr>
                </tbody>
            </table>
        </section>
    </AppFrame>
</template>
