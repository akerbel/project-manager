/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import './bootstrap';
import { createApp } from 'vue';
import axios from 'axios';
import VueAxios from 'vue-axios';
import { createRouter, createWebHistory } from 'vue-router';

/**
 * Next, we will create a fresh Vue application instance. You may then begin
 * registering components with the application instance so they are ready
 * to use in your application's views. An example is included for you.
 */


const app = createApp({});

app.use(VueAxios, axios);


import MainComponent from './components/MainComponent.vue';
app.component('main-component', MainComponent);
import LoginFormComponent from './components/LoginFormComponent.vue';
app.component('login-form-component', LoginFormComponent);
import RegisterFormComponent from './components/RegisterFormComponent.vue';
app.component('register-form-component', RegisterFormComponent);
import ProjectListComponent from './components/Project/ProjectListComponent.vue';
app.component('project-list-component', ProjectListComponent);
import ProjectEditComponent from './components/Project/ProjectEditComponent.vue';
app.component('project-edit-component', ProjectEditComponent);
import SituationListComponent from './components/Situation/SituationListComponent.vue';
app.component('situation-list-component', SituationListComponent);
import SituationEditComponent from './components/Situation/SituationEditComponent.vue';
app.component('situation-edit-component', SituationEditComponent);

import vSelect from 'vue-select'
app.component('v-select', vSelect)

const routes = [
    { path: '/', component: MainComponent },
    { path: '/projects', component: ProjectListComponent },
    { path: '/project/:id', component: ProjectEditComponent },
    { path: '/project/new', component: ProjectEditComponent },
    { path: '/project/:projectId/situation/:situationId', component: SituationEditComponent },
    { path: '/project/:projectId/situation/new', component: SituationEditComponent },
    { path: '/login', component: LoginFormComponent},
    { path: '/register', component: RegisterFormComponent },
]
const router = createRouter({
    history: createWebHistory(),
    routes,
});
app.use(router);

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// Object.entries(import.meta.glob('./**/*.vue', { eager: true })).forEach(([path, definition]) => {
//     app.component(path.split('/').pop().replace(/\.\w+$/, ''), definition.default);
// });

/**
 * Finally, we will attach the application instance to a HTML element with
 * an "id" attribute of "app". This element is included with the "auth"
 * scaffolding. Otherwise, you will need to add an element yourself.
 */

router.isReady().then(() => {
    app.mount('#app');
});
