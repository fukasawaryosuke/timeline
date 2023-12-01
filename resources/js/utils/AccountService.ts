import axios from 'axios';
import useAxios from '/@/js/compositions/UseAxios';
import { AccountData } from '/@/js/models/admin/Account';
import { reactive } from 'vue';

const { asyncMethodLock, axiosErrorHandling, successToast, errorToast } =
  useAxios();

interface AccountServiceInterface {
  accountData: AccountData;
  getAccount(userId: number): Promise<void>;
  postAccount(formData: FormData): Promise<void>;
  putAccount(formData: FormData): Promise<void>;
  resetPassword(userId: number): Promise<void>;
}

export class AccountService implements AccountServiceInterface {
  accountData: AccountData;

  constructor() {
    this.accountData = reactive({
      id: null,
      userName: '',
      mailadd: '',
      divisionCd: null,
      isActive: 1,
      systemRoleId: 1,
      isInitialUser: 0,
    });
  }

  public async getAccount(userId: number): Promise<void> {
    await asyncMethodLock(async () => {
      try {
        const res = await axios.get(`/api/account/${userId}`);
        Object.assign(this.accountData, res.data);
      } catch (err) {
        this.handleAxiosError(err);
      }
    });
  }

  public async postAccount(formData: FormData): Promise<void> {
    await asyncMethodLock(async () => {
      try {
        await axios.post('/api/account/create', formData);
        successToast(200, 'アカウントの作成に成功しました。');
      } catch (err) {
        this.handleAxiosError(err);
      }
    });
  }

  public async putAccount(formData: FormData): Promise<void> {
    await asyncMethodLock(async () => {
      try {
        await axios.post('/api/account/update', formData, {
          headers: {
            'Content-type': 'multipart/form-data',
            'X-HTTP-Method-Override': 'PUT',
          },
        });
        successToast(200, 'アカウントの更新に成功しました。');
      } catch (err) {
        this.handleAxiosError(err);
      }
    });
  }

  public async resetPassword(userId: number): Promise<void> {
    await asyncMethodLock(async () => {
      try {
        await axios.post(
          '/api/account/reset/password',
          { user_id: userId },
          {
            headers: {
              'Content-type': 'application/json',
              'X-HTTP-Method-Override': 'PUT',
            },
          }
        );
        successToast(200, 'パスワードのリセットに成功しました。');
      } catch (err) {
        this.handleAxiosError(err);
      }
    });
  }

  private handleAxiosError(err: any): void {
    const result = axiosErrorHandling(err);
    errorToast(result.code, result.message);
  }
}
