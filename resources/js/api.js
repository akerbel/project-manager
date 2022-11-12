import { reactive } from 'vue';
import axios from 'axios';

export const api = reactive({
    token: '',

    isLoggedIn: function() {
        let token = this.getToken();
        return (typeof token == 'string' && token !== '');
    },

    getToken: function() {
        return localStorage.getItem('token');
    },

    setToken: function(token) {
        this.token = token;
        localStorage.setItem('token', token);
    },

    dropToken: function() {
        this.token = '';
        localStorage.removeItem('token');
    },

    url: function(url) {
        return '/api/' + url;
    },

    headers: function() {
        return {'Authorization': 'Bearer ' + api.getToken()};
    },

    call: function(method, url, data = {}) {
        if (this.isLoggedIn()) {
            return axios({
                method: method,
                url: api.url(url),
                headers: api.headers(),
                responseType: 'json',
                data: data
            });
        }
        else {
            return axios({
                method: method,
                url: api.url(url),
                responseType: 'json',
                data: data
            });
        }
    }

})
