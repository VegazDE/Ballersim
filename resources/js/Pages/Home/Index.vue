<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';

const page = usePage();
const user = page.props.auth?.user ?? null;
</script>

<template>
    <Head title="Baller Manager" />

    <main class="min-h-screen bg-[radial-gradient(circle_at_top_left,_#f7fee7_0%,_#ecfccb_30%,_#fefce8_65%,_#fff7ed_100%)] text-zinc-900">
        <section class="mx-auto flex w-full max-w-6xl flex-col gap-10 px-6 py-10 lg:py-14">
            <header class="flex items-center justify-between rounded-2xl border border-zinc-200 bg-white/75 px-5 py-4 backdrop-blur">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.25em] text-emerald-700">Baller Manager</p>
                    <p class="text-sm text-zinc-600">Real-time 6v6 League Management</p>
                </div>

                <div class="flex items-center gap-2">
                    <Link
                        v-if="!user"
                        href="/login"
                        class="rounded-lg border border-zinc-300 px-4 py-2 text-sm font-semibold text-zinc-700 transition hover:border-zinc-400"
                    >
                        Login
                    </Link>
                    <Link
                        v-if="!user"
                        href="/register"
                        class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700"
                    >
                        Register
                    </Link>
                    <Link
                        v-if="user"
                        href="/dashboard"
                        class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700"
                    >
                        Open Dashboard
                    </Link>
                </div>
            </header>

            <section class="grid gap-6 lg:grid-cols-[1.15fr_0.85fr]">
                <article class="rounded-3xl border border-zinc-200 bg-white/80 p-7 shadow-sm backdrop-blur lg:p-10">
                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-amber-700">Season Kickoff</p>
                    <h1 class="mt-3 text-4xl font-black leading-tight tracking-tight text-zinc-900 lg:text-6xl">
                        Build your squad.
                        <span class="text-emerald-700">Own every matchday.</span>
                    </h1>
                    <p class="mt-5 max-w-2xl text-base leading-relaxed text-zinc-600">
                        Manage your club through compact 7-minute real-time games, tactical decisions, and league races.
                        Start in Liga 6 and climb to the top through pure performance.
                    </p>

                    <div class="mt-8 flex flex-wrap gap-3">
                        <Link
                            :href="user ? '/transfer-market' : '/register'"
                            class="rounded-xl bg-zinc-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-zinc-700"
                        >
                            {{ user ? 'Open Transfer Market' : 'Create Manager Account' }}
                        </Link>
                        <Link
                            v-if="user"
                            href="/dashboard"
                            class="rounded-xl border border-zinc-300 px-5 py-3 text-sm font-semibold text-zinc-700 transition hover:border-zinc-500"
                        >
                            View Dashboard
                        </Link>
                    </div>
                </article>

                <aside class="grid gap-4">
                    <div class="rounded-2xl border border-zinc-200 bg-white/80 p-5 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-zinc-500">Core Loop</p>
                        <p class="mt-2 text-xl font-bold text-zinc-900">League first roadmap is active</p>
                        <p class="mt-2 text-sm text-zinc-600">Leagues, divisions, and automatic CPU teams are in place as the current foundation.</p>
                    </div>
                    <div class="rounded-2xl border border-zinc-200 bg-white/80 p-5 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-zinc-500">Realtime Format</p>
                        <p class="mt-2 text-3xl font-black text-emerald-700">7 min</p>
                        <p class="mt-2 text-sm text-zinc-600">Compact match sessions with strategic substitutions and tactical shifts.</p>
                    </div>
                </aside>
            </section>
        </section>
    </main>
</template>
