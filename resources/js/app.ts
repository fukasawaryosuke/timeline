import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import Toast, { POSITION } from 'vue-toastification';
import 'vue-toastification/dist/index.css';
import { library } from '@fortawesome/fontawesome-svg-core';
import {
  faLeaf,
  faLink,
  faXmark,
  faArrowUpRightFromSquare,
  faClipboardCheck,
  faBorderAll,
  faPaperPlane,
  faChevronLeft,
  faChevronRight,
  faClipboard,
  faPenToSquare,
  faStamp,
  faX,
  faHouse,
  faPen,
  faSun,
  faCloud,
  faCloudRain,
  faThunderstorm,
  faClockRotateLeft,
  faUser,
  faTrash,
  faBuilding,
  faVideo,
  faReply,
  faQuestion,
  faSearch
} from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

library.add(faLeaf);
library.add(faBorderAll);
library.add(faPaperPlane);
library.add(faClipboardCheck);
library.add(faLink);
library.add(faArrowUpRightFromSquare);
library.add(faChevronLeft);
library.add(faChevronRight);
library.add(faXmark);
library.add(faClipboard);
library.add(faPenToSquare);
library.add(faStamp);
library.add(faX);
library.add(faHouse);
library.add(faPen);
library.add(faSun);
library.add(faCloud);
library.add(faCloudRain);
library.add(faThunderstorm);
library.add(faClockRotateLeft);
library.add(faUser);
library.add(faTrash);
library.add(faBuilding);
library.add(faVideo);
library.add(faReply);
library.add(faQuestion);
library.add(faSearch);

createApp(App)
  .component('fa', FontAwesomeIcon)
  .use(router)
  .use(Toast, {
    position: POSITION.TOP_RIGHT,
    timeout: 3000,
    transition: {
      enter: 'Vue-Toastification__fade-enter-active',
      leave: 'Vue-Toastification__fade-leave-active',
      move: 'fade-move',
    },
  })
  .mount('#app');
