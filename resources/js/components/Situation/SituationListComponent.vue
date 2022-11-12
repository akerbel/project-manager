<template>
    <div v-if="message" class="error-message">{{ message }}</div>
    <div><button @click="this.$router.push(projectId + '/situation/new')">New</button></div>
    <ul>
        <li>
            <div>ID</div>
            <div>Name</div>
            <div>Description</div>
            <div>Status</div>
            <div>Actions</div>
        </li>
        <li v-for="(situation, index) in situations">
            <div>{{ index + 1 }}</div>
            <div>{{ situation.name }}</div>
            <div>{{ situation.description }}</div>
            <div>{{ situation.status }}</div>
            <div>
                <button @click="this.$router.push(projectId + '/situation/' + situation.id)">Edit</button>
                <button @click="deleteSituation(situation.id)">Delete</button>
            </div>
        </li>
    </ul>
</template>

<script>
import { api } from '../../api.js';

export default {
    props: {
        projectId: Number
    },
    data() {
        return {
            api,
            situations: [],
            message: '',
        }
    },
    methods: {
        getSituations() {
            api.call(
                'GET',
                'situations/' + this.$props.projectId,
            )
                .then((response) => {
                    this.situations = response.data;
                })
                .catch((error) => {
                    this.message = error.response.data.error;
                });
        },

        deleteSituation(id) {
            if (confirm('Do you really want to delete case #' + id.toString() + '?')) {
                api.call(
                    'DELETE',
                    'situation/' + id.toString()
                )
                    .then((response) => {
                        this.message = 'The case #' + id.toString() + ' has been deleted';
                        let index = this.situations.findIndex((item) => {
                            return item.id === id;
                        });
                        this.situations.splice(index, 1);
                    })
                    .catch((error) => {
                        this.message = error.response.data.error;
                    });
            }
        }
    },
    watch: {
        // We have to watch projectId, because it is undefined at rendering.
        projectId(newValue, oldValue) {
            if (oldValue === undefined && newValue !== undefined) {
                this.getSituations();
            }
        },
    },
}
</script>
