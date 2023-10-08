<template>
    <div class="rename-modal" v-show="isRename" @click="outsideModal($event)" >

        <div class="rename-form" ref="modal_rename">
            <span>Переименовать файл</span>
            <input type="text" v-model="this.name">
            <div class="btn-1" @click="renameFile">Переименовать</div>
        </div>
        <div class="modal-cover" ></div>
    </div>
    <div class="create-folder-modal" v-show="isFolder" @click="outsideModal($event)">

        <div class="rename-form" ref="modal_folder">
            <span>Создать папку</span>
            <input type="text" v-model="this.folderName">
            <div class="btn-1" @click="createFolder">Создать</div>
        </div>
        <div class="modal-cover" ></div>
    </div>
    <span style="font-family: Stem-Medium; font-size: 24px">{{this.$store.state.folder_name}}</span>
    <div class="files-table" ref="files_table" @contextmenu.prevent="openFolderMenu($event)">
        <template v-for="folder in folders" v-if="this.folders">
            <router-link :to="'/client/folder/' + folder.path" class="file-preview">
                <div class="file-pre">
                    <img :src="'/storage/img/folder.png'" style="max-width: 80px; max-height: 80px">
                </div>
                <div class="file-name">{{folder.name}}</div>
            </router-link>
        </template>
        <template v-for="file in files" v-if="this.files">
            <router-link class="file-preview" :to="/f/ + file.id" @contextmenu.prevent="openMenu($event,file.id,file.access,file.name)">
                <div class="file-pre">
                    <img :src="'/storage/img/link.png'" class="link-file" v-if="file.access === 'PUBLIC'">
                    <img :src="'storage/img/' + file.extension + '.svg'" v-if="file.extension != 'png'">
                    <img :src="file.url" style="max-height: 80px; max-width: 80px" v-else>

                </div>
                <div class="file-name">
                    <span>{{file.name.length >= 20 ? file.name.substring(0,20) + "..." + file.extension : file.name + '.' + file.extension}}</span>
                </div>
            </router-link>
        </template>
        <div v-show="isCreateFolder">
            <div class="new-folder-menu" :style="{ top: this.crFolTop, left: this.crFolLeft }"  ref="new_folder">
                <div class="create-folder" @click="this.isFolder = !this.isFolder; this.isCreateFolder = !this.isCreateFolder">
                    Создать папку
                </div>
            </div>
        </div>
        <div class="rmb-menu" :style="{ top: this.top, left: this.left}" v-if="isContext" ref="rmb_menu">
            <div class="rmb-element" @click="openAccess" ref="rmb_element">
                Открыть доступ
            </div>
            <div class="rmb-element" @click="closeAccess" v-if="this.file_access === 'PUBLIC'" ref="rmb_element">
                Закрыть доступ
            </div>
            <div class="rmb-element" @click="openRename" ref="rmb_element">
                Переименовать
            </div>
        </div>
    </div>
</template>

<script>
import store from "@/store.js";

export default {
    name: "FileTable",
    props: ['loadedfiles'],
    data() {
        return {
            folderName: null,
            isFolder: false,
            isCreateFolder: false,
            crFolLeft: null,
            crFolTop: null,
            folder_id: null,
            name: null,
            folders: null,
            files: null,
            isRename: false,
            isContext: false,
        }
    },
    watch: {
        loadedfiles: function (newVal, oldVal) {
            this.files = newVal;
        }
    },
    methods: {
        openFolderMenu(event) {
            if(event.target != this.$refs.files_table) {
                return;
            }
            this.isContext = false;
            this.isCreateFolder = true;
            this.crFolLeft = event.pageX;
            this.crFolTop = event.pageY;

        },
        createFolder() {
            axios.post('/api/file/createFolderInFolder',{name: this.folderName, folder_id: this.$store.state.folder_id}).then(r => {
                console.log(r);
            })
        },
        outsideModal(event) {

            if(!this.$refs.modal_rename.contains(event.target)) {
                this.isRename = false;
            }
            if(!this.$refs.modal_folder.contains(event.target)) {
                this.isFolder = false;
            }
        },
        openRename() {
            this.isRename = true;
            this.isContext = false;
        },
        openAccess() {
            axios.post('/api/file/openAccess',{id: this.file_id}).then(r => {
                axios.get('/api/file').then(r => {
                    this.files = r.data.files;
                });
            })
        },
        closeAccess() {
            axios.post('/api/file/closeAccess',{id: this.file_id}).then(r => {
                axios.get('/api/file').then(r => {
                    this.files = r.data.files;
                });
            })
        },
        renameFile() {
            if(this.files.find(file => file.name === this.name)) {
                console.log('error');
                return
            }
            axios.post('/api/file/rename',{id: this.file_id,name: this.name}).then(r => {
                axios.get('/api/file').then(r => {
                    this.files = r.data.data;
                });
            })
        },
        openMenu(event,id,access,file_name) {
            this.isCreateFolder = false;
            this.file_id = id;
            this.name = file_name
            this.file_access = access;
            this.top = event.pageY;
            this.left = event.pageX;
            this.isContext = true;
        },
        clickOutside(event) {

            if(!this.$refs?.new_folder?.contains(event.target)) {
                this.isCreateFolder = false;
            }
            if(!this.$refs?.modal_rename?.contains(event.target)) {
                this.isRename = false;
            }
            if(!this.$refs?.rmb_menu?.contains(event.target)) {
                this.isContext = false;
            }


        }
    },
    mounted() {

    },
    created() {

        window.addEventListener('click',this.clickOutside);
        this.files = this.$store.state.folder_files;
        this.folders = this.$store.state.folders;
        var path = '';
        if(Array.isArray(this.$route.params.name)) {
            path = this.$route.params.name.join('/');
        } else {
            path = this.$route.params.name;
        }
        axios.get('/api/file/folder/' + path).then(r => {
            store.commit('set_folder', r.data.data[0]);
            this.files = r.data.data[0].files;
            this.folders = r.data.data[0].folders;
        })

    }
}
</script>

<style scoped>

</style>
