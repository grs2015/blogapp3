<template>
    <div>
        {{ $t('Welcome to your Jetstream application!') }}
    </div>
    <button @click="loadLanguageAsync('de')">Change to Deutsch Language</button>
    <button @click="loadLanguageAsync('en')">Change to English Language</button>
    <button @click="loadLanguageAsync('pl')">Change to Polish Language</button>
    <div>
        {{ lang }} {{ loaded }}
    </div>
    <div>

    </div>
</template>

<script setup>
import { Link } from '@inertiajs/inertia-vue3'
import { getActiveLanguage } from 'laravel-vue-i18n';
import { isLoaded } from 'laravel-vue-i18n';
import { loadLanguageAsync } from 'laravel-vue-i18n';

defineProps({ name: String })

const lang = getActiveLanguage();
const loaded = isLoaded('de');

</script>
