import axios from 'axios';
import useAxios from '/@/js/compositions/UseAxios';
import { OrganizationData } from '/@/js/models/admin/Organization';
import { reactive } from 'vue';

const { asyncMethodLock, axiosErrorHandling, successToast, errorToast } =
  useAxios();

interface OrganizationServiceInterface {
  organizationData: OrganizationData;
  organizationTreeList: [];
  getOrganization(organizationId: number): Promise<void>;
  postOrganization(formData: FormData): Promise<void>;
  putOrganization(formData: FormData): Promise<void>;
  getOrganizationTreeList(): Promise<void>;
}

export class OrganizationService implements OrganizationServiceInterface {
  organizationData: OrganizationData;
  organizationTreeList: [];

  constructor() {
    this.organizationData = reactive({
      id: null,
      organizationName: '',
      parentDivisionCd: null,
      isActive: 1,
    });
    this.organizationTreeList = [];
  }

  public async getOrganization(organizationId: number): Promise<void> {
    await asyncMethodLock(async () => {
      try {
        const res = await axios.get(`/api/organization/${organizationId}`);
        Object.assign(this.organizationData, res.data);
      } catch (err) {
        this.handleAxiosError(err);
      }
    });
  }

  public async postOrganization(formData: FormData): Promise<void> {
    await asyncMethodLock(async () => {
      try {
        await axios.post('/api/organization/create', formData);
        successToast(200, '部門の作成に成功しました。');
      } catch (err) {
        this.handleAxiosError(err);
      }
    });
  }

  public async putOrganization(formData: FormData): Promise<void> {
    await asyncMethodLock(async () => {
      try {
        await axios.post('/api/organization/update', formData, {
          headers: {
            'Content-type': 'multipart/form-data',
            'X-HTTP-Method-Override': 'PUT',
          },
        });
        successToast(200, '部門の更新に成功しました。');
      } catch (err) {
        this.handleAxiosError(err);
      }
    });
  }

  public async getOrganizationTreeList(): Promise<void> {
    try {
      const res = await axios.get('/api/organization');
      this.organizationTreeList = res.data;
    } catch (err) {
      this.handleAxiosError(err);
    }
  }

  private handleAxiosError(err: any): void {
    const result = axiosErrorHandling(err);
    errorToast(result.code, result.message);
  }
}
