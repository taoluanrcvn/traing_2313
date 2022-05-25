import Vue from 'vue'
import App from './App.vue'
import router from './router'
import vuetify from './plugins/vuetify'
import VueCookies from 'vue-cookies'
import i18n from "@/plugins/i18n";
import VueI18n from 'vue-i18n'
import vnMessage from './lang/vn.json'

Vue.config.productionTip = false
Vue.use(VueCookies, { expire: '30d'})
Vue.use(require('vue-moment'));
new Vue({
  router,
  vuetify,
  i18n,
  render: h => h(App)
}).$mount('#app')
