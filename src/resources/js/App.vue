<script setup>
    import { useTranslations } from '@cms/composables/useTranslations.js'
    const { trans, setLocale, currentLocale } = useTranslations()
</script>

<template>
    <div>
        <div class="bg-green-500">{{trans('cyvian.static.add')}}</div>
        <button @click="setLocale('en')">EN</button>
        <button @click="setLocale('fr')">FR</button>
        Current locale est: {{currentLocale}}
    </div>
</template>
