import { createRouter, createWebHistory} from "vue-router";
import store from './store.js';

// сделать проверку на авторизацию при переходе по роуту
const routes = [
    {
        path: '/',
        name: 'index',
        component: () => import("./components/ExampleComponent.vue"),
    },
    {
        path: '/client',
        name: 'client',
        meta: { requiresAuth: true },
        component: () => import("./components/FilesComponent.vue"),
        children: [
            {
                path: 'recent',
                name: 'recent',
                component: () => import("./components/RecentComponent.vue"),
            },
            {
                path: '',
                name: 'files',
                props: { loadedfiles: true},
                component: () => import("./components/FileTable.vue"),
            },
            {
                path: 'folder/:name+',
                name: 'folder',

                component: () => import("./components/FolderComponent.vue"),
                beforeUpdate: (to,from,next) => {
                    console.log(to);
                },
                beforeResolve: (to,from,next) => {
                    if(store.state.isAuth) {
                        var path = '';
                        if (Array.isArray(to.params.name)) {
                            path = to.params.name.join('/');
                        } else {
                            path = to.params.name;
                        }
                        axios.get('/api/file/folder/' + path).then(r => {
                            store.commit('set_folder', r.data.data[0]);

                        })
                    } else {
                        next({name: 'index'});
                    }
                }
            }
        ]

    },
    {
        path: '/f/:id',
        name: 'file',
        component: () => import("./components/FileComponent.vue"),
    },
    { path: '/:pathMatch(.*)*' , redirect: '/', },
]
const router = createRouter({
    history: createWebHistory(),
    routes,
});
router.beforeResolve( (to,from,next) => {

    if(to.meta.requiresAuth && !store.state.isAuth) {
        console.log('test');
        axios.get('/api/user').then(r => {
            store.commit('setAuth',true);
            next()
        }).catch(error => {
            next({name: 'index'});
        })
    } else {
        next()
    }
});
export default router;
