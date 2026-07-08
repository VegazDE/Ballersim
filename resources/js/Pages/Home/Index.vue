<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import AppFrame from '../../Layouts/AppFrame.vue';

const page = usePage();
const user = page.props.auth?.user ?? null;
const flash = page.props.flash ?? {};

const waitlistForm = useForm({
    email: '',
});

const submitWaitlist = () => {
    waitlistForm.post('/waitlist', {
        preserveScroll: true,
        onSuccess: () => waitlistForm.reset('email'),
    });
};
</script>

<template>
    <Head title="Baller Manager" />

    <AppFrame>
        <section class="grid gap-6 lg:grid-cols-[1.15fr_0.85fr]">
                <article class="rounded-3xl border border-emerald-300/30 bg-zinc-900/70 p-7 shadow-xl shadow-emerald-900/20 backdrop-blur lg:p-10">
                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-amber-300">Season Kickoff</p>
                    <h1 class="mt-3 text-4xl font-black leading-tight tracking-tight text-zinc-100 lg:text-6xl">
                        Build your squad.
                        <span class="text-emerald-300">Own every matchday.</span>
                    </h1>
                    <p class="mt-5 max-w-2xl text-base leading-relaxed text-zinc-300">
                        Manage your club through compact 7-minute real-time games, tactical decisions, and league races.
                        Start in Liga 6 and climb to the top through pure performance.
                    </p>

                    <div class="mt-8 flex flex-wrap gap-3">
                        <Link
                            :href="user ? '/transfer-market' : '/register'"
                            class="rounded-xl bg-emerald-500 px-5 py-3 text-sm font-semibold text-zinc-950 transition hover:bg-emerald-400"
                        >
                            {{ user ? 'Open Transfer Market' : 'Create Manager Account' }}
                        </Link>
                        <Link
                            v-if="user"
                            href="/dashboard"
                            class="rounded-xl border border-zinc-400/50 px-5 py-3 text-sm font-semibold text-zinc-100 transition hover:border-zinc-300"
                        >
                            View Dashboard
                        </Link>
                    </div>
                </article>

                <aside class="grid gap-4">
                    <div class="rounded-2xl border border-zinc-400/30 bg-zinc-900/65 p-5 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-zinc-300">Core Loop</p>
                        <p class="mt-2 text-xl font-bold text-zinc-100">League first roadmap is active</p>
                        <p class="mt-2 text-sm text-zinc-300">Leagues, divisions, and automatic CPU teams are in place as the current foundation.</p>
                    </div>
                    <div class="rounded-2xl border border-zinc-400/30 bg-zinc-900/65 p-5 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-zinc-300">Realtime Format</p>
                        <p class="mt-2 text-3xl font-black text-emerald-300">7 min</p>
                        <p class="mt-2 text-sm text-zinc-300">Compact match sessions with strategic substitutions and tactical shifts.</p>
                    </div>
                </aside>
            </section>

            <section class="rounded-3xl border border-amber-300/30 bg-zinc-900/70 p-7 shadow-xl shadow-amber-900/20 lg:p-10">
                <div class="grid gap-6 lg:grid-cols-[1fr_0.9fr]">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.25em] text-amber-300">Early Access</p>
                        <h2 class="mt-3 text-3xl font-black tracking-tight text-zinc-100">Join the pre-registration list</h2>
                        <p class="mt-3 max-w-xl text-zinc-300">
                            Drop your email and get notified as soon as league seasons, live matchdays, and public beta slots are available.
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
