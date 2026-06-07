export interface Activity {
  id: string;
  trip_id: string;
  date: string;
  time: string;
  title: string;
  location?: string;
  notes?: string;
  sort_order: number;
}

export interface Trip {
  id: string;
  user_id: string;
  title: string;
  description?: string;
  status: 'planned' | 'active' | 'completed' | 'cancelled';
  start_date?: string;
  end_date?: string;
  activities?: Activity[];
}
