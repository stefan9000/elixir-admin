import Vue from 'vue';
import CKEditor from '@ckeditor/ckeditor5-vue';

window.Vue = Vue;
Vue.use(CKEditor);

Vue.prototype.$http = require('axios').default.create({
    headers: {
        'Authorization': 'Bearer ' + document.getElementById('api_token').value
    }
});
