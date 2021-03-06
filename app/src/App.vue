<template>
  <div class="common-layout">
    <div v-if="loading">
      loading...
    </div>
    <el-container direction="horizontal" v-else>

        <el-main>
          <Login v-if="!user" @login="onLogin"></Login>
          <div v-else class="content">
              <Avatar :user="user" @avatar="onAvatar" />
              <div>{{ user.email }}</div>
              <el-link @click="logout">logout</el-link>
          </div>
        </el-main>

    </el-container>
  </div>
</template>

<script>
import { EventSourcePolyfill } from 'event-source-polyfill';

import emitter from 'tiny-emitter/instance'

import client from '@/services/api'
import storage from '@/services/storage'
import Login from './components/Login.vue'
import Avatar from "./components/Avatar";

export default {
  name: 'App',
  components: {
    Avatar,
    Login
  },
  data: () => {
    return {
      loading: true,
      user: null,
      interceptor: null,
      subscription: null
    }
  },
  async created() {
    const savedAuth = storage.load('auth');
    if (savedAuth && savedAuth.token) {
      await this.onLogin(savedAuth.token);
    }
    this.loading = false
  },
  methods: {
    updateClient(token) {
      if (this.interceptor !== null) {
        client.interceptors.request.eject(this.interceptor)
        this.interceptor = null
      }

      if (token) {
        this.interceptor = client.interceptors.request.use((config) => {
          config.headers.common['Authorization'] = 'Bearer ' + token;
          console.log(config.headers.common['Authorization']);
          return config
        })

        console.log(this.interceptor, client.interceptors)
      }
    },
    onAvatar(avatar) {
      this.user.avatars = [avatar]
    },
    async onLogin(token) {
      this.updateClient(token);

      let response;
      let sse_token;
      try {
        response = await client.get('/users/me');
        this.user = {...response.data, ...{token: token}};

        if ('x-subscription-token' in response.headers) {
          sse_token = response.headers['x-subscription-token'];
          console.log(sse_token, response.headers)
        }
      } catch (e) {
        console.error(e);
        this.logout()
        return;
      }

      this.user = {...response.data, ...{token: token}};

      if (this.subscription) {
        this.subscription.close();
        this.subscription = null;
      }

      this.subscription = new EventSourcePolyfill(
        process.env.VUE_APP_MERCURE_ENDPOINT + '?topic=' + encodeURIComponent('http://example.com/user/' + this.user.id),
        {
          headers: {
            'Authorization': 'Bearer ' + sse_token
          }
        }
      );

      this.subscription.onmessage = event => {
        console.log('new sse event', event)
        try {
          let data = JSON.parse(event.data);
          if ('event' in data) {
            emitter.emit(data.event, data.payload)
          }
        } catch (e) {
          console.error(e);
          return
        }
      }

      storage.save('auth', {
        user: this.user.id,
        token: token
      });
    },
    logout() {
      this.updateClient(null);

      this.user = null;

      if (this.subscription) {
        this.subscription.close();
        this.subscription = null;
      }

      storage.save('auth', {
        user: null,
        token: null
      });
    }
  }
}
</script>

<style lang="scss">
.el-header,
.el-footer {
  color: var(--el-text-color-primary);
  text-align: center;
  line-height: 60px;
}

.el-main {
  color: var(--el-text-color-primary);
}

.el-aside {
  color: var(--el-text-color-primary);
  text-align: center;
}

body > .el-container {
  margin-bottom: 40px;
}

.content {
  margin-top: 10rem;
  text-align: center;
}
</style>
