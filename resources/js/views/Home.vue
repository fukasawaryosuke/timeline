<template>
  <Modal
    :modal-open="imageModalVisible"
    :image-path="imageModalPath"
    @close="imageModalClose"
  />
  <MovieModal
    v-if="movieModalVisible"
    :modal-open="movieModalVisible"
    :movie-path="movieModalPath"
    @close="movieModalClose"
  />
  <ConfirmModal
    :modal-open="deleteModalVisible"
    confirm-label="削除"
    message="日報を削除します。よろしいですか？"
    @event="deleteDiaryRequest"
    @close="deleteModalClose"
  />
  <CardReportModal
    :report-modal-open="CardReportModalVisible"
    :diary-detail="diary"
    :login-user="loginUser"
    :image-modal-open="imageModalOpen"
    :movie-modal-open="movieModalOpen"
    :image-modal-close="imageModalClose"
    :diary-comments="diaryComments"
    :comment-reply="commentReply"
    :mention-users="mentionUsers"
    :mention-reset="mentionReset"
    @close="cardReportModalClose"
  />
  <OrganizationModal
    :orgz-tree-list="organizations"
    :orgz-modal-open="orgzModalVisible"
    :update-division-code="updateDivisionCode"
    :update-home-mode="updateHomeMode"
    @close="orgzModalClose"
  />
  <div class="bg-stone-50">
    <div class="fixed w-full z-50 h-36">
      <SnsHeader
        :login-user="loginUser"
        :mode="mode"
        :notice-list="noticeList"
        :notice-top="noticeTop"
        :ihs-list="inHouseSystems"
        :must-read-list="mustReadList"
        :get-diary="getDiary"
        :update-ihs="updateIhs"
        @mode-change="modeChange"
        @show-notice="showNotice"
        @show-ihs="showIhs"
        @show-must-read="showMustRead"
      />
      <TabMenu
        :login-user="loginUser"
        :ihs="inHouseSystems"
        :organizations="organizations"
        :selected-division="selectedDivisionCodes"
        :swtich-report-view="swtichReportView"
        :selected-user-name="selectedUserName"
        :system-name="systemName"
        :selected-ihs-id="selectedIhsId"
        :reset-ihs="resetIhs"
        :update-division-codes="updateDivisionCode"
        :update-home-mode="updateHomeMode"
        :selected-user-is-me="selectedUserIsMe"
        :change-view="changeView"
        :update-ihs="updateIhs"
        :reset-division="resetDivisionCode"
        :orgz-modal-open="orgzModalOpen"
        :selected-user="selectedUser"
        :selected-user-only-diary="selectedUserOnlyDiary"
      />
    </div>
    <div class="w-full items-start pt-36">
      <div class="w-full items-start flex justify-center relative">
        <div
          v-if="swtichReportView === 0"
          class="sticky top-44 mt-8 h-[calc(100vh_-_14rem)] overflow-y-auto"
        >
          <HomeIhs v-if="selectedIhsId" :selected-ihs-id="selectedIhsId" />
          <HomeProfile :login-user="loginUser" />
        </div>
        <div :class="{ 'w-176 mx-8': swtichReportView === 0 }">
          <!-- TODO: 引数が多すぎるのでリファクタリング -->
          <HomeReport
            v-if="swtichReportView === 0"
            :division-codes="selectedDivisionCodes"
            :login-user="loginUser"
            :is-home="isHomeSelected"
            :search-date="searchDateStr"
            :selected-user="selectedUser"
            :image-modal-open="imageModalOpen"
            :image-modal-close="imageModalClose"
            :movie-modal-open="movieModalOpen"
            :movie-modal-close="movieModalClose"
            :goto-edit-diary="gotoEditDiary"
            :delete-modal-open="deleteModalOpen"
            :indexdata="indexdata"
            :selected-user-only-diary="selectedUserOnlyDiary"
            :created-by="createdBy"
            :loading-flag="loadingFlag"
            :load-data="loadData"
            :init-loading="initLoading"
            :browsing-histories="browsingHistories"
            :toggle-history="toggleHistory"
            :close-history="closeHistory"
            :mention-flags="mentionFlags"
            :mention-users="mentionUsers"
            :selected-ihs-id="selectedIhsId"
            :diary-comments="diaryComments"
            :comment-reply="commentReply"
            @mention-start="mentionStart"
            @add-mention-user="addMentionUser"
            @remove-mention-user="removeMentionUser"
            @mention-reset="mentionReset"
          />
          <HomeCardReport
            v-if="swtichReportView === 1"
            :division-codes="selectedDivisionCodes"
            :login-user="loginUser"
            :is-home="isHomeSelected"
            :search-date="searchDateStr"
            :selected-user="selectedUser"
            :image-modal-open="imageModalOpen"
            :image-modal-close="imageModalClose"
            :movie-modal-open="movieModalOpen"
            :movie-modal-close="movieModalClose"
            :card-report-modal-open="cardReportModalOpen"
            :card-report-modal-close="cardReportModalClose"
            :goto-edit-diary="gotoEditDiary"
            :delete-modal-open="deleteModalOpen"
            :indexdata="indexdata"
            :selected-user-only-diary="selectedUserOnlyDiary"
            :created-by="createdBy"
            :loading-flag="loadingFlag"
            :load-data="loadData"
            :init-loading="initLoading"
            :selected-ihs-id="selectedIhsId"
          />
        </div>
        <div
          v-if="swtichReportView === 0"
          class="sticky top-44 mt-8 h-[calc(100vh_-_14rem)] overflow-y-auto"
        >
          <HomeMustReadTimeline
            :must-read-list="mustReadList"
            :get-diary="getDiary"
          />
        </div>
      </div>
    </div>
    <div
      v-if="Number(selectedIhsId) > 0"
      class="fixed bottom-10 flex items-center justify-center right-20 w-40 h-12 bg-assist-grad shadow-lg shadow-slate-700 rounded-full text-cyan-50 cursor-pointer p-2 text-3xl z-20"
      @click="goSendReport"
    >
      <fa icon="fa-pen" class="text-3xl pr-1" />
      作成
    </div>
    <div class="pb-10" />
  </div>

  <SetPasswordModal
    :is-open="setPasswordModal.isOpen.value"
    :message="setPasswordModal.message"
    :sub-message="setPasswordModal.subMessage"
    :confirm-label="'設定する'"
    @close_modal="setPasswordModal.close()"
    @click_right_button="processPassword"
  />
</template>

<script setup lang="ts">
import SnsHeader from '../components/SnsHeader.vue';
import TabMenu from '../components/home/TabMenu.vue';
import HomeNotice from '../components/home/main/HomeNotice.vue';
import MainContainer from '../components/home/MainContainer.vue';
import Modal from '../components/modal/Modal.vue';
import ConfirmModal from '../components/modal/ConfirmModal.vue';
import CardReportModal from '../components/modal/CardReportModal.vue';
import MovieModal from '/@/js/components/modal/MovieModal.vue';
import OrganizationModal from '/@/js/components/modal/OrganizationModal.vue';
import HomeProfile from '/@/js/components/home/main/HomeProfile.vue';
import HomeReport from '/@/js/components/home/main/HomeReport.vue';
import HomeCardReport from '/@/js/components/home/main/HomeCardReport.vue';
import HomeMustReadTimeline from '/@/js/components/home/main/HomeMustReadTimeline.vue';
import { useRouter } from 'vue-router';
import {
  ref,
  reactive,
  onBeforeMount,
  onMounted,
  watch,
  computed,
  onUnmounted,
} from 'vue';
import axios from 'axios';
import { LoginUser, Organization, InHouseSystem, User } from '../models/User';
import { OrganizationTreeListModel } from '../models/Organization';
import useNotice from '../compositions/UseNotice';
import { create } from 'domain';
import UseReport from '/@/js/compositions/UseReport';
import useAxios from '/@/js/compositions/UseAxios';
import HomeIhs from '../components/home/main/HomeIhs.vue';
import { MustReadListModel } from '/@/js/models/MustRead';
import SetPasswordModal from '/@/js/components/modal/SetPasswordModal.vue';
import { Modal as ModalClass } from '/@/js/utils/Modal';
const {
  indexdata,
  loadingFlag,
  mentionFlags,
  mentionUsers,
  browsingHistories,
  createdBy,
  selectedIhsId,
  systemName,
  diaryComments,
  loadData,
  initLoading,
  toggleHistory,
  closeHistory,
  deleteDiary,
  selectedUserOnlyDiary,
  updateIhs,
  resetIhs,
  resetLocalStorageIhs,
  commentReply,
} = UseReport();

interface Props {
  loginUser: LoginUser;
  isHumanAcademy?: boolean;
}
const props = defineProps<Props>();

const router = useRouter();

const { noticeList, getNotice } = useNotice();

const { asyncMethodLock, axiosErrorHandling, successToast, errorToast } =
  useAxios();

const goSendReport = () => {
  router.push({ name: 'SendReport', query: { ihsId: selectedIhsId.value } });
};
const gotoEditDiary = (diaryId: number) => {
  router.push({ name: 'EditReport', params: { reportId: diaryId } });
};

const mode = ref<number>(0);

const imageModalVisible = ref<boolean>(false);
const movieModalVisible = ref<boolean>(false);
const deleteModalVisible = ref<boolean>(false);
const orgzModalVisible = ref<boolean>(false);
const deleteDiaryId = ref<number>(0);
const deleteDiaryIndex = ref<number>(0);
const deleteDiaryMethod = ref<(diaryId: number) => void>();
const imageModalPath = ref<string>('');
const movieModalPath = ref<string>('');
const organizations = reactive<OrganizationTreeListModel>(
  new OrganizationTreeListModel()
);
const inHouseSystems = reactive<InHouseSystem[]>([]);
const selectedDivisionCodes = ref<number[]>([]);
const isHomeSelected = ref<boolean>(false);
const CardReportModalVisible = ref<boolean>(false);
const diary = ref<any>('');
const noticeTop = ref<string>('');
const noticeLeft = ref<string>('');
const selectedUserName = ref<string>('');
const mustReadList = reactive<MustReadListModel>(new MustReadListModel());

const isHumanAcademy = computed(() => {
  return props.isHumanAcademy;
});

/** life cycle */
onBeforeMount(() => {
  getNotice();
});

onMounted(() => {
  axios
    .get('/api/organization')
    .then((res) => {
      organizations.init(res.data);
    })
    .catch((err) => {
      const result = axiosErrorHandling(err);
      errorToast(result.code, result.message);
    });

  axios
    .get('/api/ihs/list')
    .then((res) => {
      inHouseSystems.length = 0;
      inHouseSystems.push(...res.data);
    })
    .catch((err) => {
      const result = axiosErrorHandling(err);
      errorToast(result.code, result.message);
    });
  axios
    .get('/api/must/showNotice')
    .then((res) => {
      mustReadList.init(res.data);
    })
    .catch((err) => {
      const result = axiosErrorHandling(err);
      errorToast(result.code, result.message);
    });

  if (selectedDivisionCodes.value.length == 0 && props.loginUser.organization) {
    selectedDivisionCodes.value.push(props.loginUser.organization.division_cd);
  }
  // お知らせアコーディオン関連以外の場所をクリックしたら閉じるようにする?
  window.addEventListener('click', (e) => {
    isOnHeaderMenu(e);
  });
  // お知らせアコーディオン位置調整
  noticeTop.value = getNoticeMenuTop();
  // localstorageデータリセット
  window.addEventListener('unload', () => {
    resetLocalStorageIhs();
  });
});

onUnmounted(() => {
  window.removeEventListener('click', isOnHeaderMenu);
  window.removeEventListener('unload', resetLocalStorageIhs);
});

watch(
  () => props.loginUser,
  (loginUser) => {
    if (props.loginUser.is_initial_password == 1 && !isHumanAcademy.value) {
      setPasswordModal.open();
    }

    if (loginUser.organization) {
      updateDivisionCode([loginUser.organization.division_cd]);
    }
  },
  { deep: true }
);

// method
const modeChange = (val: number) => {
  mode.value = val;
  // if (mode.value === 1) {
  //   getNotice();
  // }
};
const showNotice = () => {
  mode.value = 1;
  // if (mode.value === 1) {
  //   getNotice();
  // }
};
const showIhs = () => {
  mode.value = 2;
};
const showMustRead = () => {
  mode.value = 3;
};

const imageModalOpen = (path: string) => {
  imageModalPath.value = path;
  imageModalVisible.value = true;
};
const imageModalClose = () => {
  imageModalPath.value = '';
  imageModalVisible.value = false;
};
const movieModalOpen = (path: string) => {
  movieModalPath.value = path;
  movieModalVisible.value = true;
};
const movieModalClose = () => {
  movieModalPath.value = '';
  movieModalVisible.value = false;
};
const deleteModalOpen = (diaryId: number, diaryIndex: number) => {
  deleteDiaryId.value = diaryId;
  deleteDiaryIndex.value = diaryIndex;
  deleteModalVisible.value = true;
};
const deleteModalClose = () => {
  deleteModalVisible.value = false;
};
const orgzModalOpen = () => {
  orgzModalVisible.value = true;
};
const orgzModalClose = () => {
  orgzModalVisible.value = false;
};
const deleteDiaryRequest = async () => {
  deleteModalClose();
  await axios
    .delete(`/api/diary/deleteDiary`, {
      data: {
        diary_id: deleteDiaryId.value,
      },
    })
    .then(() => {
      deleteDiary(deleteDiaryIndex.value);
      deleteModalClose();
    })
    .catch((err) => {
      const result = axiosErrorHandling(err);
      errorToast(result.code, result.message);
    });
};
const updateHomeMode = (flg: boolean) => {
  isHomeSelected.value = flg;
  selectedUserName.value = props.loginUser.user_name;
  selectedUserInit();
};
const updateDivisionCode = (cds: number[]) => {
  selectedDivisionCodes.value.length = 0;
  selectedDivisionCodes.value.push(...cds);
  selectedUserInit();
};
const resetDivisionCode = () => {
  selectedDivisionCodes.value.length = 0;
  selectedUserInit();
};
const selectedUserIsMe = () => {
  selectedUserName.value = props.loginUser.user_name;
};
const cardReportModalOpen = (diary_detail: any) => {
  diaryComments.value[diary_detail.id] = '';
  if (!mentionUsers.value[diary_detail.id]) {
    mentionUsers.value[diary_detail.id] = [];
  }
  if (!mentionFlags.value[diary_detail.id]) {
    mentionFlags.value[diary_detail.id] = false;
  }
  diary.value = diary_detail;
  CardReportModalVisible.value = true;
};
const cardReportModalClose = () => {
  diary.value = '';
  CardReportModalVisible.value = false;
};
const getDiary = (diary_id: number) => {
  axios
    .get('/api/diary/diaryDetail', {
      params: {
        diary_id: diary_id,
      },
    })
    .then((res) => {
      mode.value = 0;
      cardReportModalOpen(res.data);
    })
    .catch((err) => {
      const result = axiosErrorHandling(err);
      errorToast(result.code, result.message);
    });
};
const isOnHeaderMenu = (e) => {
  if (!isOnNotice(e) && !isOnIhs(e) && !isOnMustRead(e)) {
    mode.value = 0;
  }
};
const isOnNotice = (e) => {
  const target = e.target as HTMLElement;
  return target.closest('.closest-notice') ? true : false;
};
const isOnIhs = (e) => {
  const target = e.target as HTMLElement;
  return target.closest('.closest-ihs') ? true : false;
};
const isOnMustRead = (e) => {
  const target = e.target as HTMLElement;
  return target.closest('.closest-must') ? true : false;
};

const getNoticeMenuTop = () => {
  const header = document.getElementById('header') as HTMLElement;
  const height = header?.offsetHeight - 12 || 0; // py-1 = 4px * 2 = 8px 分減算
  return `${String(height)}px`;
};

const selectedUserInit = () => {
  selectedUserName.value = '';
};
const selectedUser = (userName: string) => {
  selectedUserName.value = userName;
};

/** HomeMenu */
const swtichReportView = ref<number>(
  Number(localStorage.getItem('humanAssistTimeLineView')) | 1
);

const changeView = (viewNum: number) => {
  mentionUsers.value.forEach((u) => {
    u.length = 0;
  });
  diaryComments.value.length = 0;
  swtichReportView.value = viewNum;
  localStorage.setItem('humanAssistTimeLineView', String(viewNum));
};

// const updateIhs = (id: number) => {
//   selectedIhsId.value = id;
// };

/** Report */
const dateFormatLavel = 'YYYY年MM月DD日';
const dateFormatSearch = 'YYYY-MM-DD';
const reportSearchDate = reactive<Date>(new Date());
const getSearchDateStr = (dateFormat: string, d: Date) => {
  let result = dateFormat.replace('YYYY', String(d.getFullYear()));
  result = result.replace('MM', String(d.getMonth() + 1));
  result = result.replace('DD', String(d.getDate()));
  return result;
};
const searchDateStr = ref<string>(
  getSearchDateStr(dateFormatSearch, reportSearchDate)
);
// const searchDateLavel = ref<string>(
//   getSearchDateStr(dateFormatLavel, reportSearchDate)
// );
// const searchDateNext = () => {
//   reportSearchDate.setDate(reportSearchDate.getDate() + 1);
//   searchDateStr.value = getSearchDateStr(dateFormatSearch, reportSearchDate);
//   searchDateLavel.value = getSearchDateStr(dateFormatLavel, reportSearchDate);
// };
// const searchDatePrev = () => {
//   reportSearchDate.setDate(reportSearchDate.getDate() - 1);
//   searchDateStr.value = getSearchDateStr(dateFormatSearch, reportSearchDate);
//   searchDateLavel.value = getSearchDateStr(dateFormatLavel, reportSearchDate);
// };
/** mention TODO: compositionにできるかも */
const mentionStart = (id: number) => {
  mentionFlags.value[id] = !mentionFlags.value[id];
};
const addMentionUser = (id: number, user: User) => {
  mentionUsers.value[id].push(user);
};
const removeMentionUser = (id: number, userId: number) => {
  const removeIdx = mentionUsers.value[id].findIndex((u: User) => {
    return u.id == userId;
  });
  if (removeIdx > -1) {
    mentionUsers.value[id].splice(removeIdx, 1);
  }
};
const mentionReset = (diary_id: number) => {
  mentionFlags.value[diary_id] = false;
  mentionUsers.value[diary_id].length = 0;
};

// モーダル
const setPasswordModal = new ModalClass(
  'パスワード設定モーダル',
  '新しいパスワードを設定してください',
  'パスワードは6文字以上かつ数字、アルファベットを組み合わせてください。'
);

interface InputData {
  password: string;
  confirmPassword: string;
}

const processPassword = async (input_data: InputData): Promise<void> => {
  const hasError = validation(input_data);
  if (hasError) return;

  const formData = createFormData(input_data);

  putPassword(formData);
};

//バリデーション
const validation = (input_data: InputData): boolean => {
  const isEmpty = (value: string): boolean => !value || value.trim() === '';
  const mismatchPassword = (): boolean => {
    return input_data.password !== input_data.confirmPassword;
  };

  if (isEmpty(input_data.password)) {
    errorToast(400, 'パスワードが入力されていません。');
    return true;
  }

  if (isEmpty(input_data.confirmPassword)) {
    errorToast(400, 'パスワード(確認用)が入力されていません。');
    return true;
  }

  if (mismatchPassword()) {
    errorToast(400, 'パスワードとパスワード(確認用)が一致しません。');
    return true;
  }

  //NOTE:パスワードは6文字以上かつ数字、アルファベットを組み合わせる
  const regex = /^(?=.*?[a-z])(?=.*?\d)[a-z\d]{6,}$/i;
  if (!regex.test(input_data.password)) {
    errorToast(
      400,
      'パスワードは6文字以上かつ数字、アルファベットを組み合わせてください。'
    );
    return true;
  }

  return false;
};

//フォームデータ作成
const createFormData = (input_data: InputData): FormData => {
  const formData = new FormData();
  formData.append('userId', String(props.loginUser.id));
  formData.append('password', input_data.password);
  formData.append('confirmPassword', input_data.confirmPassword);

  return formData;
};

//パスワード更新
const putPassword = async (form_data: FormData): Promise<void> => {
  await asyncMethodLock(async () => {
    await axios
      .post('/api/account/update/password', form_data, {
        headers: {
          'Content-type': 'multipart/form-data',
          'X-HTTP-Method-Override': 'PUT',
        },
      })
      .then(() => {
        successToast(200, 'パスワードの更新に成功しました。');
        setPasswordModal.close();
      })
      .catch((err) => {
        const result = axiosErrorHandling(err);
        errorToast(result.code, result.message);
      });
  });
};
</script>

<style>
/* html {
  background-color: rgb(230, 253, 252);
} */
</style>

<style scoped>
.sticky {
  -ms-overflow-style: none; /* IE, Edge 対応 */
  scrollbar-width: none; /* Firefox 対応 */
}
.sticky::-webkit-scrollbar {
  display: none; /* Chrome, Safari 対応 */
}
</style>
