<template>
    <div>Baseinfo</div>
    <div>{{ model.baseinfo.address }}</div>
    <div>{{ model.baseinfo.phone }}</div>
    <div>{{ model.baseinfo.email }}</div>
    <div>{{ model.baseinfo.website }}</div>
    <div>{{ model.baseinfo.content }}</div>
    <div>{{ model.baseinfo.full_address }}</div>
</template>

<script setup>
defineProps({ model: Object })

</script>
