<template>
    <div>
        <h1>{{ message }}</h1>
        {{data}}
    </div>
</template>

<script setup>
    import { ref } from 'vue';
    const message = 'Hello, Vue 3!';
    const data = ref('allo');
</script>