<template>
    <template v-if="$attrs['disabled']">
        <q-item :disable="$attrs['disabled']">
            <q-item-section avatar>
                <q-icon :name="$attrs['name']" />
            </q-item-section>
            <q-item-section>
                <q-item-label>
                    <slot></slot>
                </q-item-label>
            </q-item-section>
        </q-item>
    </template>
    <template v-else>
        <Link :href="$attrs['href']" as="div">
        <q-item clickable :active="$attrs['active']" active-class="text-green">
            <q-item-section avatar>
                <q-icon :name="$attrs['name']" />
            </q-item-section>
            <q-item-section>
                <q-item-label>
                    <slot></slot>
                </q-item-label>
            </q-item-section>
        </q-item>
    </Link>
    </template>

</template>

<script>

export default {
    inheritAttrs: false
}

</script>

<script setup>

import { Link } from '@inertiajs/inertia-vue3'

</script>
