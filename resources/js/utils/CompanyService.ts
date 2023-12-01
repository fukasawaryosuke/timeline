import axios from 'axios';
import useAxios from '/@/js/compositions/UseAxios';
import { CompanyData } from '/@/js/models/admin/Company';
import { reactive } from 'vue';

const { asyncMethodLock, axiosErrorHandling, successToast, errorToast } =
  useAxios();

interface CompanyServiceInterface {
  companyData: CompanyData;
  getCompany(companyId: string | string[]): Promise<void>;
  postCompany(formData: FormData): Promise<void>;
  putCompany(formData: FormData): Promise<void>;
}

export class CompanyService implements CompanyServiceInterface {
  companyData: CompanyData;

  constructor() {
    this.companyData = reactive({
      id: null,
      companyName: '',
      isActive: 1,
      iconFile: null,
      companyIconPath: '',
    });
  }

  public async getCompany(companyId: string | string[]): Promise<void> {
    await asyncMethodLock(async () => {
      try {
        const res = await axios.get(`/api/company/${companyId}`);
        Object.assign(this.companyData, res.data);
      } catch (err) {
        this.handleAxiosError(err);
      }
    });
  }

  public async postCompany(formData: FormData): Promise<void> {
    await asyncMethodLock(async () => {
      try {
        await axios.post('/api/company/create', formData);
        successToast(200, '法人の作成に成功しました。');
      } catch (err) {
        this.handleAxiosError(err);
      }
    });
  }

  public async putCompany(formData: FormData): Promise<void> {
    await asyncMethodLock(async () => {
      try {
        await axios.post('/api/company/update', formData, {
          headers: {
            'Content-type': 'multipart/form-data',
            'X-HTTP-Method-Override': 'PUT',
          },
        });
        successToast(200, '法人の更新に成功しました。');
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
