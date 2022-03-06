require('./bootstrap')
import Vue from 'vue';
import VueRouter from 'vue-router';
import routes from './router/routes';
import VueSweetalert2 from 'vue-sweetalert2';
import {Ziggy} from './routes'
// If you don't need the styles, do not connect
import 'sweetalert2/dist/sweetalert2.min.css';
Vue.use(VueRouter);
Vue.component('loader', require('./components/utils/loader.vue').default);
Vue.component('multi_select', require('./components/utils/multiselect.vue').default);
Vue.use(VueSweetalert2);

const router = new VueRouter({
    mode: 'history',
    routes
});

const app = new Vue({
    name: 'App',
    router,
    el: '#app'
})
