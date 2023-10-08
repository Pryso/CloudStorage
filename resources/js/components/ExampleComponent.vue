<template>
    <div class="container">
        <div class="form-modal">
        <span style="font-family: Stem-Medium; font-size: 20px">Вход в CloudStorage</span>
        <div class="form-auth" v-if="isLogin">
            <label for="email">Почта</label>
        <input type="text" v-model="email" id="email">
            <label for="password">Пароль</label>
        <input type="password" v-model="password" id="password">
            <div class="auth-btn" @click="login"><span v-if="!isLogging">Войти</span><div class="lds-ellipsis" v-if="isLogging"><div></div><div></div><div></div><div></div></div></div>

            <span style="font-family: Stem-Medium; align-self: center">Нет аккаунта? <span style="color: var(--main-color); font-family: Stem-Medium;" @click="openReg">Зарегестрируйтесь</span></span>
        </div>
            <div class="form-auth" v-if="isRegister">
                <label for="name">Логин</label>
                <input type="text" v-model="name" id="name">
                <label for="email">Почта</label>
                <input type="text" v-model="email" id="email">
                <label for="password">Пароль</label>
                <input type="password" v-model="password" id="password">
                <div class="auth-btn" @click="register"><span v-if="!isLogging">Регистрация</span><div class="lds-ellipsis" v-if="isLogging"><div></div><div></div><div></div><div></div></div></div>
                <span style="font-family: Stem-Medium; align-self: center">Уже есть аккаунт? <span style="color: var(--main-color); font-family: Stem-Medium;" @click="openLogin">Войти</span></span>
            </div>
        </div>
    </div>
</template>
<style scoped>
.lds-ellipsis {
    display: inline-block;
    position: relative;
    width: 80px;
    height: 10px;
}
.lds-ellipsis div {
    position: absolute;
    width: 13px;
    height: 13px;
    border-radius: 50%;
    background: var(--main-color);
    animation-timing-function: cubic-bezier(0, 1, 1, 0);
}
.lds-ellipsis div:nth-child(1) {
    left: 8px;
    animation: lds-ellipsis1 0.6s infinite;
}
.lds-ellipsis div:nth-child(2) {
    left: 8px;
    animation: lds-ellipsis2 0.6s infinite;
}
.lds-ellipsis div:nth-child(3) {
    left: 32px;
    animation: lds-ellipsis2 0.6s infinite;
}
.lds-ellipsis div:nth-child(4) {
    left: 56px;
    animation: lds-ellipsis3 0.6s infinite;
}
@keyframes lds-ellipsis1 {
    0% {
        transform: scale(0);
    }
    100% {
        transform: scale(1);
    }
}
@keyframes lds-ellipsis3 {
    0% {
        transform: scale(1);
    }
    100% {
        transform: scale(0);
    }
}
@keyframes lds-ellipsis2 {
    0% {
        transform: translate(0, 0);
    }
    100% {
        transform: translate(24px, 0);
    }
}

.form-auth > label {
        font-family: Stem-Medium;
        font-size: 14px;
        color: rgba(0,0,0,.6);
        margin-bottom: 5px;
    }
    .auth-btn {
        height: 45px;
        border-radius: 6px;
        border: 3px solid rgba(0,0,0,.12);
        font-family: Stem-Medium;
        font-size: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 15px;
        cursor: pointer;
        transition: border .3s ease;
    }
    .auth-btn:hover {
        border: 3px solid var(--main-color);
        transition: border .3s ease;
    }
    .form-auth > input {
        padding: 10px;
        border-radius: 6px;
        border: 1px solid rgba(0,0,0,.12);
        font-family: Stem-Medium;
        font-size: 18px;
        width: 300px;
        margin-bottom: 15px;
    }
    .form-auth > input:focus {
        outline: 3px solid var(--main-color);
    }
    .form-modal {
        display: flex;
        flex-direction: column;
    }
    .container {
        display: flex;
        width: 100%;
        height: 100%;
        align-items: center;
        justify-content: center;
        flex-direction: column;
    }
    .form-auth {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 20px;
        box-shadow: 0 0 8px 1px rgba(0,0,0,0.22);
        border-radius: 8px;

    }
    a {
        text-decoration: none;
        color: black;
    }
</style>
<script>
    export default {
    data() {
        return {
            isLogging: false,
            isLogin: true,
            isRegister: false,
            email: null,
            name: null,
            password: null,
        }
    },
    methods: {
        openLogin() {
            this.isRegister = false;
            this.isLogin = true;
        },
        openReg() {
            this.isRegister = true;
            this.isLogin = false;
        },
        register() {
            this.isLogging = true;
            axios.get('/sanctum/csrf-cookie').then(response => {
                axios.post('/api/register', {email: this.email, password: this.password, name: this.name}).then(r => {
                    this.$router.push('client');
                    this.isLogging = false;
                    store.commit('setAuth',true);
                }).catch(error => {
                    this.isLogging = false;
                    console.log(error);
                })
            });
        },
        login() {
            this.isLogging = true;
            axios.get('/sanctum/csrf-cookie').then(response => {
                axios.post('/api/login', {email: this.email, password: this.password}).then(r => {
                    this.$router.push('client');
                    this.isLogging = false;
                    store.commit('setAuth',true);
                }).catch(error => {
                    this.isLogging = false;

                })
            });
        },
    }
}
</script>
