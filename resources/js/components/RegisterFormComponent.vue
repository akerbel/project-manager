<template>
    <form class="login-form" @submit.prevent="singUpButtonPressed">
        <h4>Registrate</h4>
        <label for="name">Name: <input v-model="name" placeholder="Name"></label>
        <label for="email">Email: <input v-model="email" placeholder="Email"></label>
        <label for="password">Password: <input v-model="password" placeholder="Password" type="password"></label>
        <label for="password">Repeat Password: <input v-model="passwordRepeat" placeholder="Password" type="password"></label>
        <button class="login">Sign Up</button>
        <div v-if="message" class="error-message">{{ message }}</div>
    </form>
</template>

<script>
import { api } from '../api.js'

export default {
    data() {
        return {
            api,
            message: '',
            name: '',
            email: '',
            password: '',
            passwordRepeat: ''
        }
    },
    methods: {
        singUpButtonPressed(e) {
            if (this.password !== this.passwordRepeat) {
                this.message = 'Password fields should match.';
                return;
            }

            api.call(
                'POST',
                'register',
                {
                    name: this.name,
                    email: this.email,
                    password: this.password
                }
            )
                .then((response) => {
                    this.message = response.data.message;
                })
                .catch((error) => {
                    this.message = error.response.data.error;
                });
        }
    },
    mounted() {
        console.log('Component mounted.')
    }
}
</script>
