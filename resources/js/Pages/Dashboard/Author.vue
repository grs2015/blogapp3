<script lang="ts">

import Layout from '@/Shared/LayoutAuthor.vue'

export default {
    layout: Layout,
}

</script>



<script setup lang="ts">

import { usePage } from '@inertiajs/inertia-vue3';

</script>

<template>
  <div>
    {{ $t('Welcome') }}, {{ usePage().props.value.auth.user.role }}
  </div>
</template>

<!-- <script>
  import Layout from '@/Shared/Layout.vue'

  export default {
    layout: Layout,
  }
</script> -->
