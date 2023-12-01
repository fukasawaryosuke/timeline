<script setup lang="ts">
import SpHeader from '/@/js/components/home/main/sp/main/SpHeader.vue';
import SpHomeMenu from '/@/js/components/home/main/sp/main/SpHomeMenu.vue';
import SpHomeCardReport from '/@/js/components/home/main/sp/main/SpHomeCardReport.vue';
import SpCardReportView from '/@/js/components/home/main/sp/main/SpCardReportView.vue';
import SpCommentSubmit from '/@/js/components/home/main/sp/comment/SpCommentSubmit.vue';
import ConfirmModal from '/@/js/components/modal/ConfirmModal.vue';
import SpMovieModal from '/@/js/components/home/main/sp/modal/SpMovieModal.vue';
// import SpMovieModal from '/@/js/components/modal/MovieModal.vue';
import { OrganizationTreeListModel } from '/@/js/models/Organization';
import HomeMustReadTimeline from '/@/js/components/home/main/HomeMustReadTimeline.vue';
import { useRoute, useRouter } from 'vue-router';
import {
  ref,
  reactive,
  onBeforeMount,
  onMounted,
  onUnmounted,
  watch,
  nextTick,
  computed,
} from 'vue';
import axios from 'axios';
import { LoginUser, InHouseSystem, User } from '/@/js/models/User';
import useNotice from '/@/js/compositions/UseNotice';
import UseReport from '/@/js/compositions/UseReport';
import useAxios from '/@/js/compositions/UseAxios';
import useFirebase from '/@/js/compositions/UseFirebase';
import { MustReadListModel } from '/@/js/models/MustRead';
import SpTabMenu from '/@/js/components/home/main/sp/main/SpTabMenu.vue';
import SpSnsList from '/@/js/components/home/main/sp/main/SpSnsList.vue';
import SpSnsHeader from '/@/js/components/home/main/sp/main/SpSnsHeader.vue';
import SpProfile from './SpProfile.vue';
import SpOrganization from './SpOrganization.vue';
import SpModal from '/@/js/components/home/main/sp/modal/SpModal.vue';
import SpSearchUser from './SpSearchUser.vue';
import SetPasswordModal from '/@/js/components/modal/SetPasswordModal.vue';
import { Modal as ModalClass } from '/@/js/utils/Modal';

const {
  indexdata,
  loadingFlag,
  mentionFlags,
  mentionUsers,
  createdBy,
  selectedIhsId,
  systemName,
  diaryComments,
  resetSystemName,
  loadData,
  initLoading,
  deleteDiary,
  selectedUserOnlyDiary,
  updateIhs,
  resetIhs,
  resetLocalStorageIhs,
  commentReply,
} = UseReport();
interface Props {
  loginUser: LoginUser;
  push?: number;
  pushDiary?: any;
  isHumanAcademy?: boolean;
}
const props = defineProps<Props>();

const route = useRoute();

const router = useRouter();

const { noticeList, getNotice } = useNotice();

const { asyncMethodLock, axiosErrorHandling, successToast, errorToast } =
  useAxios();

const { isSupported, initFirebase, getDeviceType, getChannelType } =
  useFirebase();

const goSendReport = () => {
  router.push({ name: 'SpSendReport', query: { ihsId: selectedIhsId.value } });
};
const gotoEditDiary = (diaryId: number) => {
  router.push({ name: 'SpEditReport', params: { reportId: diaryId } });
};

const mode = ref<number>(0);

const imageModalVisible = ref<boolean>(false);
const movieModalVisible = ref<boolean>(false);
const deleteModalVisible = ref<boolean>(false);
const deleteDiaryId = ref<number>(0);
const deleteDiaryIndex = ref<number>(0);
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
const selectedUserName = ref<string>('');
const mustReadList = reactive<MustReadListModel>(new MustReadListModel());

const isHumanAcademy = computed(() => {
  return props.isHumanAcademy;
});

const selectedDivisionName = computed(() => {
  if (inHouseSystems.length <= 0) return '';
  const targetIndex = inHouseSystems.findIndex((i) => {
    return i.id == selectedIhsId.value;
  });
  if (targetIndex <= -1) return '';
  const orgz = inHouseSystems[targetIndex].organization.find((o) => {
    return selectedDivisionCodes.value.includes(o.division_cd);
    // return props.selectedDivision.includes(o.division_cd);
  });
  if (orgz) {
    return orgz.organization_name;
  }
  return '';
});

// スマホのブラウザがアクティブになる際に処理が再実行されるための回避策
// 401の場合はログイン画面に移動させる
const ssoHealthCheck = async () => {
  let isOk = false;
  await axios
    .get('/api/ssohealth')
    .then(() => {
      isOk = true;
    })
    .catch((err) => {
      const result = axiosErrorHandling(err);
      if (result.code == 401) {
        isOk = false;
      } else {
        isOk = true;
      }
    });
  return isOk;
};
/** life cycle */
onBeforeMount(async () => {
  getNotice();
});

onMounted(async () => {
  const isSsoHealth = await ssoHealthCheck();
  if (!isSsoHealth) {
    location.href = '/login';
    return false;
  }
  //SPではheader部分のsystemNameの初期表示をassistタイムラインにしたいためここで処理
  systemName.value = systemName.value || 'assistタイムライン';
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
  window.addEventListener('click', (e) => {
    isOnHeaderMenu(e);
  });

  if (selectedDivisionCodes.value.length == 0 && props.loginUser.organization) {
    selectedDivisionCodes.value.push(props.loginUser.organization.division_cd);
  }
  // プッシュ通知からの遷移であれば詳細画面表示
  if (props.push) {
    // NOTE: mentionUsersデータの初期化のため投稿データを取得
    loadData(
      isHomeSelected.value,
      createdBy.value,
      selectedDivisionCodes.value,
      searchDateStr.value
    );
    // diary.value = props.pushDiary;
    cardReportModalOpen(props.pushDiary);
    // プッシュのクエリを削除
    const query = Object.assign({}, route.query);
    delete query.push;
    router.replace({ query });
  }
  // firebase
  if (isSupported()) {
    Notification.requestPermission()
      .then(async (res) => {
        if (res === 'granted') {
          const currentToken = await initFirebase();
          const deviceType = getDeviceType();
          const userAgent = navigator.userAgent;
          const channelType = getChannelType();
          if (currentToken) {
            await axios
              .post('/api/push/device', {
                token: currentToken,
                type: deviceType,
                userAgent: userAgent,
              })
              .catch((err) => {
                console.dir(err);
                const result = axiosErrorHandling(err);
                errorToast(result.code, result.message);
              });

            await axios
              .put('/api/push/updateEndpoint', {
                address: currentToken,
                channelType: channelType,
              })
              .catch((err) => {
                console.dir(err);
                const result = axiosErrorHandling(err);
                errorToast(result.code, result.message);
              });
          }
        }
      })
      .catch((err) => {
        const result = axiosErrorHandling(err);
        errorToast(result.code, result.message);
      });
  }
  // localstorageデータリセット
  window.addEventListener('unload', () => {
    resetLocalStorageIhs();
  });
  heightPut();
});

onUnmounted(() => {
  window.removeEventListener('unload', resetLocalStorageIhs);
});

watch(
  () => props.loginUser,
  (loginUser) => {
    if (props.loginUser.is_initial_password == 1 && !isHumanAcademy.value) {
      setPasswordModal.open();
    }

    if (loginUser.organization) {
      updateDivisionCodes([loginUser.organization.division_cd]);
    }
  },
  { deep: true }
);

// method
const modeChange = (val: number) => {
  snsChange.value = val;
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
const resetDivisionCode = () => {
  selectedDivisionCodes.value.length = 0;
  selectedUserInit();
  nextTick(() => {
    heightPut();
  });
};
const selectedUserIsMe = () => {
  selectedUserName.value = props.loginUser.user_name;
  nextTick(() => {
    heightPut();
  });
};
const cardReportModalOpen = (diary_detail: any) => {
  if (!mentionUsers.value[diary_detail.id]) {
    mentionUsers.value[diary_detail.id] = [];
  }
  if (!mentionFlags.value[diary_detail.id]) {
    mentionFlags.value[diary_detail.id] = false;
  }
  diary.value = diary_detail;
  CardReportModalVisible.value = true;
  footerAreaMode.value = true;
  nextTick(() => {
    heightPut();
  });
};
const cardReportModalClose = () => {
  diary.value = '';
  CardReportModalVisible.value = false;
  footerAreaMode.value = false;
  nextTick(() => {
    heightPut();
  });
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
  swtichReportView.value = viewNum;
  localStorage.setItem('humanAssistTimeLineView', String(viewNum));
};

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
const myDiaryClicked = () => {
  updateDivisionCodes([]);
  updateHomeMode(true);
  selectedUserIsMe();
  snsModeChange(0);
  cardReportModalClose();
  nextTick(() => {
    heightPut();
  });
};
const updateDivisionCodes = (cds: number[]) => {
  selectedDivisionCodes.value.length = 0;
  selectedDivisionCodes.value.push(...cds);
  selectedUserInit();
};
const snsChange = ref<number>(0);
const history: number[] = [];
const snsModeChange = (val: number) => {
  snsChange.value = val;

  if (history.length >= 1 && val === history[history.length - 1]) {
    // ここでheightPutの処理を入れないとスタイルがくずれるため
    nextTick(() => {
      heightPut();
    });
    snsModeChange(0);
  } else {
    history.push(val);
  }
  nextTick(() => {
    heightPut();
    scrollTop();
  });
};
const homeClicked = () => {
  updateHomeMode(false);
  updateDivisionCodes([props.loginUser.organization?.division_cd]);
  cardReportModalClose();
  nextTick(() => {
    heightPut();
  });
};
const inputChanged = (dairy: any, cmt: string) => {
  diaryComments.value[dairy.id] = cmt;
};
const emojiAdd = (emoji: string, id: any) => {
  if (!diaryComments.value[id]) {
    diaryComments.value[id] = '';
    diaryComments.value[id] = diaryComments.value[id] + emoji;
  } else {
    diaryComments.value[id] = diaryComments.value[id] + emoji;
  }
};
const submitComment = async (diary_id: number) => {
  await asyncMethodLock(async () => {
    await axios
      .post('/api/comment/create', {
        diary_id: diary_id,
        comment: diaryComments.value[diary_id],
        mentions: mentionUsers.value[diary_id],
      })
      .then((res) => {
        if (res.data.success) {
          let com = res.data.received_comment;
          if (com) {
            if (diary.value) {
              diary.value.comment.push(com);
            }
          }

          // コメントをリストに反映したので入力欄を初期化
          diaryComments.value[com.diary_id] = '';
          mentionFlags.value[com.diary_id] = false;
          mentionUsers.value[com.diary_id].length = 0;

          let commentListId = 'comment_list_diary' + String(com.diary_id);
          setTimeout(() => {
            let commentList = document.getElementById(commentListId);
            if (commentList) {
              commentList.scrollTop = commentList.scrollHeight;
            }
          }, 300);
        }
      })
      .catch((err) => {
        const result = axiosErrorHandling(err);
        errorToast(result.code, result.message);
      });
  });
};

const burgerOpenVisible = ref<boolean>(false);
const burgerOpen = (flg: boolean) => {
  burgerOpenVisible.value = flg;
};
const headerHeight = ref<string>('');
const heightPut = () => {
  const height = document.getElementById('header');
  headerHeight.value = `${String(height?.offsetHeight)}px` || '0px';
};
const footerAreaMode = ref<boolean>(false);
const footerArea = (flg: boolean) => {
  footerAreaMode.value = flg;
};
const goHome = () => {
  updateHomeMode(false);
  updateDivisionCodes([props.loginUser.organization?.division_cd]);
  resetIhs();
  resetSystemName();
  snsModeChange(0);
  cardReportModalClose();
  nextTick(() => {
    heightPut();
  });
};
const mentionReset = (diary_id: number) => {
  mentionFlags.value[diary_id] = false;
  mentionUsers.value[diary_id].length = 0;
};
const scrollTop = () => {
  window.scroll({ top: 0 });
};
const isOnHeaderMenu = (e) => {
  const target = e.target as HTMLElement;
  const isResult = target.closest('#g-nav') ? true : false;
  const openbntResult = target.closest('.openbtn1') ? true : false;

  if (burgerOpenVisible.value && !isResult && !openbntResult) {
    burgerOpenVisible.value = false;
  }
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

<template>
  <SpModal
    :modal-open="imageModalVisible"
    :image-path="imageModalPath"
    @close="imageModalClose"
  />
  <SpMovieModal
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
  <header id="header">
    <SpHeader
      :login-user="loginUser"
      :system-name="systemName"
      :sns-change="snsModeChange"
      :burger-open="burgerOpen"
      :burger-open-visible="burgerOpenVisible"
      :card-report-modal-close="cardReportModalClose"
      :select-division-reset="resetDivisionCode"
      :sns-change-value="snsChange"
      @mode-change="modeChange"
    />
    <template v-if="!CardReportModalVisible">
      <SpSnsHeader
        :login-user="loginUser"
        :home-clicked="goHome"
        :my-diary-clicked="myDiaryClicked"
      />
      <SpHomeMenu
        v-if="snsChange === 0"
        :login-user="loginUser"
        :selected-user-name="selectedUserName"
        :selected-division-name="selectedDivisionName"
        :home-clicked="homeClicked"
        :update-division-codes="updateDivisionCodes"
        :update-home-mode="updateHomeMode"
        :select-division-reset="resetDivisionCode"
      />
    </template>
  </header>
  <main class="timeline_list">
    <section>
      <div class="inner" :style="{ marginTop: headerHeight }">
        <SpCardReportView
          v-show="CardReportModalVisible"
          :login-user="loginUser"
          :report-modal-open="CardReportModalVisible"
          :diary-detail="diary"
          :image-modal-open="imageModalOpen"
          :image-modal-close="imageModalClose"
          :movie-modal-open="movieModalOpen"
          :header-height="headerHeight"
          :comment-reply="commentReply"
          :mention-reset="mentionReset"
          :footer-area="footerArea"
          @close="cardReportModalClose"
        />
        <template v-if="!CardReportModalVisible">
          <SpSearchUser
            v-if="snsChange === 6"
            :selected-user="selectedUser"
            :selected-user-only-diary="
              (createdby) => {
                selectedUserOnlyDiary(createdby);
                nextTick(() => {
                  heightPut();
                });
              }
            "
            @mode-change="modeChange"
          />
          <SpSnsList
            v-if="snsChange >= 3 && snsChange <= 5"
            :login-user="loginUser"
            :notice-list="noticeList"
            :get-diary="getDiary"
            :sns-change="snsChange"
            :ihs-list="inHouseSystems"
            :update-ihs="updateIhs"
            :must-read-list="mustReadList"
            :height-put="heightPut"
            @mode-change="modeChange"
          />
          <SpOrganization
            v-else-if="snsChange === 2"
            :login-user="loginUser"
            :ihs="inHouseSystems"
            :orgz-tree-list="organizations"
            :selected-ihs-id="selectedIhsId"
            :selected-division="selectedDivisionCodes"
            :sns-change="snsModeChange"
            :update-division-codes="updateDivisionCodes"
            :update-home-mode="updateHomeMode"
          />
          <SpProfile
            v-else-if="snsChange === 1"
            :login-user="loginUser"
            :sns-change="snsChange"
          />
          <div v-show="snsChange === 0">
            <!-- TODO: 引数が多すぎるのでリファクタリング -->
            <SpHomeCardReport
              :division-codes="selectedDivisionCodes"
              :login-user="loginUser"
              :is-home="isHomeSelected"
              :search-date="searchDateStr"
              :image-modal-open="imageModalOpen"
              :image-modal-close="imageModalClose"
              :movie-modal-open="movieModalOpen"
              :movie-modal-close="movieModalClose"
              :card-report-modal-open="cardReportModalOpen"
              :card-report-modal-close="cardReportModalClose"
              :goto-edit-diary="gotoEditDiary"
              :delete-modal-open="deleteModalOpen"
              :selected-user="selectedUser"
              :indexdata="indexdata"
              :selected-user-only-diary="
                (createdby) => {
                  selectedUserOnlyDiary(createdby);
                  nextTick(() => {
                    heightPut();
                  });
                }
              "
              :header-height="headerHeight"
              :created-by="createdBy"
              :loading-flag="loadingFlag"
              :load-data="loadData"
              :init-loading="initLoading"
              :selected-ihs-id="selectedIhsId"
              :get-diary="getDiary"
              :must-read-list="mustReadList"
              :go-home="goHome"
            />
          </div>
        </template>
        <div v-if="swtichReportView === 0" class="sticky top-44 w-80">
          <HomeMustReadTimeline
            :must-read-list="mustReadList"
            :get-diary="getDiary"
          />
        </div>
        <div
          v-if="
            snsChange === 0 &&
            !CardReportModalVisible &&
            Number(selectedIhsId) > 0
          "
          id="add_article"
          @click="goSendReport"
        >
          <i class="fa fa-plus"></i>
        </div>
        <div class="pb-10" />
      </div>
    </section>
  </main>
  <footer>
    <SpTabMenu
      v-show="!footerAreaMode && !CardReportModalVisible"
      :notice-list="noticeList"
      :sns-change="snsModeChange"
      :tab-number="snsChange"
      :must-read-list="mustReadList"
      :selected-ihs-id="selectedIhsId"
    />
    <SpCommentSubmit
      v-if="footerAreaMode && !imageModalVisible && !movieModalVisible"
      :login-user="loginUser"
      :diary-detail="diary"
      :dairy-id="diary.id"
      :mention-users="mentionUsers[diary.id]"
      :content="diaryComments[diary.id] ?? ''"
      :changed="
        (cmt) => {
          inputChanged(diary, cmt);
        }
      "
      :remove-user="removeMentionUser"
      :add-mention-user="addMentionUser"
      @comment-send="submitComment(diary.id)"
      @coment-submit="submitComment"
      @submit-emoji="emojiAdd"
    />
  </footer>

  <SetPasswordModal
    :is-open="setPasswordModal.isOpen.value"
    :message="setPasswordModal.message"
    :sub-message="setPasswordModal.subMessage"
    :confirm-label="'設定する'"
    @close_modal="setPasswordModal.close()"
    @click_right_button="processPassword"
  />
</template>

<style>
.bg-assist-grad {
  @apply bg-gradient-to-r from-assist-grad-from to-assist-grad-to;
}
</style>
