<script setup lang="ts">
import { ref, computed } from 'vue';
import { CompanyData } from '/@/js/models/admin/Company';
import { useToast } from 'vue-toastification';
import { Validator, Target } from '/@/js/utils/Validation';

const toast = useToast();

const props = defineProps<{
  companyData: CompanyData;
  isEdit: boolean;
}>();

const emit = defineEmits([
  'put_company',
  'post_company',
  'upload_file',
  'delete_file',
]);

interface InitialUser {
  userName: string;
  mailadd: string;
}

const initialUser = ref<InitialUser>({
  userName: '',
  mailadd: '',
});

const companyData = computed(() => props.companyData);
const displayTitle = computed(() => (props.isEdit ? '法人編集' : '法人作成'));
const displayButton = computed(() => (props.isEdit ? '更新' : '作成'));

//法人登録・更新
const processCompany = (
  company_data: CompanyData,
  initial_user: InitialUser
): void => {
  const hasError = validation(company_data, initial_user);
  if (hasError) return;

  const formData = createFormData(company_data, initial_user);

  emit(props.isEdit ? 'put_company' : 'post_company', formData);
};

//バリデーション
const validation = (
  company_data: CompanyData,
  initial_user: InitialUser
): boolean => {
  const isEmpty = (value: string): boolean => !value || value.trim() === '';

  const targets: Target[] = [
    {
      value: company_data.companyName,
      message: '法人名が入力されていません。',
      rule: isEmpty,
    },
  ];

  if (!props.isEdit) {
    targets.push(
      {
        value: initial_user.userName,
        message: '氏名が入力されていません。',
        rule: isEmpty,
      },
      {
        value: initial_user.mailadd,
        message: 'メールアドレスが入力されていません。',
        rule: isEmpty,
      }
    );
  }

  const validator = new Validator(targets);
  const hasError = validator.validate();

  return hasError;
};

//フォームデータ作成
const createFormData = (
  company_data: CompanyData,
  initial_user: InitialUser
): FormData => {
  const formData = new FormData();
  formData.append('companyName', company_data.companyName ?? '');
  formData.append('companyIconPath', company_data.companyIconPath ?? '');
  formData.append('iconFile', company_data.iconFile ?? '');
  formData.append('isActive', company_data.isActive.toString() ?? '');

  if (props.isEdit) {
    formData.append('companyId', company_data.id?.toString() ?? '');
  }

  if (!props.isEdit) {
    formData.append('userName', initial_user.userName ?? '');
    formData.append('mailadd', initial_user.mailadd ?? '');
  }

  return formData;
};

//画像選択
const clickInputFile = (): void => {
  document.getElementById('inputfile')?.click();
};

//画像アップロード
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
    emit('upload_file', file);
  }
};

//画像削除
const deleteFile = (): void => {
  emit('delete_file');
};
</script>

<template>
  <div class="content">
    <div class="flex-content">
      <h1>{{ displayTitle }}</h1>
      <div class="inner_content">
        <div class="main_header flex_end">
          <button
            class="yellowbtn btn"
            @click="processCompany(companyData, initialUser)"
          >
            <i class="fa fa-floppy-o fa_blank" aria-hidden="true"></i>
            {{ displayButton }}
          </button>
        </div>

        <div class="main_area w-[400px]">
          <div v-if="props.isEdit" class="mb-8">
            <label>
              <p>法人コード</p>
              <p>{{ companyData.companyCode }}</p>
            </label>
          </div>

          <div class="mb-8">
            <label>
              <p>
                法人名
                <span class="redtxt smalltxt">（必須）</span>
              </p>
              <input v-model="companyData.companyName" type="text" />
            </label>
          </div>

          <div class="mb-8">
            <label>
              <p>コーポレートアイコン</p>
              <button class="bluebtn btn" @click="clickInputFile">
                <i class="fa fa-file-image fa_blank" aria-hidden="true"></i>
                ファイルを選択
              </button>
              <input
                id="inputfile"
                accept="image/*"
                type="file"
                class="hidden"
                @change="uploadFile($event, /image\/*/)"
              />
            </label>
          </div>

          <div
            v-if="companyData.companyIconPath"
            class="flex items-center mb-8"
          >
            <img
              class="w-[60px] h-[60px] rounded-[50%] mr-2"
              :src="companyData.companyIconPath"
              alt="icon"
            />
            <i
              class="fa fa-trash cursor-pointer"
              aria-hidden="true"
              @click="deleteFile()"
            ></i>
          </div>

          <div class="mb-8">
            <p class="font-bold">ステータス</p>
            <div class="flex gap-4">
              <label class="flex gap-2">
                <input v-model="companyData.isActive" value="1" type="radio" />
                <p>有効</p>
              </label>
              <label class="flex gap-2">
                <input v-model="companyData.isActive" value="0" type="radio" />
                <p>無効</p>
              </label>
            </div>
          </div>

          <div v-if="!props.isEdit" class="mb-2">
            <p class="font-bold">初期ユーザー</p>
          </div>

          <div v-if="!props.isEdit" class="mb-8">
            <label>
              <p>
                氏名
                <span class="redtxt smalltxt">（必須）</span>
              </p>
              <input v-model="initialUser.userName" type="text" />
            </label>
          </div>

          <div v-if="!props.isEdit" class="mb-8">
            <label>
              <p>
                メールアドレス
                <span class="redtxt smalltxt">（必須）</span>
              </p>
              <input v-model="initialUser.mailadd" type="text" />
            </label>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
