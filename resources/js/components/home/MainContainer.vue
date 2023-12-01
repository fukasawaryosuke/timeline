<template>
  <div class="w-full items-start pt-36">
    <div class="w-full items-start flex justify-center relative">
      <div v-if="swtichReportView === 0" class="sticky top-40 mt-8">
        <HomeProfile :login-user="loginUser" />
      </div>
      <div :class="{ 'w-176 mx-8': swtichReportView === 0 }">
        <HomeReport
          v-if="swtichReportView === 0"
          :division-codes="divisionCodes"
          :login-user="loginUser"
          :is-home="isHomeSelected"
          :search-date="searchDateStr"
          :selected-user="selectedUser"
          :image-modal-open="imageModalOpen"
          :image-modal-close="imageModalClose"
          :goto-edit-diary="gotoEditDiary"
          :delete-modal-open="deleteModalOpen"
        />
        <HomeCardReport
          v-if="swtichReportView === 1"
          :division-codes="divisionCodes"
          :login-user="loginUser"
          :is-home="isHomeSelected"
          :search-date="searchDateStr"
          :selected-user="selectedUser"
          :image-modal-open="imageModalOpen"
          :image-modal-close="imageModalClose"
          :card-report-modal-open="cardReportModalOpen"
          :card-report-modal-close="cardReportModalClose"
          :goto-edit-diary="gotoEditDiary"
          :delete-modal-open="deleteModalOpen"
        />
      </div>
      <div v-if="swtichReportView === 0" class="spacer w-80"></div>
    </div>
  </div>
</template>

<script setup lang="ts">
import HomeMenu from "./main/HomeMenu.vue";
import HomeReport from "./main/HomeReport.vue";
import HomeCardReport from "./main/HomeCardReport.vue";
import { ref, reactive } from "vue";
import { LoginUser } from "../../models/User";
import HomeProfile from "./main/HomeProfile.vue";
import HomeNotice from "./main/HomeNotice.vue";
import UseReport from "/@/js/compositions/UseReport";

interface Props {
  divisionCodes: number[];
  loginUser: LoginUser;
  isHomeSelected: boolean;
  swtichReportView: number;
  selectedUser: (path: string) => void;
  imageModalOpen: (path: string) => void;
  imageModalClose: () => void;
  cardReportModalOpen: (diary: any) => void;
  cardReportModalClose: () => void;
  gotoEditDiary: (diaryId: number) => void;
  deleteModalOpen: (
    diaryId: number,
    diaryIndex: number,
    deleteDiary: (diaryIndex: number) => void
  ) => void;
}
defineProps<Props>();

interface Emits {
  (e: "selectNoticeDiary", diaryId): void;
  (e: "deleteModalOpen", diaryId): void;
}
const emit = defineEmits<Emits>();

// const {
//   indexdata,
//   loadingFlag,
//   mentionFlags,
//   mentionUsers,
//   browsingHistories,
//   createdBy,
//   loadData,
//   initLoading,
//   toggleHistory,
//   closeHistory,
//   deleteDiary,
//   selectedUserOnlyDiary,
// } = UseReport();

const dateFormatLavel = "YYYY年MM月DD日";
const dateFormatSearch = "YYYY-MM-DD";
const reportSearchDate = reactive<Date>(new Date());
const getSearchDateStr = (dateFormat: string, d: Date) => {
  let result = dateFormat.replace("YYYY", String(d.getFullYear()));
  result = result.replace("MM", String(d.getMonth() + 1));
  result = result.replace("DD", String(d.getDate()));
  return result;
};
const searchDateStr = ref<string>(
  getSearchDateStr(dateFormatSearch, reportSearchDate)
);
const searchDateLavel = ref<string>(
  getSearchDateStr(dateFormatLavel, reportSearchDate)
);
const searchDateNext = () => {
  reportSearchDate.setDate(reportSearchDate.getDate() + 1);
  searchDateStr.value = getSearchDateStr(dateFormatSearch, reportSearchDate);
  searchDateLavel.value = getSearchDateStr(dateFormatLavel, reportSearchDate);
};
const searchDatePrev = () => {
  reportSearchDate.setDate(reportSearchDate.getDate() - 1);
  searchDateStr.value = getSearchDateStr(dateFormatSearch, reportSearchDate);
  searchDateLavel.value = getSearchDateStr(dateFormatLavel, reportSearchDate);
};

// const swtichReportView = ref<number>(
//   Number(localStorage.getItem("humanAssistTimeLineView")) | 0
// );

// const changeView = (viewNum: number) => {
//   swtichReportView.value = viewNum;
//   localStorage.setItem("humanAssistTimeLineView", String(viewNum));
// };
// const selectedNoticeDiary = (diaryId: number) => {
//   emit("selectNoticeDiary", diaryId);
// };
</script>

<style scoped></style>
