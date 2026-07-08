<script setup>
import { computed } from 'vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

const page = usePage();
const user = computed(() => page.props.auth?.user ?? null);
const logoutForm = useForm({});

const navLinks = computed(() => {
    if (user.value) {
        return [
            { label: 'Dashboard', href: '/dashboard' },
            { label: 'My Team', href: '/my-team' },
            { label: 'Leagues', href: '/leagues' },
            { label: 'Spielplan', href: '/spielplan' },
            { label: 'Transfer Market', href: '/transfer-market' },
        ];
    }

    return [{ label: 'Home', href: '/' }];
});

const logout = () => {
    logoutForm.post('/logout');
};
</script>

<template>
    <header class="sticky top-0 z-20 rounded-2xl border border-emerald-400/25 bg-zinc-900/70 px-5 py-4 backdrop-blur">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <Link href="/" class="text-xs font-semibold uppercase tracking-[0.25em] text-emerald-300">Baller Manager</Link>
                <p class="text-sm text-zinc-300">Real-time 6v6 League Management</p>
            </div>

            <nav class="flex flex-wrap items-center gap-2">
                <Link
                    v-for="link in navLinks"
                    :key="link.href"
                    :href="link.href"
                    class="rounded-lg border border-zinc-400/35 px-3 py-2 text-sm font-semibold text-zinc-100 transition hover:border-zinc-300"
                >
                    {{ link.label }}
                </Link>

                <template v-if="user">
                    <button
                        type="button"
                        class="rounded-lg bg-amber-400 px-3 py-2 text-sm font-bold text-zinc-900 transition hover:bg-amber-300"
                        @click="logout"
                    >
                        Logout
                    </button>
                </template>

                <template v-else>
                    <Link href="/login" class="rounded-lg border border-zinc-400/40 px-3 py-2 text-sm font-semibold text-zinc-100 transition hover:border-zinc-300">
                        Login
                    </Link>
                    <Link href="/#waitlist" class="rounded-lg bg-amber-400 px-3 py-2 text-sm font-semibold text-zinc-900 transition hover:bg-amber-300">
                        Join Waitlist
                    </Link>
                </template>
            </nav>
        </div>
    </header>
</template>
