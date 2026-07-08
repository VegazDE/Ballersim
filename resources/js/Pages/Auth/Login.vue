<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AppFrame from '../../Layouts/AppFrame.vue';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post('/login');
};
</script>

<template>
    <Head title="Login" />

    <AppFrame>
        <section class="mx-auto w-full max-w-md rounded-2xl border border-zinc-400/30 bg-zinc-900/70 p-7 shadow-sm">
            <h1 class="text-2xl font-bold tracking-tight text-zinc-100">Welcome back</h1>
            <p class="mt-2 text-sm text-zinc-300">Log in to manage your club and matchdays.</p>

            <form class="mt-6 space-y-4" @submit.prevent="submit">
                <div>
                    <label class="mb-1 block text-sm font-medium text-zinc-200" for="email">Email</label>
                    <input
                        id="email"
                        v-model="form.email"
                        type="email"
                        class="w-full rounded-lg border border-zinc-500/40 bg-zinc-900/75 px-3 py-2 text-sm text-zinc-100 outline-none transition focus:border-emerald-400"
                        required
                    >
                    <p v-if="form.errors.email" class="mt-1 text-xs text-red-600">{{ form.errors.email }}</p>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-zinc-200" for="password">Password</label>
                    <input
                        id="password"
                        v-model="form.password"
                        type="password"
                        class="w-full rounded-lg border border-zinc-500/40 bg-zinc-900/75 px-3 py-2 text-sm text-zinc-100 outline-none transition focus:border-emerald-400"
                        required
                    >
                    <p v-if="form.errors.password" class="mt-1 text-xs text-red-600">{{ form.errors.password }}</p>
                </div>

                <label class="flex items-center gap-2 text-sm text-zinc-300">
                    <input v-model="form.remember" type="checkbox" class="rounded border-zinc-300 text-emerald-600">
                    Stay signed in
                </label>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full rounded-lg bg-emerald-500 px-4 py-2.5 text-sm font-semibold text-zinc-950 transition hover:bg-emerald-400 disabled:opacity-60"
                >
                    Log in
                </button>
            </form>

            <p class="mt-5 text-sm text-zinc-300">
                New signups are currently closed. Join the waitlist on the homepage for launch access.
            </p>
        </section>
    </AppFrame>
</template>
