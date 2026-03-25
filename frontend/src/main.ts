import { createApp } from 'vue'
import { createPinia } from 'pinia'
import { gsap } from 'gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'
import router from './router'
import i18n from './i18n'
import App from './App.vue'
import './styles/main.css'

gsap.registerPlugin(ScrollTrigger)

const app = createApp(App)
const pinia = createPinia()

app.use(pinia)
app.use(router)
app.use(i18n)
app.mount('#app')
