<template>
    <div class="w-1/2 mx-auto">
        <message-component :message="message"></message-component>
        <button
            v-if="showLogoutButton"
            class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded float-right"
            @click="logout()">
            Logout
        </button>
        <router-view></router-view>
    </div>
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
