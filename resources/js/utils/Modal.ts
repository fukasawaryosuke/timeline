import { ref, Ref } from 'vue';
interface ModalInterface {
  title: string;
  message?: string;
  subMessage?: string;
  isOpen: Ref<boolean>;
  open(): void;
  close(): void;
}

export class Modal implements ModalInterface {
  title: string;
  message?: string;
  subMessage?: string;
  isOpen: Ref<boolean>;

  constructor(title: string, message?: string, subMessage?: string) {
    this.title = title;
    this.message = message;
    this.subMessage = subMessage;
    this.isOpen = ref(false);
  }

  open() {
    this.isOpen.value = true;
  }

  close() {
    this.isOpen.value = false;
  }

  rightButton() {
    this.close();
  }
}
