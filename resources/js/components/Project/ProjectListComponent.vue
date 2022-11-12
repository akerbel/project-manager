<template>
    <div class="w-100">
        <button
            class="btn-new-project bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-2 mr-2 rounded"
            @click="this.$router.push('/project/new')"
        >
            New project
        </button>
        <message-component :message="message"></message-component>
        <table class="table-auto border-separate border border-slate-400 w-100 my-1">
            <thead>
                <tr>
                    <th class="border border-slate-300 py-2 px-4">ID</th>
                    <th class="border border-slate-300 py-2 px-4">Name</th>
                    <th class="border border-slate-300 py-2 px-4">Description</th>
                    <th class="border border-slate-300 py-2 px-4">Categories</th>
                    <th class="border border-slate-300 py-2 px-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="project in projects">
                    <td class="border border-slate-300 py-2 px-4">{{ project.id }}</td>
                    <td class="border border-slate-300 py-2 px-4">{{ project.name }}</td>
                    <td class="border border-slate-300 py-2 px-4">{{ project.description }}</td>
                    <td class="border border-slate-300 py-2 px-4">
                        <span v-for="category in project.categories">{{ category.name }}, </span>
                    </td>
                    <td class="border border-slate-300 py-2 px-4">
                        <button
                            class="btn-edit-project bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-2 mr-2 rounded"
                            @click="this.$router.push('/project/' + project.id)"
                        >
                            Edit
                        </button>
                        <button
                            class="btn-delete-project bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-2 rounded"
                            @click="deleteProject(project.id)"
                        >
                            Delete
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

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
