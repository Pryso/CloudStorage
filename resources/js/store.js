import { createStore } from 'vuex'

// Create a new store instance.
const store = createStore({
    state () {
        return {
            isAuth: false,
            folder_id: null,
            folder_files: [],
            folder_name: null,
            folders: [],
        }
    },
    mutations: {
        setAuth(state,status) {
          state.isAuth = status;
        },
        set_folder(state, response) {
            state.folder_files = response.files;
            state.folder_id = response.id;
            state.folder_name = response.name;
            state.folders = response.folders;

        },

    }
});
export default store;
