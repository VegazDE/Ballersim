<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppFrame from '../../Layouts/AppFrame.vue';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post('/register');
};
</script>

<template>
    <Head title="Register" />

    <AppFrame>
        <section class="mx-auto w-full max-w-md rounded-2xl border border-zinc-400/30 bg-zinc-900/70 p-7 shadow-sm">
            <h1 class="text-2xl font-bold tracking-tight text-zinc-100">Create your manager account</h1>
            <p class="mt-2 text-sm text-zinc-300">Join the league pyramid and start your climb.</p>

            <form class="mt-6 space-y-4" @submit.prevent="submit">
                <div>
                    <label class="mb-1 block text-sm font-medium text-zinc-200" for="name">Name</label>
                    <input
                        id="name"
                        v-model="form.name"
                        type="text"
                        class="w-full rounded-lg border border-zinc-500/40 bg-zinc-900/75 px-3 py-2 text-sm text-zinc-100 outline-none transition focus:border-emerald-400"
                        required
                    >
                    <p v-if="form.errors.name" class="mt-1 text-xs text-red-600">{{ form.errors.name }}</p>
                </div>

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

                <div>
                    <label class="mb-1 block text-sm font-medium text-zinc-200" for="password_confirmation">Confirm password</label>
                    <input
                        id="password_confirmation"
                        v-model="form.password_confirmation"
                        type="password"
                        class="w-full rounded-lg border border-zinc-500/40 bg-zinc-900/75 px-3 py-2 text-sm text-zinc-100 outline-none transition focus:border-emerald-400"
                        required
                    >
                </div>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full rounded-lg bg-emerald-500 px-4 py-2.5 text-sm font-semibold text-zinc-950 transition hover:bg-emerald-400 disabled:opacity-60"
                >
                    Create account
                </button>
            </form>

            <p class="mt-5 text-sm text-zinc-300">
                Already registered?
                <Link href="/login" class="font-semibold text-emerald-300 hover:text-emerald-200">Log in</Link>
            </p>
        </section>
    </AppFrame>
</template>
