import { createApp } from 'vue'
import App from './App.vue'
import '../css/app.css'
import useTranslations from "@/composables/useTranslations";

const { trans, setLocale, getLocale } = useTranslations()
const app = createApp(App);
app.mixin({
    methods: {
        $trans: trans,
        $setLocale: setLocale,
        $locale() {
            return getLocale()
        },
        route
    }
})
app.mount('#app');