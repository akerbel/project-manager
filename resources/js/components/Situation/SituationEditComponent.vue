<template>
    <div class="w-100">
        <button
            class="btn-back bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-2 mr-2 rounded"
            @click="this.$router.push('/project/' + $route.params.projectId)"
        >
            Back
        </button>
        <message-component :message="message"></message-component>
        <form class="situation-edit-form w-1/2 my-1 flex justify-center flex-column m-auto" @submit.prevent="saveSituation">
            <h4 v-if="isNewSituation" class="text-xl text-center font-bold">Create new case</h4>
            <h4 v-else class="text-xl text-center font-bold">Edit Case #{{ situation.id }} <b>{{ situation.name }}</b></h4>
            <label for="situation.name" class="font-bold m-1">
                Name
                <input v-model="situation.name" required class="m-2 p-1 rounded">
            </label>
            <label for="situation.description" class="font-bold m-1">
                Description
                <textarea v-model="situation.description" class="m-2 p-1 rounded"></textarea>
            </label>
            <label for="situation.status" class="font-bold m-1">
                Status
                <v-select
                    v-model="status"
                    :options="statusOptions"
                ></v-select>
            </label>
            <button class="btn-submit bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 mr-2 rounded w-1/2">Save</button>
        </form>
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
