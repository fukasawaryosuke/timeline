<template>
  <div>
    <div class="levelArea">
      <span class="level">
        {{ getLevelLavel(parentLevel) }}
      </span>
      <span
        v-if="getSelectedOrganization(orgzTreeList)"
        class="selectOrg"
        @click="select(0)"
      >
        {{ getSelectedOrganization(orgzTreeList)?.organization_name }}
      </span>
    </div>
    <div v-if="!getSelectedOrganization(orgzTreeList)" class="box">
      <ul class="orgListsArea">
        <li
          v-for="orgzTree in orgzTreeList.list"
          :key="orgzTree.division_cd"
          :value="orgzTree.division_cd"
          class="dep"
          :class="
            orgzTree.id == props.organizationId
              ? 'bg-gray-300 text-gray-400 hover:text-gray-400 cursor-not-allowed'
              : 'bg-[white] pointer'
          "
          @click="
            orgzTree.id == props.organizationId
              ? null
              : select(orgzTree.division_cd)
          "
        >
          {{ orgzTree.organization_name }}
        </li>
      </ul>
    </div>
    <OrganizationTree
      v-if="hasUnderOrganizations(orgzTreeList)"
      :parent-level="
        props.isHumanAcademy
          ? getSelectedOrganization(orgzTreeList)?.level
          : getSelectedOrganization(orgzTreeList)?.level! + 1
      "
      :orgz-tree-list="getUnderOrganizations(orgzTreeList)"
      :is-human-academy="props.isHumanAcademy"
      :changed="transDivisionCode"
      :organization-id="props.organizationId"
    />
  </div>
</template>
<script lang="ts" setup>
import { OrganizationTreeListModel } from '../../..//models/Organization';
import useOrganization from '../../../compositions/UseOrganization';

interface Props {
  parentLevel?: number;
  orgzTreeList: OrganizationTreeListModel;
  changed: (divCd: number) => void;
  organizationId?: number;
  isHumanAcademy?: boolean;
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
<style scoped>
.box {
  margin-bottom: 1rem;
  padding: 0.5rem;
  border: 1px solid #ccc;
  border-radius: 0.25rem;
}
.dep {
  padding-left: 0.75rem;
  padding-right: 0.75rem;
  padding-top: 0.25rem;
  padding-bottom: 0.25rem;
  margin-left: 0.25rem;
  margin-top: 0.75rem;
  border-radius: 1rem;
  align-self: center;
  flex-shrink: 0;
  /* cursor: pointer; */
  /* background-color: white; */
  border: 1px solid #ccc;
}
.dep:first-child {
  margin-left: 0;
}
.level {
  color: #6b7280;
  font-weight: 600;
}
.selectOrg {
  background-color: #22b8cf;
  color: white;
  border: 1px solid #22b8cf;
  border-radius: 1rem;
  padding-left: 0.75rem;
  padding-right: 0.75rem;
  padding-top: 0.25rem;
  padding-bottom: 0.25rem;
  margin-left: 0.75rem;
  cursor: pointer;
}
.orgListsArea {
  display: flex;
  flex-wrap: wrap;
}
.levelArea {
  margin-top: 2rem;
}
</style>
