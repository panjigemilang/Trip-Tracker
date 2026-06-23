import { api } from '$lib/services/api/client';
import { browser } from '$app/environment';

export interface AppNotification {
  id: string;
  type: 'upcoming' | 'active' | 'alert';
  title: string;
  message: string;
  time: string;
  read: boolean;
  link?: string;
  extra?: {
    isTrip?: boolean;
    isStats?: boolean;
    tripName?: string;
    segmentName?: string;
    dateText?: string;
    buttonText?: string;
    status?: 'active' | 'upcoming';
    periodTitle?: string;
    completedCount?: number;
    targetCount?: number;
    percentage?: number;
    period?: 'year' | '6months';
  };
}

function formatDate(dateStr?: string) {
  if (!dateStr) return '';
  const date = new Date(dateStr);
  const months = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];
  return `${months[date.getMonth()]} ${date.getDate()}, ${date.getFullYear()}`;
}

class NotificationsStore {
  notifications = $state<AppNotification[]>([]);
  unreadCount = $derived(this.notifications.filter(n => !n.read).length);
  isMobileOverlayOpen = $state(false);
  private initialized = false;

  toggleMobileOverlay() {
    this.isMobileOverlayOpen = !this.isMobileOverlayOpen;
  }

  async init() {
    if (!browser || this.initialized) return;
    this.initialized = true;
    await this.fetchAndDerive();
  }

  async fetchAndDerive() {
    try {
      const res = await api.get<any>('/trips');
      const trips: any[] = res.data || [];
      
      const newNotifs: AppNotification[] = [];
      const now = new Date();
      now.setHours(0, 0, 0, 0);
      
      // 1. Active Trip
      const activeTrip = trips.find(t => t.status === 'active' || t.journey_id);
      if (activeTrip) {
        newNotifs.push({
          id: `active_${activeTrip.id}`,
          type: 'active',
          title: 'Journey Protocol Initiated',
          message: activeTrip.title,
          time: 'now',
          read: false,
          link: `/trips/${activeTrip.slug}/${activeTrip.id}`,
          extra: {
            isTrip: true,
            tripName: activeTrip.title,
            segmentName: 'Active tracking: In progress',
            dateText: formatDate(activeTrip.start_date),
            buttonText: 'RESUME SESSION',
            status: 'active'
          }
        });
      }

      // 2. Upcoming Trip
      const upcomingTrips = trips.filter(t => {
        if (!t.start_date || t.status === 'completed' || t.status === 'cancelled') return false;
        const start = new Date(t.start_date);
        start.setHours(0, 0, 0, 0);
        return start > now;
      }).sort((a, b) => new Date(a.start_date).getTime() - new Date(b.start_date).getTime());

      if (upcomingTrips.length > 0) {
        const nextTrip = upcomingTrips[0];
        const daysUntil = Math.ceil((new Date(nextTrip.start_date).getTime() - now.getTime()) / (1000 * 3600 * 24));
        newNotifs.push({
          id: `upcoming_${nextTrip.id}`,
          type: 'upcoming',
          title: 'Upcoming',
          message: `${nextTrip.title} starts in ${daysUntil} day${daysUntil > 1 ? 's' : ''}.`,
          time: 'recently',
          read: false,
          link: `/trips/${nextTrip.slug}/${nextTrip.id}`,
          extra: {
            isTrip: true,
            tripName: nextTrip.title,
            segmentName: nextTrip.description || 'Upcoming expedition',
            dateText: formatDate(nextTrip.start_date),
            buttonText: `${daysUntil} DAYS UNTIL DEPARTURE`,
            status: 'upcoming'
          }
        });
      }

      // 3. Stats (Yearly)
      const currentYear = now.getFullYear();
      const tripsThisYear = trips.filter(t => {
        if (!t.start_date && !t.end_date) return false;
        const dateStr = t.start_date || t.end_date;
        return new Date(dateStr).getFullYear() === currentYear;
      });
      const completedThisYear = tripsThisYear.filter(t => t.status === 'completed');

      if (tripsThisYear.length > 0) {
        const yearlyTotal = tripsThisYear.length;
        const yearlyPercentage = Math.round((completedThisYear.length / yearlyTotal) * 100);

        newNotifs.push({
          id: `stats_year_${currentYear}`,
          type: 'alert',
          title: 'Milestone Update',
          message: `${completedThisYear.length} of ${yearlyTotal} expeditions completed in ${currentYear}.`,
          time: 'today',
          read: false,
          link: '/history',
          extra: {
            isStats: true,
            periodTitle: `YEAR ${currentYear}`,
            completedCount: completedThisYear.length,
            targetCount: yearlyTotal,
            percentage: yearlyPercentage,
            period: 'year'
          }
        });
      }

      // 4. Stats (6 Months)
      const sixMonthsAgo = new Date();
      sixMonthsAgo.setMonth(sixMonthsAgo.getMonth() - 6);
      
      const tripsLast6Months = trips.filter(t => {
        if (!t.start_date && !t.end_date) return false;
        const dateStr = t.start_date || t.end_date;
        return new Date(dateStr) >= sixMonthsAgo;
      });
      const completedLast6Months = tripsLast6Months.filter(t => t.status === 'completed');

      if (tripsLast6Months.length > 0) {
        const sixMonthTotal = tripsLast6Months.length;
        const sixMonthPercentage = Math.round((completedLast6Months.length / sixMonthTotal) * 100);

        newNotifs.push({
          id: `stats_6months`,
          type: 'alert',
          title: '6-Month Milestone',
          message: `${completedLast6Months.length} of ${sixMonthTotal} expeditions completed in the last 6 months.`,
          time: 'today',
          read: false,
          link: '/history',
          extra: {
            isStats: true,
            periodTitle: 'LAST 6 MONTHS',
            completedCount: completedLast6Months.length,
            targetCount: sixMonthTotal,
            percentage: sixMonthPercentage,
            period: '6months'
          }
        });
      }

      // Load read status from localStorage
      const readSet = new Set(JSON.parse(localStorage.getItem('read_notifications') || '[]'));
      
      this.notifications = newNotifs.map(n => ({
        ...n,
        read: readSet.has(n.id)
      }));

    } catch(e) {
      console.error('Failed to fetch notifications data', e);
    }
  }

  markAsRead(id: string) {
    const notif = this.notifications.find(n => n.id === id);
    if (notif) {
      notif.read = true;
      this.saveReadState();
    }
  }

  markAllAsRead() {
    for (const notif of this.notifications) {
      notif.read = true;
    }
    this.saveReadState();
  }

  private saveReadState() {
    if (!browser) return;
    const readIds = this.notifications.filter(n => n.read).map(n => n.id);
    localStorage.setItem('read_notifications', JSON.stringify(readIds));
  }
}

export const notificationsStore = new NotificationsStore();
