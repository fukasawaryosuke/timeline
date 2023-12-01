<script setup lang="ts">
import { ref } from 'vue';
import { LoginUser } from '/@/js/models/User';
import axios from 'axios';
import useAxios from '/@/js/compositions/UseAxios';

interface Props {
  snsChange: (val: number) => void;
  burgerOpen: (flg: boolean) => void;
  burgerOpenVisible: boolean;
  loginUser: LoginUser;
  cardReportModalClose: () => void;
}
const { asyncMethodLock, axiosErrorHandling } = useAxios();
const props = defineProps<Props>();
const serviceId = 'edu-time';
const ssoUrl = process.env.MIX_SSO_LOGOUT_URL;
const callbackUrl = `${process.env.MIX_APP_URL}/login`;
const profile = () => {
  props.snsChange(1);
  props.burgerOpen(false);
  props.cardReportModalClose();
};
const ssoForm = ref<HTMLFormElement | null>(null);
const subscriptionUrl = process.env.MIX_SUBSCRIPTION_URL;

const submitSsoLogout = async (e) => {
  // ログ書き出しのためのsession保存
  await asyncMethodLock(async () => {
    await axios.post('/beforeLogout', {}).finally(() => {
      if (ssoForm.value) ssoForm.value.submit();
    });
  });
};

const subscription = () => {
  window.open(subscriptionUrl, '_blank');
};
</script>

<template>
  <div
    class="openbtn1"
    type="button"
    :class="{ active: burgerOpenVisible }"
    @click="burgerOpen(!burgerOpenVisible)"
  >
    <img :src="'images/assist_kun.png'" width="20px" height="20px" />
    {{ burgerOpenVisible ? 'CLOSE' : 'MENU' }}
  </div>
  <section
    :class="{
      modalArea: burgerOpenVisible,
      openModal: burgerOpenVisible,
      closeModal: !burgerOpenVisible,
    }"
  >
    <div id="modalBg" class="modalBg" @click="$emit('close')" />
    <div id="g-nav" :class="{ panelactive: burgerOpenVisible }">
      <div id="g-nav-list" class="drawer_font">
        <ul>
          <li>
            <a @click="profile">プロフィール</a>
          </li>
          <li v-if="subscriptionUrl">
            <a @click="subscription">まなびアシスト</a>
          </li>
        </ul>
        <div class="drawer_logout">
          <form
            ref="ssoForm"
            method="POST"
            :action="ssoUrl"
            @submit.prevent="submitSsoLogout"
          >
            <input name="service_id" type="hidden" :value="serviceId" />
            <input name="redirect_url" type="hidden" :value="callbackUrl" />
            <button type="submit" class="bluebtn btn">ログアウト</button>
          </form>
        </div>
      </div>
    </div>
  </section>
</template>
<style scoped>
.openModal {
  transition: 0.5s;
  opacity: 1;
}
.closeModal {
  transition: 0.5s;
  opacity: 0;
}
.modalArea {
  display: block;
  position: fixed;
  z-index: 9001;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: #00000099;
  transition: all 0.6s;
}

#modalBg {
  transition: all 0.6s;
}
.modalBg {
  width: 100%;
  height: 100%;
  background-color: rgba(30, 30, 30, 0);
}

.modalWrapper {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 98%;
  padding: 40px 1%;
  height: auto;
  overflow: auto;
  box-sizing: inherit;
  background-color: #fff;
  border: 1px solid rgb(6 182 212);
}
</style>
