<script>
  export default {
    layout: Layout
}
</script>

<script setup>
import { Link } from '@inertiajs/inertia-vue3'
import { getActiveLanguage } from 'laravel-vue-i18n';
import { isLoaded } from 'laravel-vue-i18n';
import { loadLanguageAsync } from 'laravel-vue-i18n';
import Layout from '@/Shared/PublicLayout.vue'

defineProps({ name: String })

const lang = getActiveLanguage();
const loaded = isLoaded('de');

</script>

<template>
    Public part
</template>


