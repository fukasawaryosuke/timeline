<script setup lang="ts">
import { LoginUser } from '/@/js/models/User';
interface Props {
  loginUser: LoginUser;
  snsChange: number;
}
const Props = defineProps<Props>();
</script>

<template>
  <div class="show_contents">
    <div class="profile_ico">
      <img
        class="icon_trimming"
        :src="`/api/users/getProfileIcon/${String(loginUser.human_id)}`"
        alt="iconImage"
        loading="lazy"
        decoding="async"
      />
    </div>
    <div class="single_article">
      <div class="org_filter">
        <h3>名前</h3>
        {{ loginUser.user_name }}
      </div>
    </div>
    <div class="single_article">
      <div class="org_filter">
        <h3>メールアドレス</h3>
        {{ loginUser.mailadd }}
      </div>
    </div>
    <div class="single_article">
      <div class="org_filter">
        <h3>所属部門</h3>
        {{ loginUser.organization?.organization_name }}
      </div>
    </div>
  </div>
</template>
