<script setup>
import { computed, reactive } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppFrame from '../../Layouts/AppFrame.vue';

const props = defineProps({
    team: {
        type: Object,
        default: null,
    },
    players: {
        type: Array,
        required: true,
    },
});

const formatCredits = (value) => new Intl.NumberFormat('de-DE').format(value ?? 0);

const form = reactive({
    formation: props.team?.formation ?? '4-3-3',
    mentality: props.team?.mentality ?? 'balanced',
    pressing: props.team?.pressing ?? 50,
    tempo: props.team?.tempo ?? 50,
    substitution_style: props.team?.substitution_style ?? 50,
    line_height: props.team?.line_height ?? 50,
    starting_lineup: [...(props.team?.starting_lineup ?? [])],
});

const formationOptions = ['4-3-3', '4-4-2', '3-5-2', '3-4-3', '4-2-3-1', '5-3-2', '4-1-4-1'];
const mentalityOptions = [
    { value: 'defensive', label: 'Defensive' },
    { value: 'balanced', label: 'Balanced' },
    { value: 'attacking', label: 'Attacking' },
];

const selectedPlayers = computed(() => new Set(form.starting_lineup.map((playerId) => Number(playerId))));

const selectedCount = computed(() => selectedPlayers.value.size);

const toggleStartingPlayer = (playerId) => {
    const numericPlayerId = Number(playerId);

    if (selectedPlayers.value.has(numericPlayerId)) {
        form.starting_lineup = form.starting_lineup.filter((value) => Number(value) !== numericPlayerId);
        return;
    }

    if (form.starting_lineup.length >= 11) {
        return;
    }

    form.starting_lineup = [...form.starting_lineup, numericPlayerId];
};

const saveSettings = () => {
    router.post('/my-team/settings', form, {
        preserveScroll: true,
        preserveState: true,
    });
};
</script>

<template>
    <Head title="My Team" />

    <AppFrame>
        <section v-if="team" class="rounded-3xl border border-emerald-300/30 bg-zinc-900/70 p-6 shadow-xl shadow-emerald-900/20 lg:p-8">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-300">Club Profile</p>
            <h1 class="mt-2 text-3xl font-black tracking-tight text-zinc-100">{{ team.name }}</h1>
            <p class="mt-3 text-zinc-300">{{ team.club }} | {{ team.league }} | {{ team.division }}</p>
            <p class="mt-2 text-sm font-semibold text-amber-300">Budget: {{ formatCredits(team.budget) }}</p>
        </section>

        <section v-if="team" class="rounded-3xl border border-zinc-400/30 bg-zinc-900/65 p-5 shadow-sm lg:p-6">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-zinc-300">Tactics</p>
                    <h2 class="text-2xl font-bold tracking-tight text-zinc-100">Set your start XI and match style</h2>
                </div>

                <button
                    type="button"
                    class="rounded-lg bg-emerald-400 px-4 py-2 text-sm font-bold text-zinc-950 transition hover:bg-emerald-300"
                    @click="saveSettings"
                >
                    Save tactics
                </button>
            </div>

            <div class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                <label class="space-y-2 text-sm">
                    <span class="font-semibold text-zinc-200">Formation</span>
                    <select v-model="form.formation" class="w-full rounded-lg border border-zinc-600 bg-zinc-900 px-3 py-2 text-zinc-100">
                        <option v-for="option in formationOptions" :key="option" :value="option">{{ option }}</option>
                    </select>
                </label>

                <label class="space-y-2 text-sm">
                    <span class="font-semibold text-zinc-200">Mentality</span>
                    <select v-model="form.mentality" class="w-full rounded-lg border border-zinc-600 bg-zinc-900 px-3 py-2 text-zinc-100">
                        <option v-for="option in mentalityOptions" :key="option.value" :value="option.value">{{ option.label }}</option>
                    </select>
                </label>

                <label class="space-y-2 text-sm">
                    <span class="font-semibold text-zinc-200">Pressing {{ form.pressing }}</span>
                    <input v-model.number="form.pressing" type="range" min="0" max="100" class="w-full">
                </label>

                <label class="space-y-2 text-sm">
                    <span class="font-semibold text-zinc-200">Tempo {{ form.tempo }}</span>
                    <input v-model.number="form.tempo" type="range" min="0" max="100" class="w-full">
                </label>

                <label class="space-y-2 text-sm">
                    <span class="font-semibold text-zinc-200">Line Height {{ form.line_height }}</span>
                    <input v-model.number="form.line_height" type="range" min="0" max="100" class="w-full">
                </label>

                <label class="space-y-2 text-sm">
                    <span class="font-semibold text-zinc-200">Substitution Style {{ form.substitution_style }}</span>
                    <input v-model.number="form.substitution_style" type="range" min="0" max="100" class="w-full">
                </label>
            </div>

            <div class="mt-5 rounded-2xl border border-zinc-700 bg-zinc-950/40 p-4 text-sm text-zinc-300">
                Selected start XI: <span class="font-semibold text-emerald-300">{{ selectedCount }}/11</span>
                <p class="mt-1">Wenn du weniger als 11 Spieler auswählst, ergänzt die Simulation den Rest automatisch.</p>
            </div>
        </section>

        <section v-if="team" class="overflow-hidden rounded-2xl border border-zinc-400/35 bg-zinc-900/65 shadow-sm">
            <table class="min-w-full divide-y divide-zinc-700 text-sm">
                <thead class="bg-zinc-900/90">
                    <tr class="text-left text-zinc-300">
                        <th class="px-4 py-3 font-semibold">XI</th>
                        <th class="px-4 py-3 font-semibold">Player</th>
                        <th class="px-4 py-3 font-semibold">Position</th>
                        <th class="px-4 py-3 font-semibold">OVR</th>
                        <th class="px-4 py-3 font-semibold">Fitness</th>
                        <th class="px-4 py-3 font-semibold">Market Value</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800">
                    <tr v-for="player in players" :key="player.id" class="bg-zinc-900/45 text-zinc-100 hover:bg-zinc-800/65">
                        <td class="px-4 py-3">
                            <button
                                type="button"
                                class="rounded-full px-3 py-1 text-xs font-semibold transition"
                                :class="selectedPlayers.has(player.id) ? 'bg-emerald-400 text-zinc-950' : 'border border-zinc-500 text-zinc-200 hover:border-zinc-300'"
                                @click="toggleStartingPlayer(player.id)"
                            >
                                {{ selectedPlayers.has(player.id) ? 'Starting XI' : 'Bench' }}
                            </button>
                        </td>
                        <td class="px-4 py-3 font-medium">
                            <Link :href="`/my-team/players/${player.id}`" class="text-emerald-300 hover:text-emerald-200">
                                {{ player.name }}
                            </Link>
                        </td>
                        <td class="px-4 py-3">{{ player.position }}</td>
                        <td class="px-4 py-3">{{ player.overall }}</td>
                        <td class="px-4 py-3">{{ player.fitness }}</td>
                        <td class="px-4 py-3">{{ formatCredits(player.market_value) }}</td>
                    </tr>
                </tbody>
            </table>
        </section>

        <section v-else class="rounded-2xl border border-zinc-400/30 bg-zinc-900/65 p-5 text-zinc-200">
            No team assigned yet. Please contact support if this persists.
        </section>
    </AppFrame>
</template>
