<template>
    <div class="w-100">
        <button
            class="btn-back bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-2 mr-2 rounded"
            @click="this.$router.push('/projects')"
        >
            Back
        </button>
        <message-component :message="message"></message-component>
        <form class="project-edit-form w-1/2 my-1 flex justify-center flex-column m-auto" @submit.prevent="saveProject">
            <h4 v-if="isNewProject" class="text-xl text-center font-bold">Create new project</h4>
            <h4 v-else class="text-xl text-center font-bold">Edit Project #{{ project.id }} <b>{{ project.name }}</b></h4>
            <label for="project.name" class="font-bold m-1">
                Name
                <input v-model="project.name" required class="m-2 p-1 rounded">
            </label>
            <label for="project.description" class="font-bold m-1 flex-column">
                Description
                <textarea v-model="project.description" class="m-2 p-1 rounded"></textarea>
            </label>
            <label for="projectCategories" class="font-bold m-1">
                Categories
                <v-select
                    class="m-2 p-1 rounded"
                    v-model="projectCategories"
                    multiple
                    :options="categoryOptions"
                    label="name"
                ></v-select>
            </label>
            <button class="btn-submit bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 mr-2 rounded w-1/2">Save</button>
        </form>

        <situation-list-component v-if="!isNewProject" :project-id="project.id"></situation-list-component>
    </div>
</template>

<script>
import { api } from '../../api.js';
import 'vue-select/dist/vue-select.css';
import SituationListComponent from "../Situation/SituationListComponent.vue";

export default {
    data() {
        return {
            api,
            project: [],
            projectCategories: [],
            message: '',
            categoryOptions: [],
            isNewProject: false,
        }
    },
    components: {
        'situation-list': SituationListComponent
    },
    methods: {

        saveProject() {
            // Prepare categories.
            let projectCategoryIds = [];
            this.projectCategories.forEach((item) => {
                projectCategoryIds.push(item.id);
            });
            if (projectCategoryIds.length < 1) {
                this.message = 'You must chose at least one category.';
                return;
            }

            // Prepare request.
            let method, url;
            if (this.isNewProject) {
                method = 'POST';
                url = 'project';
            }
            else {
                method = 'PATCH';
                url = 'project/' + this.$route.params.id;
            }

            // Save changes.
            api.call(
                method,
                url,
                {
                    name: this.project.name,
                    description: this.project.description,
                    categories: projectCategoryIds
                }
            )
                .then((response) => {
                    this.message = 'The project is saved';
                })
                .catch((error) => {
                    this.message = error.response.data.error;
                });
        },

        getCategoryOptions() {
            api.call(
                'GET',
                'categories'
            )
                .then((response) => {
                    response.data.forEach((item) => {
                        this.categoryOptions.push({
                            id: item.id,
                            name: item.name
                        });
                    });
                })
                .catch((error) => {
                    this.message = error.response.data.error;
                });
        },

        getProject() {
            if (this.$route.params.id === undefined) {
                this.isNewProject = true;
            }
            else {
                api.call(
                    'GET',
                    'project/' + this.$route.params.id
                )
                    .then((response) => {
                        this.project = response.data;
                        this.project.categories.forEach((item) => {
                            this.projectCategories.push({
                                id: item.id,
                                name: item.name
                            });
                        });
                    })
                    .catch((error) => {
                        this.message = error.response.data.error;
                    });
                this.isNewProject = false;
            }
        }
    },
    mounted() {
        this.getCategoryOptions();
        this.getProject();
    }
}
</script>
