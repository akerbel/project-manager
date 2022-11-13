<template>
    <div v-if="message" :class="getClasses(color)" role="alert">
    <span class="block sm:inline">{{ message }}</span>
    </div>
</template>

<script>
export default {
    props: {
        message: {
            type: String,
            default: ''
        },
        color: {
            type: String,
            default: 'red'
        }
    },
    methods: {
        getClasses(color) {
            return 'bg-'+color+'-100 border border-'+color+'-400 text-'+color+'-700 px-4 py-3 rounded relative m-1';
        }
    }
}
</script>
