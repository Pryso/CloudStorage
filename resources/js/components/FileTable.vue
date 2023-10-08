<template>
    <div class="rename-modal" v-show="isRename" @click="outsideModal($event)">

        <div class="rename-form" ref="modal_rename">
            <span>Переименовать файл</span>
            <input type="text" v-model="this.name">
            <div class="btn-1" @click="renameFile">Переименовать</div>
        </div>
        <div class="modal-cover"></div>
    </div>
    <div class="create-folder-modal" v-show="isFolder" @click="outsideModal($event)">

        <div class="rename-form" ref="modal_folder">
            <span>Создать папку</span>
            <input type="text" v-model="this.folderName">
            <div class="btn-1" @click="createFolder">Создать</div>
        </div>
        <div class="modal-cover" ></div>
    </div>
    <div class="file-wrapper" @contextmenu.prevent="openFolderMenu($event)" ref="file_wrapper">
    <div class="files-table"  ref="files_table">
        <template v-for="folder in folders" v-if="this.folders">
            <router-link :to="{name: 'folder', params: {name: folder.name}}" class="file-preview">
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
                    <img :src="'/storage/img/' + file.extension + '.svg'" v-if="file.extension != 'png'">
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
    </div>
</template>

<script>
export default {
    name: "FileTable",
    props: ['loadedfiles'],
    data() {
        return {
            folderName: null,
            name: null,
            crFolLeft: null,
            top: null,
            left: null,
            crFolTop: null,
            isCreateFolder: false,
            folders: null,
            files: null,
            isFolder: false,
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
        createFolder() {
            axios.post('/api/file/createFolder',{name: this.folderName, path: this.folderName}).then(r => {
                this.isCreateFolder = false;
            })
        },
        openRename() {

            this.isRename = true;
            this.isContext = false;
            console.log(this.isRename);
        },
        openFolderMenu(event) {
            if(!this.$refs.file_wrapper.contains(event.target)) {
                return;
            }
            if(this.$refs.files_table.contains(event.target)) {
                return;
            }
            this.isContext = false;
            this.isCreateFolder = true;
            this.crFolLeft = event.pageX;
            this.crFolTop = event.pageY;

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
        outsideModal(event) {

            if(!this.$refs.modal_rename.contains(event.target)) {
                this.isRename = false;
            }
            if(!this.$refs.modal_folder.contains(event.target)) {
                this.isFolder = false;
            }
        },
        clickOutside(event) {
            if(!this.$refs?.new_folder?.contains(event.target)) {
                this.isCreateFolder = false;
            }
            if(!this.$refs?.rmb_menu?.contains(event.target)) {
                this.isContext = false;
            }
        }
    },
    created() {
        window.addEventListener('click',this.clickOutside);
        axios.get('/api/file').then(r => {
            this.folders = r.data.folder;
            this.files = r.data.files;
            var size = null;
            this.files.forEach(file => {
                size += file.size;
            })
            var fileSizes = (size / (100 * 1000 * 1000)).toFixed(2);
            this.leftSpace = (10 - (size / (1000 * 1000 * 1000)).toFixed(2)).toFixed(1);

            this.progressBar = fileSizes;
            var test = Intl.DateTimeFormat('ru', {month: 'long'});
            var month = test.format(new Date());
            console.log(month);
        })
    }
}
</script>

<style scoped>

</style>
