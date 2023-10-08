
<template>
    <div @dragover.prevent="onDragOver" style="height: 100%">
        <header>
            <div class="header-wrapper">
                <div class="logo">CloudStorage</div>
                <div class="elements">

                    <div class="search-files">
                        <input type="text">
                    </div>
                    <div class="user-menu">

                    </div>
                </div>
            </div>

        </header>
        <div class="content-wrapper">
            <div class="sidebar">
                <div class="sidebar-nav-elements">
                    <div class="sb-elem">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="21" viewBox="0 0 24 21" fill="none">
                            <path d="M23 13V19.5H1V13" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M12 13L12 7L12 1.5M12 1.5L16 5M12 1.5L7.5 5" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>Загрузить</div>
                    <router-link :to="{ name: 'recent'}" class="sb-elem">Последние</router-link>

                    <div class="sb-elem">Открытый доступ</div>
                </div>
                <div class="storage-fill">
                    <div class="fill-progress">
                        <div class="fill-bar" :style="{width: this.progressBar + '%'}" ></div>
                    </div>
                    Свободно {{this.leftSpace}} ГБ из 10 ГБ
                </div>
            </div>
            <div class="content">
                <div class="uploadder" v-if="isUploading">
                    <div class="uploadder-header">
                        <span>Файлы</span>
                        <span @click="closeUploader" style="font-size: 16px; cursor: pointer">Закрыть</span>
                    </div>
                    <div class="uploadder-files">
                        <template v-for="(item,index) in uploadder" :key="this.componentKey">
                            <div class="uploadFile"  >
                            <span>{{ item.name.length >= 20 ? item.name.substring(0,20) + '...' : item.name}}</span>
                            <div class="fill-progress" v-if="!item.error">
                                <div class="fill-bar" :id="'file' + index" :ref="el => item.ref = el" ></div>

                            </div>
                                <div v-else>{{item.error}}</div>
                            </div>

                        </template>
                    </div>
                </div>
    <div class="drop-box" v-if="isDropping"  @dragleave.prevent="onDragLeave"  @drop.prevent="onDrop">
        <span>Перетащите файлы</span>
    </div>


    <div class="drop-box-cover" v-if="isDropping"></div>

                <router-view :loadedfiles="this.loadedfiles" :key="this.$route.path"></router-view>
    </div>
        </div>
    </div>
</template>

<script>
export default {

    name: "FilesComponent",
    data() {
        return {
            componentKey: 0,
            files: null,
            test: null,
            progressBar: null,
            leftSpace: '...',
            isUploading: false,
            name: null,
            isBg: false,
            isDropping: false,
            uploadFiles: [],
            folder_files: null,
            top: null,
            loadedfiles: null,
            left: null,
            uploadder: [],
        }
    },
    created() {
        axios.get('/api/file').then(r => {

            this.files = r.data.files;
            var size = null;
            this.files.forEach(file => {
                size += file.size;
            })
            var fileSizes = (size / (100 * 1000 * 1000)).toFixed(2);
            this.leftSpace = (10 - (size / (1000 * 1000 * 1000)).toFixed(2)).toFixed(1);

            this.progressBar = fileSizes;
        })
    },

    mounted() {

    },
    methods: {

        forceRerender() {
            this.componentKey += 1;

        },
        closeUploader() {
          this.isUploading = false;
          this.uploadder = [];
        },




        onDragLeave(event) {
            event.preventDefault();
            this.isDropping = false;
            this.isBg = false;
        },

        onDragOver(event) {
            event.preventDefault();
            this.isDropping = true;
            this.isBg = true;
        },
        onDrop: function (event) {
            this.uploadFiles = [];
            event.preventDefault();
            const files = event.dataTransfer.files;


            for (let i = 0; i < files.length; i++) {
                this.uploadFiles.push(files[i]);
                this.uploadder.push(this.uploadFiles[i]);
            }
            this.isDropping = false;
            this.uploadFiles.forEach((value, index) => {

                this.isUploading = true;
                if(this.$route.name === "folder") {
                    if(this.$store.state.folder_files.some(e => e.full_name === value.name)) {
                        value.error = 'Данный файл уже есть';
                        return;
                    }
                } else {

                    if (this.files.some(e => e.fullname === value.name)) {
                        value.error = 'Данный файл уже есть';
                        return;
                    }
                }

                const formData = new FormData();
                formData.append('file[]', value);

                if(this.$route.name === 'folder') {
                    var path = '';
                    if(Array.isArray(this.$route.params.name)) {
                        path = this.$route.params.name.join('/');
                    } else {
                        path = this.$route.params.name;
                    }
                    formData.append('id', this.$store.state.folder_id);
                    axios.post('/api/file/folder', formData, {

                        onUploadProgress: function (progressEvent) {
                            let percentage = ((progressEvent.loaded / progressEvent.total) * 100).toFixed(2);
                            value.ref.style.width = percentage + '%';
                        }
                    }).then(r => {
                        axios.get('/api/file/folder/' + path).then(r => {
                            this.loadedfiles = r.data.data[0].files;
                            event.dataTransfer.clearData()
                        })
                    }).catch(err => {
                        value.error = 'В файле обнаружен вирус';
                        this.forceRerender();
                    });
                } else {
                axios.post('/api/file', formData, {

                    onUploadProgress: function (progressEvent) {
                        let percentage = ((progressEvent.loaded / progressEvent.total) * 100).toFixed(2);
                        value.ref.style.width = percentage + '%';
                    }
                }).then(r => {
                    axios.get('/api/file').then(r => {
                        this.loadedfiles = r.data.files;
                        this.files = r.data.files;
                        event.dataTransfer.clearData()
                    });
                }).catch(err => {
                    value.error = 'В файле обнаружен вирус';
                    this.forceRerender();
                });
                }
            });


        },
    },


}
</script>

<style scoped>


</style>
