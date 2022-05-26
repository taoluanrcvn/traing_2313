import VueI18n from 'vue-i18n'
import vnMessage from '@/lang/vn.json'
import Vue from 'vue'
Vue.use(VueI18n);
const numberFormats = {
    'en-US': {
        currency: {
            style: 'currency',
            currency: 'USD'
        }
    },
    'ja-JP': {
        currency: {
            style: 'currency',
            currency: 'JPY',
            currencyDisplay: 'symbol'
        }
    }
}
export default new VueI18n({
    locale: 'vn', // set locale
    messages: { vn: vnMessage },
    fallbackLocale: 'vn',
    numberFormats
});