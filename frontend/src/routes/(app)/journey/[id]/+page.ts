import { journeyService } from '$lib/services/api/journeyService';
import type { PageLoad } from './$types';

export const load: PageLoad = async ({ params }) => {
  const journey = await journeyService.getJourney(params.id);
  return { journey };
};
