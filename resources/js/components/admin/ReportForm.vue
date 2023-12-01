<script setup lang="ts">
import { ref } from 'vue';
import useReportForm from '/@/js/compositions/UseReportForm';
import SideMenu from '/@/js/components/admin/SideMenu.vue';
import FormAdd from '/@/js/components/admin/report/FormAdd.vue';
import FormEdit from '/@/js/components/admin/report/FormEdit.vue';
// import FormConfirm from '/@/js/components/admin/report/FormConfirm.vue';
import FormPreview from '/@/js/components/admin/report/FormPreview.vue';
import ConfirmModal from '/@/js/components/modal/ConfirmModal.vue';
import { IReportForm } from '/@/js/models/admin/ReportForm';

/** preview data */

interface Props {
  id?: string;
  form: IReportForm;
  edit: boolean;
  firstForm?: string;
}
const props = defineProps<Props>();
const {
  REPORT_FORM_TYPE,
  mode,
  formData,
  dummyData,
  updateName,
  updateRequired,
  updateContentsType,
  updateContentsRequired,
  updateContentsReportVisible,
  updateTitle,
  addContents,
  confirmForm,
  previewMode,
  deleteContents,
  deleteForm,
  modeStr,
  createOrUpdate,
  addChoice,
  updateChoice,
  deleteChoice,
  newContents,
} = useReportForm(props.form);

const deleteModalVisible = ref<boolean>(false);

const deleteModalOpen = () => {
  deleteModalVisible.value = !deleteModalVisible.value;
};
const deleteModalClose = () => {
  deleteModalVisible.value = false;
};
</script>

<template>
  <ConfirmModal
    :modal-open="deleteModalVisible"
    confirm-label="削除"
    message="投稿フォームを削除します。よろしいですか？"
    :sub-message="`このフォームを利用しているカテゴリは${firstForm}のフォームに変更されます。`"
    @event="deleteForm(id)"
    @close="deleteModalClose"
  />
  <div class="main">
    <SideMenu />
    <div class="content">
      <div class="flex-content">
        <h1>投稿フォーム{{ modeStr(edit) }}</h1>
        <!-- <router-link to="/admin/report_form">戻る</router-link> -->
        <div class="inner_content">
          <div class="main_header flex_end">
            <button class="yellowbtn btn" @click="createOrUpdate(edit, id)">
              <i class="fa fa-floppy-o fa_blank" aria-hidden="true"></i>
              {{ modeStr(edit) }}
            </button>
          </div>
          <!-- <template
            v-if="
              mode === REPORT_FORM_TYPE.EDIT ||
              mode === REPORT_FORM_TYPE.PREVIEW
            "
          > -->
          <div class="main_area">
            <div class="flex_2col">
              <FormAdd
                v-model:new-contents="newContents"
                :data="formData"
                :edit="edit"
                @add-contents="addContents"
                @update-name="updateName"
                @update-required="updateRequired"
                @update-title="updateTitle"
                @delete-contents="deleteContents"
                @selected="updateContentsType"
                @update-contents-required="updateContentsRequired"
                @confirm="confirmForm"
                @delete="deleteModalOpen"
                @add-choice="addChoice"
                @update-choice="updateChoice"
                @delete-choice="deleteChoice"
                @mode-change="previewMode"
              />
              <div class="col2_50 preview_area">
                <FormEdit
                  v-if="mode === REPORT_FORM_TYPE.EDIT"
                  :data="formData"
                  :edit="edit"
                  @add-contents="addContents"
                  @update-name="updateName"
                  @update-required="updateRequired"
                  @update-title="updateTitle"
                  @delete-contents="deleteContents"
                  @selected="updateContentsType"
                  @update-contents-required="updateContentsRequired"
                  @update-contents-report-visible="updateContentsReportVisible"
                  @add-choice="addChoice"
                  @update-choice="updateChoice"
                  @delete-choice="deleteChoice"
                />
                <FormPreview
                  v-if="mode === REPORT_FORM_TYPE.PREVIEW"
                  :data="dummyData"
                />
              </div>
            </div>
          </div>
          <!-- </template> -->
          <!-- <FormConfirm
            v-else
            :data="formData"
            :btn-string="modeStr(edit)"
            @return="returnEditForm"
            @update="createOrUpdate(edit, id)"
          /> -->
        </div>
      </div>
    </div>
  </div>
</template>
