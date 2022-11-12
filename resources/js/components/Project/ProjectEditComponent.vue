<template>
    <div v-if="message" class="info-message">{{ message }}</div>
    <div><button @click="this.$router.push('/projects')">Back</button></div>
    <form class="project-edit-form" @submit.prevent="saveProject">
        <h4 v-if="isNewProject">Create new project</h4>
        <h4 v-else>Edit Project #{{ project.id }} <b>{{ project.name }}</b></h4>
        <label for="project.name">Name: <input v-model="project.name" required></label>
        <label for="project.description">Description: <textarea v-model="project.description"></textarea></label>
        <label for="projectCategories">
            Categories:
            <v-select
                v-model="projectCategories"
                multiple
                :options="categoryOptions"
                label="name"
            ></v-select>
        </label>
        <button class="submit">Save</button>
    </form>

    <div v-if="!isNewProject">
        <situation-list :project-id="project.id"></situation-list>
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
