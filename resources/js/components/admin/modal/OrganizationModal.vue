<template>
  <section
    v-if="orgzModalOpen"
    class="modalArea"
    :class="{
      openModal: orgzModalOpen,
      closeModal: !orgzModalOpen,
    }"
  >
    <div id="modalBg" class="modalBg" @click="$emit('close')"></div>
    <div
      class="modalWrapper"
    >
      <fa
        icon="fa-xmark"
        class="xmark"
        @click="$emit('close')"
      />
      <div class="modalContents">
        <div
          class="title"
        >
          部門を選択
        </div>
        <div
          class="modalInner"
        >
          <OrganizationTree
            :organization-id="props.organizationId"
            :orgz-tree-list="orgzTreeList"
            :is-human-academy="isHumanAcademy"
            :changed="divisionCodeChanged"
          />
        </div>
        <div class="buttonArea">
          <div
            class="selectButton bg-assist-grad"
            @click="filterWithDivisionCd"
          >
            選択
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
<script lang="ts" setup>
import { ref, onMounted } from 'vue';
import { OrganizationTreeListModel } from '/@/js/models/Organization';
import OrganizationTree from '../organization/OrganizationTree.vue';
import useOrganization from '../../../compositions/UseOrganization';
import axios from 'axios';

interface Props {
  orgzTreeList: OrganizationTreeListModel;
  orgzModalOpen: boolean;
  organizationId?: number;
}
const props = defineProps<Props>();

interface Emits {
  (e: 'close'): void;
  (e: 'select', org: any): void;
}
const emits = defineEmits<Emits>();

const selectedDivisionCode = ref<number>(0);
const divisionCodeChanged = (division_cd: number) => {
  selectedDivisionCode.value = division_cd;
};
const filterWithDivisionCd = () => {
  emits('select', selectedDivisionCode.value);
  emits('close');
};

const isHumanAcademy = ref<boolean>(false);
const getIsHumanAcademy = async (): Promise<void> => {
  axios.get('/api/users/loginuser/isHumanAcademy').then((res) => {
  isHumanAcademy.value = res.data;
  });
};

onMounted(() => {
  getIsHumanAcademy();
});
</script>
<style scoped>
.openModal {
  transition: 0.5s;
  opacity: 1;
}
.closeModal {
  transition: 0.5s;
  opacity: 0;
}
.modalArea {
  display: block;
  position: fixed;
  z-index: 150;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: #00000099;
}

.modalBg {
  width: 100%;
  height: 100%;
  background-color: rgba(30, 30, 30, 0);
}

.modalWrapper {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  min-width: 750px;
  /* max-width: 75%; */
  /* padding: 40px 5%; */
  height: auto;
  overflow: auto;
  box-sizing: inherit;

  padding-left: 20px;
  padding-right: 20px;
  background-color: white;
  border: 1px solid #ccc;
  border-radius: 0.25rem;
  width: 176px;
}

.xmark{
  cursor: pointer;
  font-weight: 600;
  color: #ccc;
  position: absolute;
  top: 9px;
  right: 9px;
  font-size: 2rem;
}
.xmark:hover{
  color: #666;
}
.title{
  margin-bottom: 1rem;
  padding-top: 1rem;
  color: #6b7280;
  border-bottom: 1px solid #d1d5db;
  font-size: 1.25rem;
  font-weight: 600;
}
.modalInner{
  max-height: calc(100vh - 20rem);
  overflow-y: scroll;
}
.buttonArea{
  padding-top: 2rem;
  padding-bottom: 2rem;
  display: flex;
  justify-content: center;
}
.selectButton{
  width: 10rem;
  padding-top: 0.5rem;
  padding-bottom: 0.5rem;
  border-radius: 50rem;
  color: white;
  cursor: pointer;
  text-align: center;
}
</style>
