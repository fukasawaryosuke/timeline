import { createRouter, RouteRecordRaw, createWebHistory } from 'vue-router';
import { ref, reactive } from 'vue';
import Login from '/@/js/views/Login.vue';
import HomeVue from '../views/Home.vue';
import SendReport from '../views/SendReport.vue';
import AdminVue from '/@/js/views/Admin.vue';
import AccountList from '/@/js/components/admin/AccountList.vue';
import AccountForm from '/@/js/components/admin/AccountForm.vue';
import AccountStatistics from '/@/js/components/admin/AccountStatistics.vue';
import InHouseSystemList from '/@/js/components/admin/InHouseSystemList.vue';
import InHouseSystemForm from '/@/js/components/admin/InHouseSystemForm.vue';
import InHouseSystemSort from '/@/js/components/admin/InHouseSystemSort.vue';
import InHouseSystemStatistics from '/@/js/components/admin/InHouseSystemStatistics.vue';
import ReportList from '/@/js/components/admin/ReportList.vue';
import ReportForm from '/@/js/components/admin/ReportForm.vue';
import { LoginUser } from '/@/js/models/User';
import axios from 'axios';
import useAxios from '/@/js/compositions/UseAxios';
import useReportForm from '/@/js/compositions/UseReportForm';
import useFirebase from '/@/js/compositions/UseFirebase';
import ReportFormModel from '/@/js/models/admin/ReportForm';
import IhsFormModel from '/@/js/models/admin/IhsForm';
import { REPORT_CONTENT_TYPE } from '/@/js/contarcts/ReportContentType';
import SpHome from '/@/js/components/home/main/sp/main/SpHome.vue';
import SpSendReport from '/@/js/components/home/main/sp/main/SpSendReport.vue';
import SpDetail from '/@/js/components/home/main/sp/main/SpDetail.vue';
import CompanyList from '/@/js/components/admin/CompanyList.vue';
import CompanyForm from '/@/js/components/admin/CompanyForm.vue';
import OrganizationList from '/@/js/components/admin/OrganizationList.vue';
import OrganizationForm from '/@/js/components/admin/OrganizationForm.vue';

const { getForm } = useReportForm({} as ReportFormModel); // getFormのみの利用なので空データでいい
const { axiosErrorHandling, errorToast } = useAxios();
const isHumanAcademy = ref<boolean | null>(null);
axios.get('/api/users/loginuser/isHumanAcademy').then((res) => {
  isHumanAcademy.value = res.data;
});

const loginUser = reactive<LoginUser>({} as LoginUser);
// NOTE: エラー画面でも実行されてしまうため、エラー表示は見せない
axios.get('/api/users/loginuser').then((res) => {
  const resUser: LoginUser = res.data as LoginUser;
  loginUser.id = resUser.id;
  loginUser.human_id = resUser.human_id;
  loginUser.user_name = resUser.user_name;
  loginUser.mailadd = resUser.mailadd;
  loginUser.organization = resUser.organization;
  loginUser.system_role_id = resUser.system_role_id;
  loginUser.is_initial_password = resUser.is_initial_password;
});

const DEFAULT_TITLE = 'assistタイムライン';

const ADMIN_TITLE = '管理画面';

const routes: Array<RouteRecordRaw> = [
  {
    path: '/login',
    name: 'Login',
    component: Login,
    meta: {
      title: 'ログイン',
    },
    props: (route) => ({
      id: route.query.id,
      password: route.query.password,
      errorMessage: route.query.error_message,
    }),
  },
  {
    path: '/home',
    name: 'Home',
    component: HomeVue,
    meta: {
      title: DEFAULT_TITLE,
    },
    props: () => ({
      loginUser: loginUser,
      isHumanAcademy: isHumanAcademy.value,
    }),
  },
  {
    path: '/sphome',
    name: 'SpHome',
    component: SpHome,
    meta: {
      title: 'タイムライン', // NOTE: ショートカット作成時にデフォルトで"タイムライン"にしてほしいとの依頼があったため
    },
    props: (route) => ({
      loginUser: loginUser,
      push: route.query.push,
      pushDiary: route.meta.pushDiary,
      isHumanAcademy: isHumanAcademy.value,
    }),
    beforeEnter: async (to, from, next) => {
      if (to.query.push) {
        await axios
          .get(`/api/diary/diaryDetail?diary_id=${to.query.push}`)
          .then((res) => {
            console.log(res);
            to.meta.pushDiary = res.data;
          });
      }
      next();
    },
  },
  {
    path: '/sendreport',
    name: 'SendReport',
    component: SendReport,
    meta: {
      title: '日報作成',
    },
    props: (route) => ({
      loginUser: loginUser,
      form: route.meta.form,
      edit: false,
      ihs: route.meta.ihs,
      id: 0,
      systemName: route.meta.system_name,
      description: route.meta.description,
    }),
    beforeEnter: async (to, from, next) => {
      if (to.query.ihsId) {
        // 指定のidがない場合は暫定で1とする(5分日報のid)
        const ihsId = Number(to.query.ihsId) < 1 ? 1 : to.query.ihsId;
        const url = `/api/reportForm/getForm/${ihsId}`;
        await axios
          .get(url)
          .then((res) => {
            const reportForm = new ReportFormModel(res.data.data.report_form);
            reportForm.diary.contents.forEach((d) => {
              if (d.contentType === REPORT_CONTENT_TYPE.DATETIME) {
                d.time = new Date();
              }
            });
            to.meta.form = reportForm;
            to.meta.ihs = ihsId;
            to.meta.system_name = res.data.data.system_name;
            to.meta.description = res.data.data.description ?? '';
          })
          .catch((err) => {
            const result = axiosErrorHandling(err);
            errorToast(result.code, result.message);
            next({
              path: '/home',
            });
          });
      } else {
        next({
          path: '/home',
        });
      }
      next(); // パイプラインの次のフックに移動する。
    },
  },
  {
    path: '/spsendreport',
    name: 'SpSendReport',
    component: SpSendReport,
    meta: {
      title: '日報作成',
    },
    props: (route) => ({
      loginUser: loginUser,
      form: route.meta.form,
      edit: false,
      ihs: route.meta.ihs,
      id: 0,
      systemName: route.meta.system_name,
      description: route.meta.description,
    }),
    beforeEnter: async (to, from, next) => {
      if (to.query.ihsId) {
        // 指定のidがない場合は暫定で1とする(5分日報のid)
        const ihsId = Number(to.query.ihsId) < 1 ? 1 : to.query.ihsId;
        const url = `/api/reportForm/getForm/${ihsId}`;
        await axios
          .get(url)
          .then((res) => {
            const reportForm = new ReportFormModel(res.data.data.report_form);
            reportForm.diary.contents.forEach((d) => {
              if (d.contentType === REPORT_CONTENT_TYPE.DATETIME) {
                d.time = new Date();
              }
            });
            to.meta.form = reportForm;
            to.meta.ihs = ihsId;
            to.meta.system_name = res.data.data.system_name;
            to.meta.description = res.data.data.description ?? '';
          })
          .catch((err) => {
            const result = axiosErrorHandling(err);
            errorToast(result.code, result.message);
            next({
              path: '/sphome',
            });
          });
      } else {
        next({
          path: '/sphome',
        });
      }
      next(); // パイプラインの次のフックに移動する。
    },
  },
  {
    path: '/editreport/:reportId',
    name: 'EditReport',
    component: SendReport,
    meta: {
      title: '日報編集',
    },
    props: (route) => ({
      loginUser: loginUser,
      form: route.meta.form,
      edit: true,
      id: route.meta.id,
      systemName: route.meta.system_name,
      description: route.meta.description,
    }),
    beforeEnter: async (to, from, next) => {
      // to     :次にナビゲーションされる対象の ルートオブジェクト。
      // from   :ナビゲーションされる前の現在のルート
      // next   :フックを解決するための関数
      // 指定のdiaryを取得しpropsに渡す
      if (to.params.reportId) {
        const url = `/api/diary/myDiary?diary_id=${to.params.reportId}`;
        await axios
          .get(url, {
            data: {
              diary_id: to.params.reportId,
            },
          })
          .then((res) => {
            const reportForm = new ReportFormModel(res.data);
            // 日付に変換
            reportForm.diary.contents.forEach((d) => {
              if (d.contentType === REPORT_CONTENT_TYPE.DATETIME && d.time) {
                d.time = new Date(d.time);
              }
            });
            to.meta.form = reportForm;
            to.meta.id = Number(to.params.reportId);
            to.meta.system_name = res.data.system_name;
            to.meta.description = res.data.description;
          })
          .catch((err) => {
            const result = axiosErrorHandling(err);
            errorToast(result.code, result.message);
            next({
              path: '/home',
            });
          });
      } else {
        next({
          path: '/home',
        });
      }
      next(); // パイプラインの次のフックに移動する。
    },
  },
  {
    path: '/speditreport/:reportId',
    name: 'SpEditReport',
    component: SpSendReport,
    meta: {
      title: '日報編集',
    },
    props: (route) => ({
      loginUser: loginUser,
      form: route.meta.form,
      edit: true,
      id: route.meta.id,
      systemName: route.meta.system_name,
      description: route.meta.description,
    }),
    beforeEnter: async (to, from, next) => {
      // to     :次にナビゲーションされる対象の ルートオブジェクト。
      // from   :ナビゲーションされる前の現在のルート
      // next   :フックを解決するための関数
      // 指定のdiaryを取得しpropsに渡す
      if (to.params.reportId) {
        const url = `/api/diary/myDiary?diary_id=${to.params.reportId}`;
        await axios
          .get(url, {
            data: {
              diary_id: to.params.reportId,
            },
          })
          .then((res) => {
            const reportForm = new ReportFormModel(res.data);
            // 日付に変換
            reportForm.diary.contents.forEach((d) => {
              if (d.contentType === REPORT_CONTENT_TYPE.DATETIME && d.time) {
                d.time = new Date(d.time);
              }
            });
            to.meta.form = reportForm;
            to.meta.id = Number(to.params.reportId);
            to.meta.system_name = res.data.system_name;
            to.meta.description = res.data.description;
          })
          .catch((err) => {
            const result = axiosErrorHandling(err);
            errorToast(result.code, result.message);
            next({
              path: '/home',
            });
          });
      } else {
        next({
          path: '/home',
        });
      }
      next(); // パイプラインの次のフックに移動する。
    },
  },
  // 管理画面
  {
    path: '/admin/account',
    component: AdminVue,
    props: () => ({
      loginUser: loginUser,
    }),
    children: [
      // アカウント一覧
      {
        path: '',
        component: AccountList,
        meta: {
          title: `${ADMIN_TITLE} アカウント一覧`,
        },
        name: 'AccountList',
      },
      // アカウント作成
      {
        path: 'create',
        component: AccountForm,
        name: 'AccountCreate',
        meta: {
          title: `${ADMIN_TITLE} アカウント作成`,
        },
      },
      // アカウント更新
      {
        path: ':id',
        component: AccountForm,
        name: 'AccountEdit',
        meta: {
          title: `${ADMIN_TITLE} アカウント更新`,
        },
        props: (route) => ({
          id: route.params.id,
          isHumanAcademy: isHumanAcademy.value,
        }),
      },
      // アカウント別投稿数
      {
        path: 'statistics',
        component: AccountStatistics,
        meta: {
          title: `${ADMIN_TITLE} アカウント別投稿数`,
        },
        name: 'AccountStatistics',
      },
    ],
  },
  {
    path: '/admin/in_house_system',
    component: AdminVue,
    props: () => ({
      loginUser: loginUser,
    }),
    children: [
      // 制度一覧
      {
        path: '',
        component: InHouseSystemList,
        meta: {
          title: `${ADMIN_TITLE} カテゴリ一覧`,
        },
        name: 'InHouseSystemList',
      },
      // 制度並び替え
      {
        path: 'sort',
        component: InHouseSystemSort,
        meta: {
          title: `${ADMIN_TITLE} カテゴリ並び替え`,
        },
        name: 'InHouseSystemSort',
      },
      // 制度作成
      {
        path: 'create',
        component: InHouseSystemForm,
        meta: {
          title: `${ADMIN_TITLE} カテゴリ作成`,
        },
        name: 'InHouseSystemCreate',
        props: (route) => ({
          id: route.params.id,
          ihs: IhsFormModel.getInstance(),
          formList: route.meta.form_list,
          edit: false,
        }),
        beforeEnter: async (to, from, next) => {
          await axios
            .get(`/api/reportForm/getFormList`)
            .then((res) => {
              to.meta.form_list = res.data.data;
            })
            .catch((err) => {
              const result = axiosErrorHandling(err);
              errorToast(result.code, result.message);
              next({
                path: '/admin/in_house_system',
              });
            });
          next();
        },
      },
      // 制度更新
      {
        path: ':id',
        component: InHouseSystemForm,
        meta: {
          title: `${ADMIN_TITLE} カテゴリ更新`,
        },
        name: 'InHouseSystemEdit',
        props: (route) => ({
          id: route.params.id,
          ihs: route.meta.ihs,
          formList: route.meta.form_list,
          edit: true,
        }),
        beforeEnter: async (to, from, next) => {
          await axios
            .get(`/api/ihs/${to.params.id}`)
            .then((res) => {
              to.meta.ihs = new IhsFormModel(res.data.data);
            })
            .catch((err) => {
              const result = axiosErrorHandling(err);
              errorToast(result.code, result.message);
              next({
                path: '/admin/in_house_system',
              });
            });
          await axios
            .get(`/api/reportForm/getFormList`)
            .then((res) => {
              to.meta.form_list = res.data.data;
            })
            .catch((err) => {
              const result = axiosErrorHandling(err);
              errorToast(result.code, result.message);
              next({
                path: '/admin/in_house_system',
              });
            });
          next();
        },
      },
      // 制度別投稿数
      {
        path: 'statistics',
        component: InHouseSystemStatistics,
        meta: {
          title: `${ADMIN_TITLE} カテゴリ別投稿数`,
        },
        name: 'InHouseSystemStatistics',
      },
    ],
  },
  {
    path: '/admin/report_form',
    component: AdminVue,
    props: () => ({
      loginUser: loginUser,
    }),
    children: [
      // 投稿フォーム一覧
      {
        path: '',
        component: ReportList,
        meta: {
          title: `${ADMIN_TITLE} 投稿フォーム一覧`,
        },
        name: 'ReportFormList',
      },
      // 投稿フォーム作成
      {
        path: 'create',
        component: ReportForm,
        name: 'ReportFormCreate',
        meta: {
          title: `${ADMIN_TITLE} 投稿フォーム作成`,
        },
        props: (route) => ({
          id: route.params.id,
          form: ReportFormModel.getInstance(),
          edit: false,
        }),
      },
      // 投稿フォーム更新
      {
        path: ':id',
        component: ReportForm,
        meta: {
          title: `${ADMIN_TITLE} 投稿フォーム更新`,
        },
        name: 'ReportFormEdit',
        props: (route) => ({
          id: route.params.id,
          form: route.meta.form,
          edit: true,
          firstForm: route.meta.firstForm,
        }),
        beforeEnter: async (to, from, next) => {
          const [result, firstForm] = await getForm(to.params.id);
          if (result) {
            to.meta.form = result;
            to.meta.firstForm = firstForm;
            next();
          } else {
            next('/admin/report_form');
          }
        },
      },
    ],
  },
  //法人管理
  {
    path: '/admin/company',
    component: AdminVue,
    props: () => ({
      loginUser: loginUser,
    }),
    beforeEnter: (to, from, next) => {
      axios.get('/api/users/loginuser/isHumanAcademy').then((res) => {
        const isHumanAcademy = res.data;
        isHumanAcademy ? next() : next('/admin');
      });
    },
    children: [
      {
        path: '',
        component: CompanyList,
        name: 'CompanyList',
        meta: {
          title: `${ADMIN_TITLE} 法人一覧`,
        },
      },
      {
        path: 'create',
        component: CompanyForm,
        name: 'CompanyCreate',
        meta: {
          title: `${ADMIN_TITLE} 法人作成`,
        },
      },
      {
        path: ':id',
        component: CompanyForm,
        name: 'CompanyEdit',
        meta: {
          title: `${ADMIN_TITLE} 法人更新`,
        },
        props: (route) => ({
          id: route.params.id,
        }),
      },
    ],
  },
  //部門管理
  {
    path: '/admin/organization',
    component: AdminVue,
    props: () => ({
      loginUser: loginUser,
    }),
    children: [
      {
        path: '',
        component: OrganizationList,
        name: 'OrganizationList',
        meta: {
          title: `${ADMIN_TITLE} 部門一覧`,
        },
      },
      {
        path: 'create',
        component: OrganizationForm,
        name: 'OrganizationCreate',
        meta: {
          title: `${ADMIN_TITLE} 部門作成`,
        },
      },
      {
        path: ':id',
        component: OrganizationForm,
        name: 'OrganizationEdit',
        meta: {
          title: `${ADMIN_TITLE} 部門更新`,
        },
        props: (route) => ({
          id: route.params.id,
        }),
      },
    ],
  },
  {
    path: '/admin',
    redirect: () => {
      return { name: 'AccountList' };
    },
  },
  {
    path: '/',
    redirect: () => {
      return { name: 'Home' };
    },
  },
];

const router = createRouter({
  history: createWebHistory('/'),
  routes,
  // スクロールバーの初期位置を常にTopにする
  scrollBehavior(to, from, savedPosition) {
    return { top: 0 };
  },
});
router.afterEach((to) => {
  const title = to.meta.title ? String(to.meta.title) : DEFAULT_TITLE;
  document.title = title;
});

export default router;
