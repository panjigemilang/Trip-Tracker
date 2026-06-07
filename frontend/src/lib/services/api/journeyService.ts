import { apiClient } from './client';
import type { Journey } from '$lib/types/journey';

export const journeyService = {
  startJourney: async (tripId: string): Promise<Journey> => {
    return apiClient.post<{ data: Journey }>(`/trips/${tripId}/journey`, {}).then((res) => res.data);
  },

  getJourney: async (journeyId: string): Promise<Journey> => {
    return apiClient.get<{ data: Journey }>(`/journeys/${journeyId}`).then((res) => res.data);
  },

  updateActivityStatus: async (journeyId: string, activityId: string, status: string): Promise<Journey> => {
    return apiClient.patch<{ data: Journey }>(`/journeys/${journeyId}/activities/${activityId}`, { status }).then((res) => res.data);
  }
};
