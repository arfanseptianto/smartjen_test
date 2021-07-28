import { createApp } from 'vue'
import App from './App.vue'

//import router
import router from './router'
import axios from 'axios'

//import Bootstrap, Popper, jQuery
import 'bootstrap/dist/css/bootstrap.css'
import '@popperjs/core/dist/umd/popper.min'
import 'bootstrap/dist/js/bootstrap.min'
import 'bootstrap-icons/font/bootstrap-icons.css'
import '@/assets/css/style.css'

const app = createApp(App)

app.config.globalProperties.loader = true;

axios.defaults.withCredentials = true
axios.defaults.headers.common['Accept'] = 'application/json'

//use vue router
app.use(router)

app.mount('#app')
