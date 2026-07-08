<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import AppFrame from '../../Layouts/AppFrame.vue';

const page = usePage();
const user = page.props.auth?.user ?? null;

const roadmapPhases = [
    { stage: '01', title: 'Foundation and deployment stability', status: 'Live now', detail: 'Core stack, deploy path, server playbook.' },
    { stage: '02', title: 'League system core', status: 'Live now', detail: '6 leagues, 2 divisions each, base structure.' },
    { stage: '03', title: 'Fixtures and standings', status: 'In build', detail: 'Season schedules, league tables, drill-down pages.' },
    { stage: '04', title: 'Match simulation v1', status: 'Planned', detail: '7-minute gameplay resolution and event engine.' },
    { stage: '05', title: 'CPU teams and auto-fill logic', status: 'Live now', detail: 'CPU managers fill open league slots.' },
    { stage: '06', title: 'Matchday execution loop and persistence', status: 'Planned', detail: 'Run daily rounds and persist all outcomes.' },
    { stage: '07', title: 'Live ticker and realtime updates', status: 'Planned', detail: 'Minute-by-minute feed for active games.' },
    { stage: '08', title: 'Tactics and lineups', status: 'Planned', detail: 'Formations, substitutions, tactical presets.' },
    { stage: '09', title: 'Transfer market and draft integration', status: 'In build', detail: 'Listings, bids, contract decisions, squad churn.' },
    { stage: '10', title: 'Promotion, relegation and rollover', status: 'Planned', detail: 'League movement and new season generation.' },
    { stage: '11', title: 'Deep systems', status: 'Planned', detail: 'Training, fitness, injuries, scouting loops.' },
    { stage: '12', title: 'Extended systems', status: 'Planned', detail: 'Youth academy, loans, cup, and club items.' },
    { stage: '13', title: 'Community and cosmetic systems', status: 'Planned', detail: 'Social loops, visuals, and identity upgrades.' },
];

const waitlistForm = useForm({
    email: '',
});

const submitWaitlist = () => {
    waitlistForm.post('/waitlist', {
        preserveScroll: true,
        onSuccess: () => waitlistForm.reset('email'),
    });
};

const statusClass = (status) => {
    if (status === 'Live now') {
        return 'border-emerald-300/50 bg-emerald-400/10 text-emerald-200';
    }

    if (status === 'In build') {
        return 'border-amber-300/50 bg-amber-400/10 text-amber-200';
    }

    return 'border-zinc-300/30 bg-zinc-400/10 text-zinc-200';
};
</script>

<template>
    <Head title="Baller Manager" />

    <AppFrame>
        <section class="hero-shell relative overflow-hidden rounded-3xl border border-emerald-300/30 bg-zinc-900/75 p-7 shadow-2xl shadow-emerald-900/25 lg:p-10">
            <div class="orb orb-a" />
            <div class="orb orb-b" />
            <div class="orb orb-c" />

            <div class="relative z-10 grid gap-6 lg:grid-cols-[1.2fr_0.8fr]">
                <article>
                    <p class="text-sm font-semibold uppercase tracking-[0.25em] text-amber-300">Waitlist Phase</p>
                    <h1 class="mt-3 text-4xl font-black leading-tight tracking-tight text-zinc-100 lg:text-6xl">
                        Baller Manager Roadmap
                        <span class="block text-emerald-300">Everything planned. One timeline.</span>
                    </h1>
                    <p class="mt-5 max-w-3xl text-base leading-relaxed text-zinc-300">
                        Registration is currently closed. We are rolling out features in focused phases: league depth,
                        simulation quality, tactical control, and long-term club systems.
                    </p>

                    <div class="mt-8 flex flex-wrap gap-3">
                        <a href="#waitlist" class="rounded-xl bg-amber-400 px-5 py-3 text-sm font-bold text-zinc-900 transition hover:bg-amber-300">
                            Join Waitlist
                        </a>
                        <Link
                            v-if="user"
                            href="/dashboard"
                            class="rounded-xl border border-zinc-400/50 px-5 py-3 text-sm font-semibold text-zinc-100 transition hover:border-zinc-300"
                        >
                            Open Dashboard
                        </Link>
                    </div>
                </article>

                <aside class="space-y-4">
                    <div class="rounded-2xl border border-zinc-400/30 bg-zinc-950/45 p-5">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-zinc-300">Current Match Format</p>
                        <p class="mt-2 text-4xl font-black text-emerald-300">7 min</p>
                        <p class="mt-2 text-sm text-zinc-300">Short real-time rounds with high tactical impact.</p>
                    </div>
                    <div class="rounded-2xl border border-zinc-400/30 bg-zinc-950/45 p-5">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-zinc-300">Access Policy</p>
                        <p class="mt-2 text-xl font-bold text-zinc-100">Waitlist only</p>
                        <p class="mt-2 text-sm text-zinc-300">New accounts are paused until the next rollout wave.</p>
                    </div>
                </aside>
            </div>
        </section>

        <section class="rounded-3xl border border-zinc-400/30 bg-zinc-900/70 p-7 shadow-xl shadow-zinc-900/35 lg:p-10">
            <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.25em] text-emerald-300">Master Roadmap</p>
                    <h2 class="mt-2 text-3xl font-black tracking-tight text-zinc-100">All planned features</h2>
                </div>
                <p class="text-sm text-zinc-300">13 phases from launch foundation to community systems.</p>
            </div>

            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                <article
                    v-for="(phase, index) in roadmapPhases"
                    :key="phase.stage"
                    class="road-card rounded-2xl border border-zinc-500/35 bg-zinc-950/45 p-5"
                    :style="{ animationDelay: `${index * 70}ms` }"
                >
                    <div class="flex items-center justify-between gap-3">
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-zinc-300">Phase {{ phase.stage }}</p>
                        <span class="rounded-full border px-2.5 py-1 text-xs font-semibold" :class="statusClass(phase.status)">{{ phase.status }}</span>
                    </div>
                    <h3 class="mt-3 text-lg font-bold leading-tight text-zinc-100">{{ phase.title }}</h3>
                    <p class="mt-2 text-sm leading-relaxed text-zinc-300">{{ phase.detail }}</p>
                </article>
            </div>
        </section>

        <section id="waitlist" class="rounded-3xl border border-amber-300/30 bg-zinc-900/70 p-7 shadow-xl shadow-amber-900/25 lg:p-10">
            <div class="grid gap-6 lg:grid-cols-[1fr_0.9fr]">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.25em] text-amber-300">Early Access</p>
                    <h2 class="mt-3 text-3xl font-black tracking-tight text-zinc-100">Get notified for launch waves</h2>
                    <p class="mt-3 max-w-xl text-zinc-300">
                        Drop your email and receive updates when new manager slots open and key roadmap milestones go live.
                    </p>
                </div>

                <form class="rounded-2xl border border-zinc-400/30 bg-zinc-950/45 p-5" @submit.prevent="submitWaitlist">
                    <label class="mb-2 block text-sm font-semibold text-zinc-100" for="waitlist-email">Email address</label>
                    <input
                        id="waitlist-email"
                        v-model="waitlistForm.email"
                        type="email"
                        class="w-full rounded-xl border border-zinc-500/50 bg-zinc-900/80 px-4 py-3 text-sm text-zinc-100 outline-none transition placeholder:text-zinc-400 focus:border-emerald-300"
                        placeholder="you@clubmail.com"
                        required
                    >
                    <p v-if="waitlistForm.errors.email" class="mt-2 text-xs text-rose-300">{{ waitlistForm.errors.email }}</p>

                    <button
                        type="submit"
                        :disabled="waitlistForm.processing"
                        class="mt-4 w-full rounded-xl bg-amber-400 px-4 py-3 text-sm font-bold text-zinc-900 transition hover:bg-amber-300 disabled:opacity-60"
                    >
                        {{ waitlistForm.processing ? 'Submitting...' : 'Get on the waitlist' }}
                    </button>
                    <p class="mt-3 text-xs text-zinc-400">Email is stored server-side for launch updates only.</p>
                </form>
            </div>
        </section>
    </AppFrame>
</template>

<style scoped>
.hero-shell {
    background-image: radial-gradient(circle at 0% 0%, rgba(16, 185, 129, 0.2), transparent 40%), radial-gradient(circle at 90% 20%, rgba(251, 191, 36, 0.2), transparent 35%);
}

.orb {
    position: absolute;
    border-radius: 9999px;
    filter: blur(24px);
    animation: float-pulse 7s ease-in-out infinite;
}

.orb-a {
    width: 180px;
    height: 180px;
    left: -40px;
    top: -30px;
    background: rgba(16, 185, 129, 0.22);
}

.orb-b {
    width: 140px;
    height: 140px;
    right: 8%;
    top: 12%;
    background: rgba(251, 191, 36, 0.2);
    animation-delay: 0.8s;
}

.orb-c {
    width: 120px;
    height: 120px;
    right: -25px;
    bottom: -15px;
    background: rgba(63, 63, 70, 0.3);
    animation-delay: 1.4s;
}

.road-card {
    opacity: 0;
    transform: translateY(16px);
    animation: road-enter 0.6s ease forwards;
}

@keyframes float-pulse {
    0%,
    100% {
        transform: translateY(0) scale(1);
    }
    50% {
        transform: translateY(-10px) scale(1.05);
    }
}

@keyframes road-enter {
    from {
        opacity: 0;
        transform: translateY(16px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
