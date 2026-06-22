import { api } from '$lib/services/api/client';
import type { PageLoad } from './$types';

export const load: PageLoad = async ({ params }) => {
  const res = await api.get<any>(`/trips/${params.id}`);
  return { trip: res.data };
};
