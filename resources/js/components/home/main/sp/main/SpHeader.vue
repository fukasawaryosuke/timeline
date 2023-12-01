<script setup lang="ts">
import { LoginUser } from '/@/js/models/User';
import BurgerMenu from '/@/js/components/home/main/sp/main/BurgerMenu.vue';
interface Props {
  loginUser: LoginUser;
  systemName: string;
  snsChange: (val: number) => void;
  burgerOpen: (flg: boolean) => void;
  burgerOpenVisible: boolean;
  cardReportModalClose: () => void;
  headerHeight: string;
  selectDivisionReset: () => void;
  snsChangeValue: number;
}
const props = defineProps<Props>();

interface Emits {
  (e: 'modeChange', mode: number): void;
}
const emit = defineEmits<Emits>();

const searchUser = () => {
  props.selectDivisionReset();
  if (props.snsChangeValue == 6) {
    emit('modeChange', 0);
  } else {
    emit('modeChange', 6);
  }
};
</script>

<template>
  <div class="inner upper bg-assist-grad">
    <div class="header_blank">
      <BurgerMenu
        :sns-change="snsChange"
        :burger-open="burgerOpen"
        :burger-open-visible="burgerOpenVisible"
        :login-user="loginUser"
        :card-report-modal-close="cardReportModalClose"
      />
    </div>
    <h1 class="logo">
      {{ systemName }}
    </h1>
    <div class="header_blank end_area">
      <fa icon="fa-search" class="search-button" @click="searchUser"></fa>
      <a
        href="https://ha-all-employee.zendesk.com/hc/ja/categories/10485521418265-%E3%82%A2%E3%82%B7%E3%82%B9%E3%83%88%E3%82%BF%E3%82%A4%E3%83%A0%E3%83%A9%E3%82%A4%E3%83%B3%E3%81%AB%E3%81%A4%E3%81%84%E3%81%A6"
        title="操作でわからないことがあれば、こちらをクリック！"
        target="_blank"
        class="question"
      >
        <fa icon="question" />
      </a>
    </div>
  </div>
</template>
<style scoped>
.question {
  display: flex;
  width: 28px;
  height: 28px;
  background-color: rgb(255 255 255 / 0.5);
  border-radius: 100px;
  color: rgb(107 114 128);
  align-items: center;
  justify-content: center;
}
.end_area{
  display: flex;
  align-items: center;
  width: auto;
}
.search-button{
  width: 20px;
  height: 20px;
  position: relative;
  right: 20px;
}
.logo{
  padding-left: 20px;
}
</style>
