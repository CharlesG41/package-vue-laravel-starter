<template>
    <div>
      <button @click="changeLanguage('en')">English</button>
      <button class="bg-gray-500 text-white px-4 py-2 rounded-md" @click="changeLanguage('fr')">Francasdasdadis tbnk</button>
      <p>Current language: {{ currentLocale }}</p>
      <p>Translated text: {{ trans('cyvian.static.add') }}</p>
    </div>
</template>
  
<script setup>
    import { ref } from 'vue'
    import { useTranslations } from '../composables/useTranslations'

    const { trans, setLocale, getLocale, currentLocale } = useTranslations()

    const changeLanguage = async (locale) => {
        console.log('changeLanguage', locale)
        await setLocale(locale)
    }
</script>