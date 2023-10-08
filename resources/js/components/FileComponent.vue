
<template>
<div class="wrap" v-if="isLoadead">
    <div class="box-file">
        <div class="file">
        <img :src="'/storage/img/' + this.file.extension + '.svg'" height="120">
        <div class="file-name">
            {{this.file.name + '.' + this.file.extension}}
        </div>
        <a class="download-btn" :href="this.file.url" :download="this.file.name + '.' + this.file.extension">
            Скачать
        </a>
        </div>
    </div>
</div>
</template>

<script>
export default {


    name: "FileComponent",
    data() {
        return {
            isLoadead: false,
            file: null,
        }
    },
    beforeCreate() {
        axios.get('/api/file/' + this.$route.params.id).then(r => {
            this.file = r.data.data;
            this.isLoadead = true;
        });
    },

}
</script>

<style scoped>
.box-file {
    width: 600px;
    display: flex;
    flex-direction: column;
    align-items: center;
    box-shadow: 0 0 8px 2px rgb(0 0 0 / 20%);
    border-radius: 6px;
    justify-content: center;
}
.download-btn {
    margin-top: 12px;
    height: 40px;
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: Stem-Medium;
    border-radius: 6px;
    box-shadow: 1px 2px 12px 3px rgb(0 0 0 / 20%);

}
.file-name {
    margin-top: 10px;
    align-items: center;
    justify-content: center;
    display: flex;
    font-family: Stem-Medium;
}
.wrap {
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}
.file {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 20px;
    box-shadow: 0px 0px 0px rgba(0,0,0,0.3);
}
</style>
