export type JourneyStatus = 'active' | 'completed' | 'cancelled';
export type ActivityStatus = 'upcoming' | 'current' | 'completed' | 'skipped' | 'cancelled' | 'missed';

export interface JourneyActivity {
  id: string;
  journey_id: string;
  activity_id: string;
  status: ActivityStatus;
  status_changed_at: string | null;
  created_at: string;
  updated_at: string;
  activity?: any; // To be typed properly if needed
}

export interface Journey {
  id: string;
  trip_id: string;
  user_id: string;
  status: JourneyStatus;
  current_activity_id: string | null;
  started_at: string;
  ended_at: string | null;
  created_at: string;
  updated_at: string;
  journey_activities?: JourneyActivity[];
  current_activity?: any;
  trip?: any;
}
