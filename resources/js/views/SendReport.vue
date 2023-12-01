<template>
  <div>
    <Header :login-user="loginUser" />
    <div class="pt-14" />
    <SelectCategory :title="systemName" :description="description" />
    <!-- <TabMenu /> -->
    <SendReport
      v-model:is-require-notice="isRequireNotice"
      :diary="diaryTemplate"
      :diary-edit="edit"
      :report-page-mode="reportPageMode"
      :file-infos="fileInfos"
      :require-message="requireMessage"
      :require-notice="form.required"
      @get-last-report="getLastReport"
      @reset-media="resetMedia"
      @confirm-report="confirmReport"
      @edit-report="editReport"
      @submit-post-report="submitPostReport"
      @submit-put-report="submitPutReport"
      @delete-media="deleteMedia"
      @select-media="selectMedia"
      @save-draft="saveDraft"
    />
    <div class="pb-10" />
  </div>
</template>

<script setup lang="ts">
import Header from '../components/Header.vue';
import SendReport from '../components/home/sendreport/SendReport.vue';
import SelectCategory from '../components/home/sendreport/SelectCategory.vue';
import { LoginUser } from '../models/User';
import { ref, reactive, onMounted } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';
import UseAxios, { AxiosErrorType } from '/@/js/compositions/UseAxios';
import { IReportForm } from '/@/js/models/admin/ReportForm';
import { REPORT_CONTENT_TYPE } from '/@/js/contarcts/ReportContentType';
import { useToast } from 'vue-toastification';

interface Props {
  loginUser: LoginUser;
  form: IReportForm;
  edit: boolean;
  ihs?: number | string;
  id: number;
  systemName: string;
  description: string;
}
const props = defineProps<Props>();
const toast = useToast();
const diaryTemplate = reactive(props.form.diary);
const { asyncMethodLock, axiosErrorHandling, errorToast } = UseAxios();
// S3から画像を削除するためのリスト
const imagePathList = reactive<Array<string>>([]);
const deleteImageList = reactive<Array<string>>([]);
// Ulizaから動画を削除するためのリスト
const moviePathList = reactive<Array<string>>([]);
const movieDeleteList = reactive<Array<string>>([]);
// 必読投稿するかしないかのフラグ
const isRequireNotice = ref<boolean>(props.form.required);

const router = useRouter();

const MODE = {
  EDIT: 1,
  CONFIRM: 2,
};

const reportPageMode = ref<number>(MODE.EDIT);

const fileInfos = ref([] as any);

const resetMedia = (event: any) => {
  event.currentTarget.value = '';
};

onMounted(() => {
  diaryTemplate.contents.forEach((c) => {
    if (c.contentType == REPORT_CONTENT_TYPE.IMAGE && c.path) {
      // 初期表示時なので必ずstringで入る imagePathListの追加処理はここ以外では行わないこと
      imagePathList.push(c.path as string);
    } else if (c.contentType == REPORT_CONTENT_TYPE.MOVIE && c.path) {
      // 初期表示時なので必ずstringで入る moviePathListの追加処理はここ以外では行わないこと
      moviePathList.push(c.path as string);
    }
  });
});

// メディアファイル選択
const selectMedia = (event: any, index: number, accept: RegExp) => {
  const selectFile: File = event.target.files[0];
  // キャンセル時
  if (selectFile === undefined) {
    return false;
  }
  if (accept.test(selectFile.type)) {
    diaryTemplate.contents[index].path = selectFile;
    diaryTemplate.contents[index].content = selectFile.name;
  } else {
    resetMedia(event);
    diaryTemplate.contents[index].path = '';
    diaryTemplate.contents[index].content = '';
    toast.error('ファイル形式が違います');
  }
};
// 選択した画像削除
const deleteMedia = (targetId: string, index: number) => {
  const target = document.getElementById(targetId) as HTMLInputElement;
  if (target && target.files && target.files.length > 0) {
    target.value = '';
  }
  if (
    diaryTemplate.contents[index].contentType == REPORT_CONTENT_TYPE.IMAGE &&
    imagePathList.includes(diaryTemplate.contents[index].path as string)
  ) {
    deleteImageList.push(diaryTemplate.contents[index].path as string);
  } else if (
    diaryTemplate.contents[index].contentType == REPORT_CONTENT_TYPE.MOVIE &&
    moviePathList.includes(diaryTemplate.contents[index].path as string)
  ) {
    movieDeleteList.push(diaryTemplate.contents[index].path as string);
  }
  diaryTemplate.contents[index].path = '';
  diaryTemplate.contents[index].content = '';
  // 既にファイルアップロードされていたら削除用の配列に追加
};

const requireMessage = ref<string[]>([]);

const submitPostReport = async () => {
  await asyncMethodLock(async () => {
    const imageUploadResult = await imageUpload();
    if (!imageUploadResult) return false;

    const movieUploadResult = await movieUpload();
    if (!movieUploadResult) return false;

    await axios
      .post(`/api/diary/newDairyCreate/${props.ihs}`, {
        diaryTemplate: diaryTemplate,
        requierNotice: isRequireNotice.value, // 日報作成時のみ必読投稿フラグを付与
      })
      .then(() => {
        router.push({ name: 'Home' });
      })
      .catch((err) => {
        const result = axiosErrorHandling(err);
        errorToast(result.code, result.message);
      });
  });
};

const saveDraft = async () => {
  await asyncMethodLock(async () => {
    const imageUploadResult = await imageUpload();
    if (!imageUploadResult) return false;

    const movieUploadResult = await movieUpload();
    if (!movieUploadResult) return false;

    await axios
      .post(`/api/drafts/create/${props.ihs}`, {
        diaryTemplate: diaryTemplate,
        requierNotice: isRequireNotice.value, // 日報作成時のみ必読投稿フラグを付与
      })
      .then(() => {
        router.push({ name: 'Home' });
      })
      .catch((err) => {
        const result = axiosErrorHandling(err);
        errorToast(result.code, result.message);
      });
  });
};

const submitPutReport = async () => {
  await asyncMethodLock(async () => {
    const imageUploadResult = await imageUpload();
    if (!imageUploadResult) return false;

    const movieUploadResult = await movieUpload();
    if (!movieUploadResult) return false;

    await axios
      .put(`/api/diary/updateDiary/${props.id}`, diaryTemplate)
      .then(() => {
        router.push({ name: 'Home' });
      })
      .catch((err) => {
        const result = axiosErrorHandling(err);
        errorToast(result.code, result.message);
      });
    // 独自実行なのでawaitしなくていい
    s3ImageDelete();
    ulizaMovieDelete();
  });
};

const s3ImageDelete = async () => {
  if (deleteImageList.length < 1) return false;
  // bodyにデータを入れたいのでpostで送信
  await axios.post('/api/media/image/delete/', { list: deleteImageList });
};

const ulizaMovieDelete = async () => {
  if (movieDeleteList.length < 1) return false;
  // bodyにデータを入れたいのでpostで送信
  await axios.post('/api/media/movie/delete', { list: movieDeleteList });
};

const imageUpload = async (): Promise<boolean> => {
  for (let i = 0; i < diaryTemplate.contents.length; i++) {
    const target = diaryTemplate.contents[i];
    // pathのタイプがファイルに変わった時点でファイル変更があったので、stringでなければファイル変更とみなしアップロード処理を行う
    if (
      target.contentType === REPORT_CONTENT_TYPE.IMAGE &&
      typeof target.path != 'string'
    ) {
      const result = await s3Upload(target.path as File);
      if (result.code === 200) {
        target.path = result.message;
      } else {
        errorToast(result.code, result.message);
        return false;
      }
    }
  }
  return true;
};

const movieUpload = async (): Promise<boolean> => {
  // 動画アップロード
  for (let i = 0; i < diaryTemplate.contents.length; i++) {
    const target = diaryTemplate.contents[i];
    // pathのタイプがファイルに変わった時点でファイル変更があったので、stringでなければファイル変更とみなしアップロード処理を行う
    if (
      target.contentType === REPORT_CONTENT_TYPE.MOVIE &&
      typeof target.path != 'string'
    ) {
      const result = await ulizaUpload(target.path as File);
      if (result.code === 200) {
        target.path = result.message;
      } else {
        errorToast(result.code, result.message);
        return false;
      }
    }
  }
  return true;
};

const s3Upload = async (file: File): Promise<AxiosErrorType> => {
  const formData = new FormData();
  formData.append('file', file);
  let result: AxiosErrorType = { code: 0, message: '' };
  await axios
    .post('/api/media/image/upload', formData)
    .then((res) => {
      result.code = 200;
      result.message = res.data;
    })
    .catch((err) => {
      result = axiosErrorHandling(err);
    });
  return result;
};

const ulizaUpload = async (file: File): Promise<AxiosErrorType> => {
  const formData = new FormData();
  formData.append('file', file);
  let result: AxiosErrorType = { code: 0, message: '' };
  await axios
    .post('/api/media/movie/upload', formData)
    .then((res) => {
      result.code = 200;
      result.message = res.data;
    })
    .catch((err) => {
      result = axiosErrorHandling(err);
    });
  return result;
};

const getLastReport = () => {
  axios
    .get(`/api/diary/latestDairy/${props.ihs}`)
    .then((res) => {
      if (res.data.diary?.contents) {
        const resContents = res.data.diary.contents;
        diaryTemplate.contents.forEach((c) => {
          let resFound = resContents.find((r: any) => {
            return r.title == c.title;
          });
          if (resFound && resFound.contentType === 'choice') {
            c.checked = [];
            c.checked?.push(...resFound.checked);
          } else if (resFound) {
            c.content = resFound.content;
          }
        });
      }
    })
    .catch((err) => {
      const result = axiosErrorHandling(err);
      errorToast(result.code, result.message);
    });
};
const confirmReport = () => {
  requireMessage.value = [];

  diaryTemplate.contents.map((s) => {
    console.log(s.time);

    if (s.contentType === REPORT_CONTENT_TYPE.DATETIME) {
      let setTime = s.time as Date;
      let dayOfWeeks = setTime.getDay(); // 曜日(数値)
      let dayOfWeekStrs = ['日', '月', '火', '水', '木', '金', '土'][
        dayOfWeeks
      ];
      let years = setTime.getFullYear();
      let months = setTime.getMonth();
      let dates = setTime.getDate();

      if (s.contentType == REPORT_CONTENT_TYPE.DATETIME) {
        s.content =
          String(years) +
          '/' +
          String(months + 1) +
          '/' +
          String(dates) +
          '(' +
          dayOfWeekStrs +
          ')';
      }
    }

    return s;
  });

  for (let i = 0; i < diaryTemplate.contents.length; i++) {
    if (
      diaryTemplate.contents[i].require == true &&
      diaryTemplate.contents[i].content == '' &&
      diaryTemplate.contents[i]?.contentType !== REPORT_CONTENT_TYPE.CHOICE
    ) {
      requireMessage.value.push(
        diaryTemplate.contents[i].title + 'の入力が必須です。'
      );
    } else if (
      diaryTemplate.contents[i].require == true && // 複数選択式の条件チェック
      diaryTemplate.contents[i]?.contentType === REPORT_CONTENT_TYPE.CHOICE &&
      diaryTemplate.contents[i]?.checked &&
      diaryTemplate.contents[i]?.checked?.length === 0
    ) {
      requireMessage.value.push(
        diaryTemplate.contents[i].title + 'の選択が必須です。'
      );
    }
  }

  if (requireMessage.value.length !== 0) {
    scrollTop();
    return false;
  }

  reportPageMode.value = MODE.CONFIRM;
  scrollTop();
};

const editReport = () => {
  reportPageMode.value = MODE.EDIT;
};

const scrollTop = () => {
  window.scroll({ top: 0, behavior: 'smooth' });
};
</script>

<style scoped></style>

<style>
/* html {
  background-color: rgb(230, 253, 252);
} */
</style>
