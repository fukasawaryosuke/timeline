export interface OrganizationData {
  id: number | null;
  organizationName: string;
  divisionCd?: number;
  parentDivisionCd: number | null;
  isActive: number;
}
