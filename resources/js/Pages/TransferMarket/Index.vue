<script setup>
defineProps({
    listings: {
        type: Array,
        required: true,
    },
});

const formatCredits = (value) => new Intl.NumberFormat('de-DE').format(value ?? 0);
</script>

<template>
    <main class="min-h-screen bg-gradient-to-b from-lime-50 via-zinc-50 to-white text-zinc-900">
        <section class="mx-auto max-w-6xl px-6 py-10 lg:py-14">
            <header class="mb-10 rounded-2xl border border-zinc-200 bg-white/80 p-6 shadow-sm backdrop-blur">
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-emerald-700">Baller Manager</p>
                <h1 class="mt-2 text-3xl font-bold tracking-tight text-zinc-900 lg:text-4xl">Transfermarkt</h1>
                <p class="mt-3 max-w-3xl text-zinc-600">
                    Step 1 ist live: Dieses Modul zeigt offene Listings und bildet die Grundlage fuer Bietlogik,
                    CPU-Transfers und spaetere Live-Updates.
                </p>
            </header>

            <div class="overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-sm">
                <table class="min-w-full divide-y divide-zinc-200 text-sm">
                    <thead class="bg-zinc-50">
                        <tr class="text-left text-zinc-600">
                            <th class="px-4 py-3 font-semibold">Spieler</th>
                            <th class="px-4 py-3 font-semibold">Position</th>
                            <th class="px-4 py-3 font-semibold">OVR</th>
                            <th class="px-4 py-3 font-semibold">Verkaeufer</th>
                            <th class="px-4 py-3 font-semibold">Abloese</th>
                            <th class="px-4 py-3 font-semibold">Mindestgebot</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        <tr v-if="listings.length === 0">
                            <td class="px-4 py-8 text-center text-zinc-500" colspan="6">
                                Noch keine aktiven Listings vorhanden.
                            </td>
                        </tr>
                        <tr v-for="listing in listings" :key="listing.id" class="hover:bg-zinc-50/70">
                            <td class="px-4 py-3 font-medium text-zinc-800">{{ listing.player?.name ?? 'Unbekannt' }}</td>
                            <td class="px-4 py-3">{{ listing.player?.position ?? '-' }}</td>
                            <td class="px-4 py-3">{{ listing.player?.overall ?? '-' }}</td>
                            <td class="px-4 py-3">{{ listing.seller_team ?? '-' }}</td>
                            <td class="px-4 py-3">{{ formatCredits(listing.asking_price) }}</td>
                            <td class="px-4 py-3">{{ listing.minimum_bid ? formatCredits(listing.minimum_bid) : '-' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</template>
