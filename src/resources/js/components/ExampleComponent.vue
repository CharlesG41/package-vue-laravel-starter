<template>
    <div>
      <h1>{{ trans('cyvian.static.add') }}</h1>
      <button @click="changeLanguage('en')">English</button>
      <button class="bg-orange-500 text-white px-4 py-2 rounded-md" @click="changeLanguage('fr')">Français</button>
      <p>Current language: {{ currentLocale }}</p>
    </div>
</template>

<script>
export default {
    name: 'ExampleComponent',
    props: ['setLocale', 'trans', 'currentLocale'],
    methods: {
        changeLanguage(lang) {
            this.setLocale(lang)
        }
    }
}
</script>
