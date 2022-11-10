<template>
    <form class="login-form" @submit.prevent="loginButtonPressed">
        <h4>Login</h4>
        <label for="email">Login: <input name="email" type="email" placeholder="Email"></label>
        <label for="password">Password: <input name="password" type="password" placeholder="Password"></label>
        <button class="login">Login</button>
    </form>
</template>

<script>
    export default {
        data() {
          return {
              token: ''
          }
        },
        methods: {
            loginButtonPressed(e) {
                alert('test!');
            }
        },
        mounted() {
            console.log('Component mounted.')
        }
    }
</script>
