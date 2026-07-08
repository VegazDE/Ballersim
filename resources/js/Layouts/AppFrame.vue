<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import SiteHeader from '../Components/SiteHeader.vue';

const page = usePage();
const flash = computed(() => page.props.flash ?? {});
</script>

<template>
    <main class="min-h-screen bg-[linear-gradient(120deg,_#0f172a_0%,_#111827_40%,_#052e16_100%)] text-zinc-100">
        <section class="mx-auto flex w-full max-w-6xl flex-col gap-6 px-6 py-8 lg:py-10">
            <SiteHeader />

            <p
                v-if="flash.success"
                class="rounded-lg border border-emerald-400/40 bg-emerald-500/15 px-4 py-3 text-sm font-medium text-emerald-200"
            >
                {{ flash.success }}
            </p>

            <p
                v-if="flash.error"
                class="rounded-lg border border-rose-400/40 bg-rose-500/15 px-4 py-3 text-sm font-medium text-rose-200"
            >
                {{ flash.error }}
            </p>

            <slot />
        </section>
    </main>
</template>
