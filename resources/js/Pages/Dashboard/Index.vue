<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import AppFrame from '../../Layouts/AppFrame.vue';

const page = usePage();
const user = page.props.auth?.user;
</script>

<template>
    <Head title="Dashboard" />

    <AppFrame>
        <section class="space-y-5">
            <header class="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-zinc-400/30 bg-zinc-900/65 p-5 shadow-sm">
                <div>
                    <p class="text-sm text-zinc-300">Signed in as</p>
                    <h1 class="text-2xl font-bold tracking-tight text-zinc-100">{{ user?.name }}</h1>
                </div>
            </header>

            <section class="grid gap-4 md:grid-cols-3">
                <article class="rounded-xl border border-zinc-400/30 bg-zinc-900/65 p-5 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-zinc-300">Progress</p>
                    <p class="mt-2 text-lg font-bold text-zinc-100">League bootstrap complete</p>
                    <p class="mt-2 text-sm text-zinc-300">6 leagues and 2 divisions each are ready for season logic.</p>
                </article>

                <article class="rounded-xl border border-zinc-400/30 bg-zinc-900/65 p-5 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-zinc-300">Next Build</p>
                    <p class="mt-2 text-lg font-bold text-zinc-100">Standings and fixtures</p>
                    <p class="mt-2 text-sm text-zinc-300">League table screens and match schedule are the next core module.</p>
                    <Link href="/leagues" class="mt-3 inline-block text-sm font-semibold text-emerald-300 hover:text-emerald-200">
                        Open league overview
                    </Link>
                </article>

                <article class="rounded-xl border border-zinc-400/30 bg-zinc-900/65 p-5 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-zinc-300">Target Format</p>
                    <p class="mt-2 text-lg font-bold text-zinc-100">7-minute realtime matches</p>
                    <p class="mt-2 text-sm text-zinc-300">Compact sessions for high frequency manager decisions.</p>
                    <Link href="/transfer-market" class="mt-3 inline-block text-sm font-semibold text-amber-300 hover:text-amber-200">
                        Review transfer market
                    </Link>
                </article>
            </section>
        </section>
    </AppFrame>
</template>
