<template>
    <div class="flex justify-center">
        <form class="login-form flex justify-center flex-column" @submit.prevent="loginButtonPressed">
            <h4 class="text-xl text-center font-bold">Login</h4>
            <label for="email" class="font-bold m-1">
                Email
                <input v-model="email" placeholder="Email" class="m-2 p-1 rounded">
            </label>
            <label for="password" class="font-bold m-1">
                Password
                <input v-model="password" placeholder="Password" type="password" class="m-2 p-1 rounded">
            </label>
            <div class="buttons flex">
                <button class="btn-login bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 mr-2 rounded">Login</button>
                <button
                    class="btn-register bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-2 mr-2 rounded"
                    @click="this.$router.push('/register');"
                >
                    Registration
                </button>
            </div>
            <message-component :message="message"></message-component>
        </form>
    </div>
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
