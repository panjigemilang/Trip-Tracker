import { journeyService } from '$lib/services/api/journeyService';
import type { Journey, ActivityStatus } from '$lib/types/journey';

export function createJourneyStore() {
  let activeJourney = $state<Journey | null>(null);
  let loading = $state(false);
  let error = $state<string | null>(null);

  async function fetchJourney(id: string) {
    loading = true;
    error = null;
    try {
      activeJourney = await journeyService.getJourney(id);
    } catch (e: any) {
      error = e.message || 'Failed to fetch journey';
    } finally {
      loading = false;
    }
  }

  async function startJourney(tripId: string) {
    loading = true;
    error = null;
    try {
      activeJourney = await journeyService.startJourney(tripId);
      return activeJourney;
    } catch (e: any) {
      error = e.message || 'Failed to start journey';
      throw e;
    } finally {
      loading = false;
    }
  }

  async function updateActivityStatus(activityId: string, status: ActivityStatus) {
    if (!activeJourney) return;
    
    // Optimistic update
    const previousJourney = JSON.parse(JSON.stringify(activeJourney));
    
    if (activeJourney.journey_activities) {
      const activityIdx = activeJourney.journey_activities.findIndex(ja => ja.activity_id === activityId);
      if (activityIdx !== -1) {
        activeJourney.journey_activities[activityIdx].status = status;
      } else {
        activeJourney.journey_activities.push({
          id: 'temp',
          journey_id: activeJourney.id,
          activity_id: activityId,
          status,
          status_changed_at: new Date().toISOString(),
          created_at: new Date().toISOString(),
          updated_at: new Date().toISOString(),
        });
      }
    }

    try {
      // Background sync
      const updatedJourney = await journeyService.updateActivityStatus(activeJourney.id, activityId, status);
      // Reconcile
      activeJourney = updatedJourney;
    } catch (e: any) {
      error = e.message || 'Failed to update activity status';
      // Rollback
      activeJourney = previousJourney;
    }
  }

  return {
    get activeJourney() { return activeJourney; },
    set activeJourney(val) { activeJourney = val; },
    get loading() { return loading; },
    get error() { return error; },
    fetchJourney,
    startJourney,
    updateActivityStatus,
  };
}

export const journeyStore = createJourneyStore();
