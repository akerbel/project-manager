<template>
    <div v-if="message" class="info-message">{{ message }}</div>
    <div v-if="showLogoutButton" class="account-buttons">
        <button @click="logout()">Logout</button>
    </div>
    <router-view></router-view>
</template>

<script>
import { api } from '../api.js'

export default {
    data() {
        return {
            api,
            showLogoutButton: false,
            message: ''
        }
    },
    watch: {
        'api.token'(newValue) {
            if (!api.isLoggedIn()) {
                this.$router.push('/login');
                this.showLogoutButton = false;
            }
            else {
                this.$router.push('/projects');
                this.showLogoutButton = true;
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
            this.$router.push('/login');
        },
    },
    mounted() {
        api.token = api.getToken();
        if (this.$route.query.verified === '1') {
            this.message = 'Email is verified. Now you can sign in.';
        }
    }
}
</script>
