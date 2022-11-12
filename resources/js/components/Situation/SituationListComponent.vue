<template>
    <button
        class="btn-new-project bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-2 mr-2 rounded"
        @click="this.$router.push(projectId + '/situation/new')"
    >
        New case
    </button>
    <message-component :message="message"></message-component>
    <table class="table-auto border-separate border border-slate-400 w-100 my-1">
        <thead>
            <tr>
                <th class="border border-slate-300 py-2 px-4">ID</th>
                <th class="border border-slate-300 py-2 px-4">Case</th>
                <th class="border border-slate-300 py-2 px-4">Description</th>
                <th class="border border-slate-300 py-2 px-4">Status</th>
                <th class="border border-slate-300 py-2 px-4">Actions</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="(situation, index) in situations">
                <td class="border border-slate-300 py-2 px-4">{{ index + 1 }}</td>
                <td class="border border-slate-300 py-2 px-4">{{ situation.name }}</td>
                <td class="border border-slate-300 py-2 px-4">{{ situation.description }}</td>
                <td class="border border-slate-300 py-2 px-4">{{ statusOptions[situation.status] }}</td>
                <td class="border border-slate-300 py-2 px-4">
                    <button
                        class="btn-edit-project bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-2 mr-2 rounded"
                        @click="this.$router.push(projectId + '/situation/' + situation.id)"
                    >
                        Edit
                    </button>
                    <button
                        class="btn-delete-project bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-2 rounded"
                        @click="deleteSituation(situation.id, index)"
                    >
                        Delete
                    </button>
                </td>
            </tr>
        </tbody>
    </table>
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
            statusOptions: [
                'Planning', 'Ongoing', 'Completed'
            ],
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

        deleteSituation(id, index) {
            if (confirm('Do you really want to delete case #' + index.toString() + '?')) {
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
