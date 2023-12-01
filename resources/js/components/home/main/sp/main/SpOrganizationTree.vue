<template>
  <div>
    <div class="single_article">
      <h3>
        {{ getLevelLavel(parentLevel) }}
      </h3>
      <div
        v-if="getSelectedOrganization(orgzTreeList)"
        class="org_filter_btn filter_active"
        @click="select(0)"
      >
        {{ getSelectedOrganization(orgzTreeList)?.organization_name }}
      </div>
      <template
        v-if="!getSelectedOrganization(orgzTreeList)"
        class="mb-4 p-2 border border-gray-400 rounded-lg"
      >
        <div
          v-for="orgzTree in orgzTreeList.list"
          class="org_filter_btn"
          @click="select(orgzTree.division_cd)"
        >
          {{ orgzTree.organization_name }}
        </div>
      </template>
    </div>
    <SpOrganizationTree
      v-if="hasUnderOrganizations(orgzTreeList)"
      :parent-level="getSelectedOrganization(orgzTreeList)?.level"
      :orgz-tree-list="getUnderOrganizations(orgzTreeList)"
      :changed="transDivisionCode"
    />
  </div>
</template>
<script lang="ts" setup>
import { OrganizationTreeListModel } from '/@/js/models/Organization';
import useOrganization from '/@/js/compositions/UseOrganization';
import SpOrganizationTree from '/@/js/components/home/main/sp/main/SpOrganizationTree.vue';

interface Props {
  parentLevel?: number;
  orgzTreeList: OrganizationTreeListModel;
  changed: (divCd: number) => void;
}
const props = defineProps<Props>();
const {
  select,
  transDivisionCode,
  isSelected,
  getSelectedOrganization,
  hasUnderOrganizations,
  getUnderOrganizations,
  getLevelLavel,
} = useOrganization(props.changed);
</script>
<style scoped></style>
