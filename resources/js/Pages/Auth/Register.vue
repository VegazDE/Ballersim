<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

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

    <main class="flex min-h-screen items-center justify-center bg-zinc-100 px-4 py-10">
        <section class="w-full max-w-md rounded-2xl border border-zinc-200 bg-white p-7 shadow-sm">
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900">Create your manager account</h1>
            <p class="mt-2 text-sm text-zinc-600">Join the league pyramid and start your climb.</p>

            <form class="mt-6 space-y-4" @submit.prevent="submit">
                <div>
                    <label class="mb-1 block text-sm font-medium text-zinc-700" for="name">Name</label>
                    <input
                        id="name"
                        v-model="form.name"
                        type="text"
                        class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm outline-none transition focus:border-emerald-600"
                        required
                    >
                    <p v-if="form.errors.name" class="mt-1 text-xs text-red-600">{{ form.errors.name }}</p>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-zinc-700" for="email">Email</label>
                    <input
                        id="email"
                        v-model="form.email"
                        type="email"
                        class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm outline-none transition focus:border-emerald-600"
                        required
                    >
                    <p v-if="form.errors.email" class="mt-1 text-xs text-red-600">{{ form.errors.email }}</p>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-zinc-700" for="password">Password</label>
                    <input
                        id="password"
                        v-model="form.password"
                        type="password"
                        class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm outline-none transition focus:border-emerald-600"
                        required
                    >
                    <p v-if="form.errors.password" class="mt-1 text-xs text-red-600">{{ form.errors.password }}</p>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-zinc-700" for="password_confirmation">Confirm password</label>
                    <input
                        id="password_confirmation"
                        v-model="form.password_confirmation"
                        type="password"
                        class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm outline-none transition focus:border-emerald-600"
                        required
                    >
                </div>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-700 disabled:opacity-60"
                >
                    Create account
                </button>
            </form>

            <p class="mt-5 text-sm text-zinc-600">
                Already registered?
                <Link href="/login" class="font-semibold text-emerald-700 hover:text-emerald-800">Log in</Link>
            </p>
        </section>
    </main>
</template>
