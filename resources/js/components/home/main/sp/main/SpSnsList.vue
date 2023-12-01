<template>
  <nav class="menu_page">
    <SpHomeIhsList
      v-show="snsChange === 3"
      :ihs-list="ihsList"
      :selected-ihs-id="selectedIhsId"
      @select-ihs="selectIhs"
    />

    <SpHomeNotice
      v-show="snsChange === 4"
      :notice-list="noticeList"
      @select-notice-diary="getDiary"
    />

    <SpHomeMustRead
      v-show="snsChange === 5"
      :must-read-list="mustReadList"
      :get-diary="getDiary"
    />
  </nav>
</template>

<script setup lang="ts">
import { InHouseSystem, LoginUser } from '/@/js/models/User';
import { nextTick } from 'vue';
import { NoticeListModel } from '/@/js/models/Notice';
import SpHomeNotice from '/@/js/components/home/main/sp/main/SpHomeNotice.vue';
import SpHomeIhsList from '/@/js/components/home/main/sp/main/SpHomeIhsList.vue';
import UseReport from '/@/js/compositions/UseReport';
import SpHomeMustRead from '/@/js/components/home/main/sp/main/SpHomeMustRead.vue';
import { MustReadListModel } from '/@/js/models/MustRead';
interface Props {
  loginUser: LoginUser;
  noticeList: NoticeListModel;
  getDiary: (diary_id: number) => void;
  snsChange: number;
  ihsList: InHouseSystem[];
  updateIhs: (id: number, name: string) => void;
  mustReadList: MustReadListModel;
  heightPut: () => void;
}
const props = defineProps<Props>();

interface Emits {
  (e: 'modeChange', mode: number): void;
}
const emit = defineEmits<Emits>();

const { selectedIhsId } = UseReport();
const selectIhs = (ihsId: number, ihsName: string) => {
  emit('modeChange', 0);
  props.updateIhs(ihsId, ihsName);
  nextTick(() => {
    props.heightPut();
  });
};
</script>

<style scoped></style>

<style>
.invisible-scroll-bar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}

.invisible-scroll-bar::-webkit-scrollbar {
  display: none;
}
/* footerと被るため */
.mb-58 {
  margin-bottom: 58px;
}
</style>
