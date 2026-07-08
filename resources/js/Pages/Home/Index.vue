<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';

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

    <main class="min-h-screen bg-[linear-gradient(120deg,_#0f172a_0%,_#111827_40%,_#052e16_100%)] text-zinc-100">
        <section class="mx-auto flex w-full max-w-6xl flex-col gap-10 px-6 py-10 lg:py-14">
            <header class="flex items-center justify-between rounded-2xl border border-emerald-400/25 bg-zinc-900/55 px-5 py-4 backdrop-blur">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.25em] text-emerald-300">Baller Manager</p>
                    <p class="text-sm text-zinc-300">Real-time 6v6 League Management</p>
                </div>

                <div class="flex items-center gap-2">
                    <Link
                        v-if="!user"
                        href="/login"
                        class="rounded-lg border border-zinc-400/40 px-4 py-2 text-sm font-semibold text-zinc-100 transition hover:border-zinc-300"
                    >
                        Login
                    </Link>
                    <Link
                        v-if="!user"
                        href="/register"
                        class="rounded-lg bg-emerald-500 px-4 py-2 text-sm font-semibold text-zinc-950 transition hover:bg-emerald-400"
                    >
                        Register
                    </Link>
                    <Link
                        v-if="user"
                        href="/dashboard"
                        class="rounded-lg bg-emerald-500 px-4 py-2 text-sm font-semibold text-zinc-950 transition hover:bg-emerald-400"
                    >
                        Open Dashboard
                    </Link>
                </div>
            </header>

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

                        <p v-if="flash.success" class="mt-4 rounded-lg border border-emerald-400/40 bg-emerald-500/15 px-4 py-3 text-sm font-medium text-emerald-200">
                            {{ flash.success }}
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
        </section>
    </main>
</template>
