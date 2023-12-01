<script setup lang="ts">
import { ref, reactive, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useToast } from 'vue-toastification';
import axios from 'axios';
import useAxios from '/@/js/compositions/UseAxios';
import SideMenu from '/@/js/components/admin/SideMenu.vue';
import IhsEdit from '/@/js/components/admin/ihs/IhsEdit.vue';
import FormPreview from '/@/js/components/admin/report/FormPreview.vue';

import IhsConfirm from '/@/js/components/admin/ihs/IhsConfirm.vue';
import { IIhsForm } from '/@/js/models/admin/IhsForm';

interface Props {
  id?: string;
  ihs: IIhsForm;
  formList: any[];
  edit: boolean;
}
const props = defineProps<Props>();

const router = useRouter();
const toast = useToast();
const { asyncMethodLock, axiosErrorHandling, successToast, errorToast } =
  useAxios();

const iconFile = ref<File | null>(null);

/** state */
const ihsData = reactive<IIhsForm>(props.ihs);
const IHS_MODE_TYPE = {
  EDIT: 0,
  PREVIEW: 1,
  CONFIRM: 2,
};
const mode = ref<number>(IHS_MODE_TYPE.EDIT);
// 作成の場合は初期値として投稿フォームリストの一番目を設定する
ihsData.report_form_id = ihsData.report_form_id || props.formList[0]?.id;
const previewForm = computed(() => {
  return props.formList.filter((f) => f.id == ihsData.report_form_id)[0];
});

/** method */
const displayPreview = (e: any) => {
  if (e.target.checked) {
    if (ihsData.report_form_id) {
      mode.value = IHS_MODE_TYPE.PREVIEW;
    } else {
      e.target.checked = false;
      toast.error('投稿フォームを選択してください。');
    }
  } else {
    mode.value = IHS_MODE_TYPE.EDIT;
  }
};

const confirmIhs = () => {
  // NOTE: 非公開(下書き)の場合はバリデーションしない
  const [result, message] = ihsValidation();
  if (ihsData.release_status == 1 && result) {
    toast.error(message);
  } else {
    mode.value = IHS_MODE_TYPE.CONFIRM;
  }
};

const changeForm = (e) => {
  ihsData.report_form_id = e.target.value;
};

const updateIhsData = (e: any, key: string) => {
  ihsData[key] = e.target.value;
};

const uploadFile = (e: any, accept: RegExp) => {
  const fileList = e.target.files;
  if (fileList.length <= 0) return false;
  const selectFile: File = e.target.files[0];
  if (!accept.test(selectFile.type)) {
    e.currentTarget.value = '';
    deleteFile();
    toast.error('ファイル形式が違います');
  } else {
    const file = e.target.files[0];
    iconFile.value = file;
    ihsData.icon_path = URL.createObjectURL(file);
  }
};

const deleteFile = () => {
  iconFile.value = null;
  ihsData.icon_path = '';
};

const updateStatus = (value: number) => {
  ihsData.release_status = value;
};

const ihsValidation = (): [boolean, string] => {
  let hasError = false;
  const message: string[] = [];
  // 制度名がない
  if (!ihsData.system_name || ihsData.system_name.trim() == '') {
    hasError = true;
    message.push('カテゴリが入力されていません。');
  }
  // 説明がない
  // if (!ihsData.description || ihsData.description.trim() == '') hasError = true;
  // 投稿フォームが選択されていない 下記の記述の場合、0(選択していない)はfalseとなり、!0でtrueとして扱われる
  if (!ihsData.report_form_id) {
    hasError = true;
    message.push('投稿フォームが選択されていません。');
  }
  const errorMessage = message.join('\n');
  return [hasError, errorMessage];
};

const createOrUpdate = () => {
  const [result, message] = ihsValidation();
  if (ihsData.release_status == 1 && result) {
    toast.error(message);
    return false;
  }
  if (props.edit) {
    updateIhs(props.id);
  } else {
    createIhs();
  }
};

const createFormData = (): FormData => {
  const formData = new FormData();
  formData.append('system_name', ihsData.system_name ?? '');
  formData.append('icon_path', ihsData.icon_path ?? '');
  formData.append('description', ihsData.description ?? '');
  formData.append('release_status', String(ihsData.release_status));
  formData.append('report_form_id', String(ihsData.report_form_id));
  formData.append('organization_div', JSON.stringify(ihsData.div_cd));
  if (iconFile.value) {
    formData.append('icon_file', iconFile.value);
  }
  return formData;
};

// 制度の作成
const createIhs = async () => {
  await asyncMethodLock(async () => {
    const formData = createFormData();
    await axios
      .post('/api/ihs/create', formData)
      .then(() => {
        successToast(200, 'カテゴリの作成に成功しました。');
        gotoIhsList();
      })
      .catch((err) => {
        const result = axiosErrorHandling(err);
        errorToast(result.code, result.message);
      });
  });
};
// 制度の更新
const updateIhs = async (id?: string) => {
  await asyncMethodLock(async () => {
    const formData = createFormData();
    // NOTE: 一度postで送信し、X-HTTP-Method-OverrideでPUTに上書きしないとLaravel側でデータがnullになる
    await axios
      .post(`/api/ihs/${id}`, formData, {
        headers: {
          'Content-type': 'multipart/form-data',
          'X-HTTP-Method-Override': 'PUT',
        },
      })
      .then(() => {
        successToast(200, 'カテゴリの更新に成功しました。');
        gotoIhsList();
      })
      .catch((err) => {
        const result = axiosErrorHandling(err);
        errorToast(result.code, result.message);
      });
  });
};
// 制度の削除
const deleteIhs = async (id?: string) => {
  await asyncMethodLock(async () => {
    await axios
      .delete(`/api/ihs/${id}`)
      .then(() => {
        successToast(200, 'カテゴリの削除に成功しました。');
        gotoIhsList();
      })
      .catch((err) => {
        const result = axiosErrorHandling(err);
        errorToast(result.code, result.message);
      });
  });
};

const gotoIhsList = () => {
  router.push({
    name: 'InHouseSystemList',
  });
};

/** computed */
const modeStr = computed(() => {
  return props.edit ? '更新' : '作成';
});
</script>

<template>
  <div class="modal">モーダル</div>
  <div class="main">
    <SideMenu />
    <div class="content">
      <div class="flex-content">
        <h1>カテゴリ{{ modeStr }}</h1>
        <!-- <router-link to="/admin/in_house_system">戻る</router-link> -->
        <div class="inner_content">
          <div class="main_header flex_end">
            <button class="yellowbtn btn" @click="createOrUpdate">
              <i class="fa fa-floppy-o fa_blank" aria-hidden="true"></i>
              {{ modeStr }}
            </button>
          </div>

          <div v-if="props.formList.length === 0" class="main_area">
            投稿フォームが登録されていません。
            <br />
            先に投稿フォームを作成してください。
          </div>

          <div v-else class="main_area">
            <div class="flex_2col">
              <template
                v-if="
                  mode === IHS_MODE_TYPE.EDIT || mode === IHS_MODE_TYPE.PREVIEW
                "
              >
                <IhsEdit
                  :ihs="ihsData"
                  :form-list="formList"
                  :edit="edit"
                  :btn-str="modeStr"
                  @change-form="changeForm"
                  @update-ihs-data="updateIhsData"
                  @update-status="updateStatus"
                  @upload-file="uploadFile($event, /image\/*/)"
                  @delete-file="deleteFile"
                  @confirm-ihs="confirmIhs"
                  @delete-ihs="deleteIhs(id)"
                />
                <FormPreview :data="previewForm" />
              </template>
              <!-- <IhsConfirm
              v-else
              :ihs="ihsData"
              :form-list="formList"
              :file-name="iconFile?.name ?? ''"
              :btn-string="modeStr"
              @return="mode = IHS_MODE_TYPE.EDIT"
              @update="createOrUpdate"
            /> -->
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
