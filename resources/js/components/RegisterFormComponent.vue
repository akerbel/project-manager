<template>
    <div class="flex justify-center">
        <form class="login-form flex justify-center flex-column" @submit.prevent="singUpButtonPressed">
            <h4 class="text-xl text-center font-bold">Registrate</h4>
            <label for="name" class="font-bold m-1">
                Name
                <input v-model="name" placeholder="Name" class="m-2 p-1 rounded">
            </label>
            <label for="email" class="font-bold m-1">
                Email
                <input v-model="email" placeholder="Email" class="m-2 p-1 rounded">
            </label>
            <label for="password" class="font-bold m-1">
                Password
                <input v-model="password" placeholder="Password" type="password" class="m-2 p-1 rounded">
            </label>
            <label for="password" class="font-bold m-1">
                Repeat Password
                <input v-model="passwordRepeat" placeholder="Password" type="password" class="m-2 p-1 rounded">
            </label>
            <div class="buttons flex">
                <button class="btn-login bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 mr-2 rounded">Sign Up</button>
                <button
                    class="btn-register bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-2 mr-2 rounded"
                    @click="this.$router.push('/login');"
                >
                    Login
                </button>
            </div>
            <message-component :message="message"></message-component>
        </form>
    </div>
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
