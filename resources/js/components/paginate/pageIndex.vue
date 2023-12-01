<script lang="ts" setup>
interface Props {
  isLink: boolean;
  linkString: string;
}
defineProps<Props>();
interface Emits {
  (e: 'linkClick'): () => void;
}
defineEmits<Emits>();
</script>

<template>
  <li>
    <a v-if="isLink" @click="$emit('linkClick')">{{ linkString }}</a>
    <span v-else>{{ linkString }}</span>
  </li>
</template>
