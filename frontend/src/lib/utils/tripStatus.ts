export type DisplayStatus = 'active' | 'upcoming' | 'next-plan' | 'completed' | 'expired' | 'cancelled' | 'draft' | 'planned';

export function getDisplayStatus(trip: any): DisplayStatus {
  if (trip.journey?.status === 'completed' || trip.journey?.status === 'cancelled') {
    return trip.journey.status as DisplayStatus;
  }

  // If backend says it's active, completed, expired, cancelled, or draft, use that strictly.
  // Active means the journey has explicitly been started.
  if (['active', 'completed', 'expired', 'cancelled', 'draft'].includes(trip.status)) {
    return trip.status as DisplayStatus;
  }

  // If status is 'planned' (which is the default when activities are added), 
  // we derive 'upcoming' or 'next-plan' based on dates.
  if (!trip.start_date) {
    return 'planned';
  }

  const today = new Date();
  today.setHours(0, 0, 0, 0);
  
  const startDate = new Date(trip.start_date);
  startDate.setHours(0, 0, 0, 0);
  
  const diffTime = startDate.getTime() - today.getTime();
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

  // Even if the trip starts today or is slightly in the past, if it's not marked 'active', it's still 'upcoming' or 'planned'.
  if (diffDays <= 7) {
    return 'upcoming';
  } else {
    return 'next-plan';
  }
}
