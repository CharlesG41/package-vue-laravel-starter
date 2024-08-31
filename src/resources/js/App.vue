<script setup>
    import { useTranslations } from '@cms/composables/useTranslations.js'
    const { trans, setLocale, currentLocale } = useTranslations()
</script>

<template>
    <div class="space-y-8">
        <div class="bg-yellow-500">{{trans('cyvian.static.add')}}</div>
        <button class="mx-2 p-2 border rounded border-blue-400" @click="setLocale('en')">EN</button>
        <button class="mx-2 p-2 border rounded border-blue-400"  @click="setLocale('fr')">FR</button>
        <div>
            Current locale is:  {{currentLocale}}
        </div>
    </div>
</template>
