<template>
  <div>
    <div class="mt-8">
      <span class="text-slate-600 font-semibold">
        {{ getLevelLavel(parentLevel) }}
      </span>
      <span
        v-if="getSelectedOrganization(orgzTreeList)"
        class="bg-cyan-500 text-white border border-cyan-500 rounded-2xl px-3 py-1 ml-3 cursor-pointer"
        @click="select(0)"
      >
        {{ getSelectedOrganization(orgzTreeList)?.organization_name }}
      </span>
    </div>
    <div
      v-if="!getSelectedOrganization(orgzTreeList)"
      class="mb-4 p-2 border border-gray-400 rounded-lg"
    >
      <ul class="flex flex-wrap">
        <li
          class="px-3 py-1 ml-1 first:ml-0 mt-3 rounded-2xl self-center flex-shrink-0 cursor-pointer bg-white border border-gray-400"
          v-for="orgzTree in orgzTreeList.list"
          @click="select(orgzTree.division_cd)"
        >
          {{ orgzTree.organization_name }}
        </li>
      </ul>
    </div>
    <OrganizationTree
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
