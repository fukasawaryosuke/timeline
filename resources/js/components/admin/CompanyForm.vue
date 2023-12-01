<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import SideMenu from '/@/js/components/admin/SideMenu.vue';
import CompanyEdit from '/@/js/components/admin/company/CompanyEdit.vue';
import { CompanyService } from '/@/js/utils/CompanyService';

const route = useRoute();
const router = useRouter();

const companyId = route.params.id;
const isEdit = ref<boolean>(companyId ? true : false);

const companyService = new CompanyService();

onMounted(async () => {
  if (isEdit.value) {
    await getCompany(companyId);
  }
});

//法人取得
const getCompany = async (company_id: string | string[]): Promise<void> => {
  await companyService.getCompany(company_id).catch(toCompanyList);
};

//法人登録
const postCompany = async (formData: FormData): Promise<void> => {
  await companyService.postCompany(formData).then(toCompanyList);
};

//法人更新
const putCompany = async (formData: FormData): Promise<void> => {
  await companyService.putCompany(formData).then(toCompanyList);
};

//画面遷移
const toCompanyList = (): void => {
  router.push({ name: 'CompanyList' });
};

//画像アップロード
const uploadFile = (file: File): void => {
  companyService.companyData.iconFile = file;
  companyService.companyData.companyIconPath = URL.createObjectURL(file);
};

//画像削除
const deleteFile = (): void => {
  companyService.companyData.iconFile = null;
  companyService.companyData.companyIconPath = '';
};
</script>

<template>
  <div class="main">
    <SideMenu />
    <CompanyEdit
      :company-data="companyService.companyData"
      :is-edit="isEdit"
      @put_company="putCompany"
      @post_company="postCompany"
      @upload_file="uploadFile"
      @delete_file="deleteFile"
    />
  </div>
</template>
