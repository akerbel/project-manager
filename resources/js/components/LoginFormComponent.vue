<template>
    <form class="login-form" @submit.prevent="loginButtonPressed">
        <h4>Login</h4>
        <label for="email">Email: <input v-model="email" placeholder="Email"></label>
        <label for="password">Password: <input v-model="password" placeholder="Password" type="password"></label>
        <button class="login">Login</button>
        <div v-if="message" class="error-message">{{ message }}</div>
    </form>
</template>

<script>
    import { api } from '../api.js';

    export default {
        data() {
          return {
              api,
              message: '',
              email: '',
              password: ''
          }
        },
        methods: {
            loginButtonPressed() {
                api.call(
                    'POST',
                    'login',
                    {
                        email: this.email,
                        password: this.password
                    }
                )
                    .then((response) => {
                        api.setToken(response.data.access_token);
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
