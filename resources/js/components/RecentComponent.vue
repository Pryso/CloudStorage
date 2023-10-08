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
        <div class="tables" ref="tables">
    <template v-for="item in recent" >

        <span style="font-family: Stem-Medium">{{ item.date}}</span>
        <div class="files-table" ref="files_table">
            <template v-for="folder in item.data.folders">
                <router-link :to="'/client/folder/' + folder.path" class="file-preview">
                    <div class="file-pre">
                        <img :src="'/storage/img/folder.png'" style="max-width: 80px; max-height: 80px">
                    </div>
                    <div class="file-name">{{folder.name}}</div>
                </router-link>
            </template>
            <template v-for="file in item.data.files" >
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
        </div>
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
    name: "RecentComponent.vue",
    data() {
        return {
            recent: null,
            folderName: null,
            name: null,
            crFolLeft: null,
            crFolTop: null,
            isCreateFolder: false,
            isFolder: false,
            isRename: false,
            isContext: false,
        }
    },
    methods: {
        sortedRecent(recent) {
            const data = recent;

            return Object.keys(data)
                .map((date) => {
                    return {
                        date: date,
                        data: data[date],
                    }
                })
                .sort(function (a, b) {
                    let dateA = new Date(a.date);
                    let dateB = new Date(b.date);

                    return dateA - dateB;
                })

        },
        openAccess() {
            axios.post('/api/file/openAccess',{id: this.file_id}).then(r => {
                axios.get('/api/file').then(r => {
                    this.files = r.data.files;
                });
            })
        },
        openRename() {

            this.isRename = true;
            this.isContext = false;

        },
        closeAccess() {
            axios.post('/api/file/closeAccess',{id: this.file_id}).then(r => {
                axios.get('/api/file').then(r => {
                    this.files = r.data.files;
                });
            })
        },
        createFolder() {
            axios.post('/api/file/createFolder',{name: this.folderName, path: this.folderName}).then(r => {

            })
        },
        renameFile() {
            if(this.files.find(file => file.name === this.name)) {
                console.log('error');
                return;
            }
            axios.post('/api/file/rename',{id: this.file_id,name: this.name}).then(r => {
                axios.get('/api/file').then(r => {
                    this.files = r.data.data;
                });
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
        openFolderMenu(event) {
            var target_class = event.target.parentElement.className;
            if(!this.$refs.file_wrapper.contains(event.target)) {
                return;
            }
            if(target_class === 'file-pre' || target_class === 'files-table' || target_class === 'file-name') {
                return;
            }

            this.isContext = false;
            this.isCreateFolder = true;
            this.crFolLeft = event.pageX;
            this.crFolTop = event.pageY;
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
            if(!this.$refs?.rmb_menu?.contains(event.target)) {
                this.isContext = false;
            }
        },
    },
    created() {
        window.addEventListener('click',this.clickOutside);
        axios.get('/api/file/recent').then(r => {
            var response = r.data;
            this.files = r.data.files;
            Object.keys(response).forEach(a => {
                response[a] = response[a].reduce((a,b) => {
                    a[b.created_at] = a[b.created_at]  ? a[b.created_at].concat(b) : [b];
                    return a;
                }, {});
            })
            var recent = new Object();
            var keys = [...new Set([...Object.keys(response.files),...Object.keys(response.folder)])];
            keys.forEach(key => {

                recent[key] = {files: response.files[key] ? response.files[key] : null, folders: response.folder[key] ? response.folder[key] : null};
            })
            this.recent = this.sortedRecent(recent);
            console.log(this.recent)

        })
    }
}
</script>

<style scoped>

</style>
