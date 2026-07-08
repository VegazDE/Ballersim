<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';

const page = usePage();
const user = page.props.auth?.user;
const logoutForm = useForm({});

const logout = () => {
    logoutForm.post('/logout');
};
</script>

<template>
    <Head title="Dashboard" />

    <main class="min-h-screen bg-zinc-100 px-4 py-8 lg:px-8">
        <section class="mx-auto max-w-6xl space-y-5">
            <header class="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm">
                <div>
                    <p class="text-sm text-zinc-500">Signed in as</p>
                    <h1 class="text-2xl font-bold tracking-tight text-zinc-900">{{ user?.name }}</h1>
                </div>

                <div class="flex flex-wrap gap-2">
                    <Link
                        href="/transfer-market"
                        class="rounded-lg bg-zinc-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-zinc-700"
                    >
                        Transfer Market
                    </Link>
                    <button
                        type="button"
                        class="rounded-lg border border-zinc-300 px-4 py-2 text-sm font-semibold text-zinc-700 transition hover:border-zinc-500"
                        @click="logout"
                    >
                        Logout
                    </button>
                </div>
            </header>

            <section class="grid gap-4 md:grid-cols-3">
                <article class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-zinc-500">Progress</p>
                    <p class="mt-2 text-lg font-bold text-zinc-900">League bootstrap complete</p>
                    <p class="mt-2 text-sm text-zinc-600">6 leagues and 2 divisions each are ready for season logic.</p>
                </article>

                <article class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-zinc-500">Next Build</p>
                    <p class="mt-2 text-lg font-bold text-zinc-900">Standings and fixtures</p>
                    <p class="mt-2 text-sm text-zinc-600">League table screens and match schedule are the next core module.</p>
                </article>

                <article class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-zinc-500">Target Format</p>
                    <p class="mt-2 text-lg font-bold text-zinc-900">7-minute realtime matches</p>
                    <p class="mt-2 text-sm text-zinc-600">Compact sessions for high frequency manager decisions.</p>
                </article>
            </section>
        </section>
    </main>
</template>
