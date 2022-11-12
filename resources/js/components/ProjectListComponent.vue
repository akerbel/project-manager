<template>
    <div v-if="message" class="error-message">{{ message }}</div>
    <div>{{ projects }}</div>
</template>

<script>
import { api } from '../api.js';

export default {
    data() {
        return {
            api,
            projects: [],
            message: '',
        }
    },
    methods: {
        getProjects() {
            api.call(
                'GET',
                'projects',
            )
                .then((response) => {
                    this.projects = response.data;
                })
                .catch((error) => {
                    this.message = error.response.data.error;
                });
        }
    },
    mounted() {
        this.getProjects();
        console.log('ProjectList component mounted.')
    }
}
</script>
