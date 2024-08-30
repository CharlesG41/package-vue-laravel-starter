import { createApp } from 'vue'
import ExampleComponent from '@cms/components/ExampleComponent.vue'
import { useTranslations } from './composables/useTranslations'
import '../css/app.css'

export function init(el, initialTranslations) {
    const app = createApp({
        setup() {
        const { initTranslations, getLanguagePreference, setLocale, trans } = useTranslations()
        const storedLocale = getLanguagePreference()
        initTranslations(initialTranslations, storedLocale)

        return {
            setLocale,
            trans
        }
        },
        template: '<example-component></example-component>'
    })

    app.component('example-component', ExampleComponent)

    app.mount(el)
}