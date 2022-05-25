import VueI18n from 'vue-i18n'
import vnMessage from '@/lang/vn.json'
import Vue from 'vue'
Vue.use(VueI18n);
export default new VueI18n({
    locale: 'vn', // set locale
    messages: { vn: vnMessage },
    fallbackLocale: 'vn',
});