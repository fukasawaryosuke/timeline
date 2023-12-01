<script setup lang="ts">
import { computed } from 'vue';
import { AccountData } from '/@/js/models/admin/Account';
import { OrganizationTreeListModel } from '/@/js/models/Organization';
import OrganizationModal from '../modal/OrganizationModal.vue';
import ResetPasswordModal from '../modal/ResetPasswordModal.vue';
import { Modal } from '/@/js/utils/Modal';
import { Validator, Target } from '/@/js/utils/Validation';

const props = defineProps<{
  accountData: AccountData;
  organizationTreeList: OrganizationTreeListModel;
  originalIsActive?: number;
  isEdit: boolean;
  isHumanAcademy: boolean;
}>();

const emit = defineEmits(['put_account', 'post_account', 'reset_password']);

const accountData = computed(() => props.accountData);
const displayTitle = computed(() =>
  props.isEdit ? 'アカウント編集' : 'アカウント作成'
);
const displayButton = computed(() => (props.isEdit ? '更新' : '作成'));
const displayDivisionName = computed(() =>
  props.organizationTreeList.getOrganizationName(props.accountData.divisionCd)
);
const isInitialUser = computed(() => props.accountData.isInitialUser === 1);

//アカウント登録・更新
const processAccount = async (account_data: AccountData) => {
  const hasError = validation(account_data.userName, account_data.mailadd);
  if (hasError) return;

  const formData = createFormData(account_data);

  emit(props.isEdit ? 'put_account' : 'post_account', formData);
};

//バリデーション
const validation = (user_name: string, mailadd: string): boolean => {
  const isEmpty = (value: string): boolean => !value || value.trim() === '';

  const targets: Target[] = [
    {
      value: user_name,
      message: '氏名が入力されていません。',
      rule: isEmpty,
    },
    {
      value: mailadd,
      message: 'メールアドレスが入力されていません。',
      rule: isEmpty,
    },
  ];

  const validator = new Validator(targets);
  const hasError = validator.validate();

  return hasError;
};

//フォームデータ作成
const createFormData = (account_data: AccountData): FormData => {
  const formData = new FormData();
  formData.append('userName', account_data.userName ?? '');
  formData.append('mailadd', account_data.mailadd ?? '');
  formData.append('isActive', account_data.isActive.toString() ?? '');
  formData.append('divisionCd', account_data.divisionCd?.toString() ?? '');
  formData.append('systemRoleId', account_data.systemRoleId?.toString() ?? '');

  if (props.isEdit) {
    formData.append('userId', account_data.id?.toString() ?? '');
  }

  return formData;
};

//親課題の選択
const selectDivision = (division_cd: number) => {
  accountData.value.divisionCd = division_cd;
};

const deleteDivision = () => {
  accountData.value.divisionCd = null;
};

const resetPassword = () => {
  emit('reset_password', accountData.value.id);
};

// モーダル
const organizationModal = new Modal('部門選択モーダル');
const resetPasswordModal = new Modal(
  'リセットパスワードモーダル',
  'パスワードをリセットします。本当にリセットしますか？'
);
</script>

<template>
  <div class="content">
    <div class="flex-content">
      <h1>{{ displayTitle }}</h1>
      <div class="inner_content">
        <div class="main_header flex_end">
          <button class="yellowbtn btn" @click="processAccount(accountData)">
            <i class="fa fa-floppy-o fa_blank" aria-hidden="true"></i>
            {{ displayButton }}
          </button>
        </div>

        <div class="main_area w-[400px]">
          <div class="mb-8">
            <label>
              <p>
                氏名
                <span class="redtxt smalltxt">（必須）</span>
              </p>
              <input v-model="accountData.userName" type="text" />
            </label>
          </div>

          <div class="mb-8">
            <label>
              <p>
                メールアドレス
                <span class="redtxt smalltxt">（必須）</span>
              </p>
              <input v-model="accountData.mailadd" type="text" />
            </label>
          </div>

          <div class="mb-8">
            <div class="font-bold">
              <p>所属部門</p>
              <button
                class="org_button w-[100px]"
                @click="organizationModal.open()"
              >
                組織選択
              </button>
            </div>
            <div v-if="displayDivisionName" class="selectOrgArea">
              <div class="org_ul">
                <div class="org_list">
                  {{ displayDivisionName }}
                  <fa
                    icon="fa-xmark"
                    class="xmark"
                    :value="accountData.divisionCd"
                    @click="deleteDivision()"
                  />
                </div>
              </div>
            </div>
          </div>

          <div class="mb-8">
            <p class="font-bold">ステータス</p>
            <div class="flex gap-4">
              <label class="flex gap-2">
                <input
                  v-model="accountData.isActive"
                  value="1"
                  type="radio"
                  :disabled="isInitialUser"
                />
                <p>有効</p>
              </label>
              <label class="flex gap-2">
                <input
                  v-model="accountData.isActive"
                  value="0"
                  type="radio"
                  :disabled="isInitialUser"
                />
                <p>無効</p>
              </label>
            </div>
          </div>

          <div class="mb-8">
            <p class="font-bold">管理画面アクセス</p>
            <div class="flex gap-4">
              <label>
                <input
                  v-model="accountData.systemRoleId"
                  class="cursor-pointer"
                  type="checkbox"
                  true-value="2"
                  false-value="0"
                  :disabled="isInitialUser"
                />
              </label>
            </div>
          </div>

          <div v-if="props.isEdit && !props.isHumanAcademy" class="mb-8">
            <button class="yellowbtn btn" @click="resetPasswordModal.open()">
              <i aria-hidden="true"></i>
              パスワードをリセット
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <OrganizationModal
    :orgz-tree-list="props.organizationTreeList"
    :orgz-modal-open="organizationModal.isOpen.value"
    @close="organizationModal.close()"
    @select="selectDivision"
  />

  <ResetPasswordModal
    :is-open="resetPasswordModal.isOpen.value"
    :message="resetPasswordModal.message"
    :confirm-label="'リセット'"
    @close_modal="resetPasswordModal.close()"
    @click_right_button="resetPassword"
  />
</template>

<style>
.org_button {
  color: #06b6d4;
  border: 1px solid #06b6d4;
  padding: 4px 17px;
  border-radius: 30px;
  background-color: white;
  margin-right: 15px;
}

.org_ul {
  display: flex;
  flex-wrap: wrap;
}

.org_list {
  border: 1px solid #00b0e3;
  border-radius: 45px;
  font-weight: initial;
  padding: 4px 14px;
  background: #00b0e3;
  color: white;
  padding-left: 0.75rem;
  padding-right: 0.75rem;
  padding-top: 0.25rem;
  padding-bottom: 0.25rem;
  margin-left: 0.25rem;
  margin-top: 0.75rem;
  margin-bottom: 0.75rem;
  cursor: default;
}
.xmark {
  margin-left: 0.5rem;
  cursor: pointer;
}
.selectOrgArea {
  width: auto;
  margin-top: 5px;
}
</style>
