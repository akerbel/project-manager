<template>
    <div v-if="message" class="info-message">{{ message }}</div>
    <div class="account-buttons">
        <button @click="logout()">Logout</button>
    </div>
    <div class="account-buttons">
        <button @click="goTo('login')">Login</button>
    </div>
    <div class="account-buttons">
        <button @click="goTo('register')">Register</button>
    </div>
    <router-view></router-view>
</template>

<script>
import { api } from '../api.js'

export default {
    data() {
        return {
            api,
            message: ''
        }
    },
    watch: {
        'api.token'(newValue) {
            if (!api.isLoggedIn()) {
                this.goTo('login');
            }
            else {
                this.goTo('projects');
            }
        },
        '$route.query.verified'(newValue) {
            if (newValue === 1) {
                this.message = 'Email is verified. Now you can sign in.';
            }
        }
    },
    methods: {
        logout() {
            api.dropToken();
            this.goTo('login');
        },

        goTo(route) {
            this.$router.push('/' + route);
        }
    },
    mounted() {
        api.token = api.getToken();
        if (this.$route.query.verified === '1') {
            this.message = 'Email is verified. Now you can sign in.'
        }
    }
}
</script>
