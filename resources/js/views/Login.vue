<script lang="ts" setup>
import { ref } from 'vue';
import axios from 'axios';

import useAxios from '/@/js/compositions/UseAxios';
import useFirebase from '/@/js/compositions/UseFirebase';
interface Props {
  id?: string;
  password?: string;
  companyCode?: string;
  errorMessage?: string;
}
const props = defineProps<Props>();

const { axiosErrorHandling, asyncMethodLock, errorToast } = useAxios();
const { isMobile } = useFirebase();
const serviceId = 'edu-time';
const ssoUrl = ref<string>(process.env.MIX_SSO_LOGIN_URL ?? '');
const callbackUrl = `${process.env.MIX_APP_URL}/callback`;
const id = ref<string>(props.id ?? '');
const password = ref<string>(props.password ?? '');
const companyCode = ref<string>(props.companyCode ?? '');
const ssoForm = ref<HTMLFormElement | null>(null);

interface CustomWindow extends Window {
  timelineCsrf?: string;
}
declare const window: CustomWindow;
const timelineCsrf = ref<string>(window.timelineCsrf ?? '');

const submitSsoLogin = async (e) => {
  // ログ書き出しのためのsession保存
  const defaultHome = isMobile() ? 'sphome' : 'home';
  await asyncMethodLock(async () => {
    await axios
      .post('/beforeLogin', {
        id: id.value,
        password: password.value,
        companyCode: companyCode.value,
        defaultHome: defaultHome,
      })
      .then((res) => {
        let data = res.data.data;
        if (data.ssoUrl) {
          ssoUrl.value = data.ssoUrl;
          if (ssoForm.value) {
            ssoForm.value.action = data.ssoUrl;
            ssoForm.value.submit();
          }
        }
      })
      .catch((err) => {
        const result = axiosErrorHandling(err);
        errorToast(result.code, result.message);
      });
  });
};
</script>

<template>
  <header id="header">
    <div class="inner upper bg-assist-grad"></div>
  </header>
  <main class="login">
    <!--.loginクラス-->
    <section>
      <div class="inner">
        <img src="/images/assist-login.png" />
        <h1>assistタイムライン</h1>
        <form
          id="sso_form"
          ref="ssoForm"
          :action="ssoUrl"
          method="POST"
          class="login_form"
          @submit.prevent="submitSsoLogin"
        >
          <input name="service_id" type="hidden" :value="serviceId" />
          <input name="redirect_url" type="hidden" :value="callbackUrl" />
          <input name="_token" type="hidden" :value="timelineCsrf" />
          <div class="input_parts">
            <input
              v-model="companyCode"
              name="company_code"
              type="text"
              placeholder="法人コード"
              required
            />
          </div>
          <div class="input_parts">
            <input
              v-model="id"
              name="login_id"
              type="text"
              placeholder="ID"
              required
            />
          </div>
          <div class="input_parts">
            <input
              v-model="password"
              name="password"
              type="text"
              placeholder="パスワード"
              required
            />
          </div>
          <button type="submit" class="bluebtn btn">
            <i class="fa fa-lock fa_blank" aria-hidden="true"></i>
            ログイン
          </button>
          <div class="inner redtxt">{{ errorMessage }}</div>
          <div class="inner">
            <!-- <span class="tips">
              ログインできない場合は運用管理者までお問い合わせください。
            </span> -->
          </div>
        </form>
      </div>
    </section>
  </main>
</template>

<style>
.bg-assist-grad {
  @apply bg-gradient-to-r from-assist-grad-from to-assist-grad-to;
}
</style>
