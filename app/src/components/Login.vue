<template>

  <div class="login">
  <transition name="fade" mode="out-in">

    <el-form size="medium" label-position="top" :model="form" v-if="mode === 'register'" label-width="180px" @submit.prevent="register">
      <el-form-item label="Email" required>
        <el-input v-model="form.email"></el-input>
      </el-form-item>
      <el-form-item label="Password" required>
        <el-input type="password" v-model="form.password"></el-input>
      </el-form-item>
      <el-form-item label="Confirm Password" required>
        <el-input type="password" v-model="form.password_confirm"></el-input>
      </el-form-item>
      <el-form-item>
        <div style="display: flex; justify-content: space-between;">
            <el-button type="primary" @click="register" v-loading="loading">Register</el-button>

            <el-link type="info" @click="mode = 'login'">or login</el-link>
        </div>
      </el-form-item>
    </el-form>

    <el-form :model="form" v-else @submit.prevent="login">
      <el-form-item>
        <el-input placeholder="email" v-model="form.email"></el-input>
      </el-form-item>
      <el-form-item>
        <el-input type="password" placeholder="passord" v-model="form.password"></el-input>
      </el-form-item>
      <el-form-item>
        <div style="display: flex; justify-content: space-between;">
        <el-button type="primary" @click="login" v-loading="loading">Login</el-button>

        <el-link type="info" @click="mode = 'register'">or register</el-link>
        </div>
      </el-form-item>
    </el-form>
  </transition>
  </div>
</template>

<script>
import client from '@/services/api';

export default {
  name: 'Login',
  data: () => {
    return {
      mode: 'login',
      loading: false,
      form: {
        email: '',
        password: '',
        password_confirm: ''
      }
    }
  },
  emits: ['login'],
  methods: {
    async login() {
        this.loading = true;
        try {
          let response = await client.post('/auth/login', this.form);
          this.$emit('login', response.data.token);
        } catch(e) {
          console.error(e);
          this.loading = false;
        }
    },

    async register() {
      this.loading = true;
      try {
        await client.post('/users/', this.form);
      } catch (e) {
        console.error(e);
        this.loading = false;
        return;
      }

      await this.login();
    }
  }
}
</script>

<style scoped lang="scss">
  .login {
    box-shadow: 5px 5px 12px 0px rgb(0 0 255 / 20%);
    border-radius: 10px;
    margin-top: 10rem;
    padding: 3rem 2rem 0.5rem 2rem;
    margin-left: auto;
    margin-right: auto;
    max-width: 320px;
    background-color: #e9eef3;
  }

  .no-mode-fade-enter-active, .no-mode-fade-leave-active {
    transition: opacity .5s
  }

  .no-mode-fade-enter-from, .no-mode-fade-leave-to {
    opacity: 0
  }
</style>