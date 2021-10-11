<template>
  <div class="account-picture">
    <el-upload
        class="avatar-uploader"
        :action="uploadUrl"
        name="picture"
        :headers="headers"
        :multiple="false"
        :show-file-list="false"
        :on-success="handleAvatarSuccess"
        :before-upload="beforeAvatarUpload"
    >
      <img v-if="imageUrl" :src="imageUrl" class="avatar" />
      <i v-else class="el-icon-plus avatar-uploader-icon"></i>
    </el-upload>
  </div>
</template>

<script>

export default {
  name: 'Avatar',
  props: ['user'],
  data() {
    return {
      imageUrl: this.user.avatars.length > 0 ? this.user.avatars[0].url : '',
    }
  },
  computed: {
    uploadUrl() {
      return `${process.env.VUE_APP_API_BASE_URL}/users/${this.user.id}/pictures`;
    },
    headers() {
      return {
        Authorization: 'Bearer ' + this.user.token
      }
    }
  },
  methods: {
    handleAvatarSuccess(res, file) {
      this.imageUrl = URL.createObjectURL(file.raw)
    },
    beforeAvatarUpload(file) {
      const isLt2M = file.size / 1024 / 1024 < 2

      if (!isLt2M) {
        this.$message.error('Avatar picture size can not exceed 2MB!')
      }

      return isLt2M
    }
  }
}
</script>

<style lang="scss">
.avatar-uploader .el-upload {
  border: 1px dashed #d9d9d9;
  border-radius: 6px;
  cursor: pointer;
  position: relative;
  overflow: hidden;
}
.avatar-uploader .el-upload:hover {
  border-color: #409eff;
}
.avatar-uploader-icon {
  font-size: 28px;
  color: #8c939d;
  width: 178px;
  height: 178px;
  line-height: 178px;
  text-align: center;
}
.avatar {
  width: 178px;
  height: 178px;
  display: block;
}
</style>