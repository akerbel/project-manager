<template>
    <div v-if="message" class="info-message">{{ message }}</div>
    <div><button @click="this.$router.push('/project/' + $route.params.projectId)">Back</button></div>
    <form class="situation-edit-form" @submit.prevent="saveSituation">
        <h4 v-if="isNewSituation">Create new case</h4>
        <h4 v-else>Edit Case #{{ situation.id }} <b>{{ situation.name }}</b></h4>
        <label for="situation.name">Name: <input v-model="situation.name" required></label>
        <label for="situation.description">Description: <textarea v-model="situation.description"></textarea></label>
        <label for="situation.status">
            Status:
            <v-select
                v-model="status"
                :options="statusOptions"
            ></v-select>
        </label>
        <button class="submit">Save</button>
    </form>
</template>

<script>
import { api } from '../../api.js';
import 'vue-select/dist/vue-select.css';
import SituationListComponent from "../Situation/SituationListComponent.vue";

export default {
    data() {
        return {
            api,
            situation: [],
            message: '',
            isNewSituation: false,
            statusOptions: [
                'Planning', 'Ongoing', 'Completed'
            ],
            status: null
        }
    },
    methods: {

        saveSituation() {

            // Prepare request.
            let method, url;
            if (this.isNewSituation) {
                method = 'POST';
                url = 'situation';
            }
            else {
                method = 'PATCH';
                url = 'situation/' + this.$route.params.situationId;
            }

            // Save changes.
            api.call(
                method,
                url,
                {
                    name: this.situation.name,
                    description: this.situation.description,
                    project_id: this.$route.params.projectId,
                    status: this.statusOptions.findIndex((item) => {
                        return item === this.status;
                    })
                }
            )
                .then((response) => {
                    this.message = 'The case has been saved';
                })
                .catch((error) => {
                    this.message = error.response.data.error;
                });
        },

        getSituation() {
            if (this.$route.params.situationId === undefined) {
                this.isNewSituation = true;
            }
            else {
                api.call(
                    'GET',
                    'situation/' + this.$route.params.situationId
                )
                    .then((response) => {
                        this.situation = response.data;
                        this.status = this.statusOptions[this.situation.status];
                    })
                    .catch((error) => {
                        this.message = error.response.data.error;
                    });
                this.isNewSituation = false;
            }
        }
    },
    mounted() {
        this.getSituation();
    }
}
</script>
