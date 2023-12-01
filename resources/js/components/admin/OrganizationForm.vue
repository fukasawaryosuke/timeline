<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import SideMenu from '/@/js/components/admin/SideMenu.vue';
import OrganizationEdit from '/@/js/components/admin/organization/OrganizationEdit.vue';
import { OrganizationTreeListModel } from '/@/js/models/Organization';
import { OrganizationService } from '/@/js/utils/OrganizationService';

const route = useRoute();
const router = useRouter();

const organizationId = Number(route.params.id);
const isEdit = ref<boolean>(organizationId ? true : false);

const organizationService = new OrganizationService();

const originalIsActive = ref<number>(0);

//NOTE:既存の実装を流用
const organizationTreeList = reactive<OrganizationTreeListModel>(
  new OrganizationTreeListModel()
);

onMounted(async () => {
  if (isEdit.value) {
    await getOrganization(organizationId);
  }

  await getOrganizationTreeList();
});

//部門取得
const getOrganization = async (organization_id: number): Promise<void> => {
  await organizationService
    .getOrganization(organization_id)
    .then(
      () =>
        (originalIsActive.value = organizationService.organizationData.isActive)
    )
    .catch(toOrganizationList);
};

//部門登録
const postOrganization = async (formData: FormData): Promise<void> => {
  await organizationService.postOrganization(formData).then(toOrganizationList);
};

//部門更新
const putOrganization = async (formData: FormData): Promise<void> => {
  await organizationService.putOrganization(formData).then(toOrganizationList);
};

//部門ツリー取得
const getOrganizationTreeList = async (): Promise<void> => {
  await organizationService
    .getOrganizationTreeList()
    .then(() =>
      organizationTreeList.init(organizationService.organizationTreeList)
    );
};

//画面遷移
const toOrganizationList = (): void => {
  router.push({ name: 'OrganizationList' });
};
</script>

<template>
  <div class="main">
    <SideMenu />
    <OrganizationEdit
      :organization-id="organizationId"
      :organization-data="organizationService.organizationData"
      :organization-tree-list="organizationTreeList"
      :original-is-active="originalIsActive"
      :is-edit="isEdit"
      @put_organization="putOrganization"
      @post_organization="postOrganization"
    />
  </div>
</template>
