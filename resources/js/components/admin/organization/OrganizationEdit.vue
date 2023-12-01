<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { OrganizationData } from '/@/js/models/admin/Organization';
import { OrganizationTreeListModel } from '/@/js/models/Organization';
import OrganizationModal from '../modal/OrganizationModal.vue';
import ConfirmModal from '../modal/ConfirmModal.vue';
import { Modal } from '/@/js/utils/Modal';
import { Validator, Target } from '/@/js/utils/Validation';

const props = defineProps<{
  organizationId?: number;
  organizationData: OrganizationData;
  organizationTreeList: OrganizationTreeListModel;
  originalIsActive?: number;
  isEdit: boolean;
}>();

const emit = defineEmits(['put_organization', 'post_organization']);

const organizationData = computed(() => props.organizationData);
const displayTitle = computed(() => (props.isEdit ? '部門編集' : '部門作成'));
const displayButton = computed(() => (props.isEdit ? '更新' : '作成'));
const displayParentDivision = computed(() =>
  props.organizationTreeList.getOrganizationName(
    organizationData.value.parentDivisionCd
  )
);

//部門登録・更新
const processOrganization = async (organization_data: OrganizationData) => {
  //バリデーション
  const hasError = validation(organization_data);
  if (hasError) return;

  //整合性チェック
  const isChangedStatus =
    props.originalIsActive !== organizationData.value.isActive;
  const hasChildren = props.organizationTreeList.hasChildren(
    organizationData.value.divisionCd
  );
  if (hasChildren && isChangedStatus) {
    await checkIntegrity();
    if (!isConfirmed.value) return;
  }

  //フォームデータ作成
  const formData = createFormData(organization_data);

  emit(props.isEdit ? 'put_organization' : 'post_organization', formData);
};

//バリデーション
const validation = (organization_data: OrganizationData): boolean => {
  const isEmpty = (value: string): boolean => !value || value.trim() === '';

  const targets: Target[] = [
    {
      value: organization_data.organizationName,
      message: '部門名が入力されていません。',
      rule: isEmpty,
    },
  ];

  const validator = new Validator(targets);
  const hasError = validator.validate();

  return hasError;
};

//整合性チェック
const isConfirmed = ref<boolean>(false);
const checkIntegrity = (): Promise<void> => {
  return new Promise((resolve) => {
    confirmModal.open();

    const unwatch = watch(
      () => confirmModal.isOpen.value,
      (newVal) => {
        if (!newVal) {
          unwatch();
          resolve();
        }
      }
    );
  });
};

//フォームデータ作成
const createFormData = (organization_data: OrganizationData): FormData => {
  const formData = new FormData();
  formData.append('organizationName', organization_data.organizationName ?? '');
  formData.append('isActive', organization_data.isActive.toString() ?? '');
  formData.append(
    'parentDivisionCd',
    organizationData.value.parentDivisionCd?.toString() ?? ''
  );

  if (props.isEdit) {
    formData.append('organizationId', organization_data.id?.toString() ?? '');
  }

  return formData;
};

//親課題の選択
const selectParentDivision = (division_cd: number) => {
  organizationData.value.parentDivisionCd =
    division_cd == 0 ? null : division_cd;
};

const deleteParentDivision = () => {
  organizationData.value.parentDivisionCd = null;
};

// モーダル
const organizationModal = new Modal('部門選択モーダル');
const confirmModal = new Modal(
  '確認モーダル',
  'この部門に紐づく全ての子部門のステータスも変更されます。本当に変更しますか？'
);

const confirm = (is_confirmed: boolean) => {
  isConfirmed.value = is_confirmed;
  confirmModal.close();
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
            @click="processOrganization(organizationData)"
          >
            <i class="fa fa-floppy-o fa_blank" aria-hidden="true"></i>
            {{ displayButton }}
          </button>
        </div>

        <div class="main_area w-[400px]">
          <div v-if="props.isEdit" class="mb-8">
            <label>
              <p>部門コード</p>
              <p>{{ organizationData.divisionCd }}</p>
            </label>
          </div>

          <div class="mb-8">
            <label>
              <p>
                部門名
                <span class="redtxt smalltxt">（必須）</span>
              </p>
              <input v-model="organizationData.organizationName" type="text" />
            </label>
          </div>

          <div class="mb-8">
            <div class="font-bold">
              <p>親部門</p>
              <button
                class="org_button w-[100px]"
                @click="organizationModal.open()"
              >
                組織選択
              </button>
            </div>
            <div class="selectOrgArea">
              <div class="org_ul">
                <div v-if="displayParentDivision" class="org_list">
                  {{ displayParentDivision }}
                  <fa
                    icon="fa-xmark"
                    class="xmark"
                    :value="organizationData.parentDivisionCd"
                    @click="deleteParentDivision()"
                  />
                </div>
                <div v-else class="org_list">未選択</div>
              </div>
            </div>
          </div>

          <div>
            <p class="font-bold">ステータス</p>
            <div class="flex gap-4">
              <label class="flex gap-2">
                <input
                  v-model="organizationData.isActive"
                  value="1"
                  type="radio"
                />
                <p>有効</p>
              </label>
              <label class="flex gap-2">
                <input
                  v-model="organizationData.isActive"
                  value="0"
                  type="radio"
                />
                <p>無効</p>
              </label>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <OrganizationModal
    :organization-id="props.organizationId"
    :orgz-tree-list="props.organizationTreeList"
    :orgz-modal-open="organizationModal.isOpen.value"
    @close="organizationModal.close()"
    @select="selectParentDivision"
  />

  <ConfirmModal
    :is-open="confirmModal.isOpen.value"
    :message="confirmModal.message"
    :confirm-label="'変更する'"
    @close_modal="confirmModal.close()"
    @confirm="confirm"
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
