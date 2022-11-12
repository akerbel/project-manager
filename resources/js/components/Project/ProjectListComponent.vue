<template>
    <div><button @click="this.$router.push('/project/new')">New</button></div>
    <div v-if="message" class="error-message">{{ message }}</div>
    <ul>
        <li>
            <div>ID</div>
            <div>Name</div>
            <div>Description</div>
            <div>Categories</div>
            <div>Actions</div>
        </li>
        <li v-for="project in projects">
            <div>{{ project.id }}</div>
            <div>{{ project.name }}</div>
            <div>{{ project.description }}</div>
            <div>
                <span v-for="category in project.categories">{{ category.name }}, </span>
            </div>
            <div>
                <button @click="this.$router.push('/project/' + project.id)">Edit</button>
                <button @click="deleteProject(project.id)">Delete</button>
            </div>
        </li>
    </ul>
</template>

<script>
import { api } from '../../api.js';

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
        },

        deleteProject(id) {
            if (confirm('Do you really want to delete project #' + id.toString() + '?')) {
                api.call(
                    'DELETE',
                    'project/' + id.toString()
                )
                    .then((response) => {
                        this.message = 'The project #' + id.toString() + ' has been deleted';
                        let index = this.projects.findIndex((item) => {
                            return item.id === id;
                        });
                        this.projects.splice(index, 1);
                    })
                    .catch((error) => {
                        this.message = error.response.data.error;
                    });
            }
        }
    },
    mounted() {
        this.getProjects();
        console.log('ProjectList component mounted.')
    }
}
</script>
