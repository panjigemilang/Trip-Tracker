export type ActivityPeriod = 'morning' | 'afternoon' | 'evening' | 'night';

/**
 * Calculates the period of the day based on a 24-hour time string (HH:mm or HH:mm:ss).
 * Morning: 05:00 - 11:59
 * Afternoon: 12:00 - 16:59
 * Evening: 17:00 - 20:59
 * Night: 21:00 - 04:59
 * 
 * @param time Time string in 24-hour format
 * @returns ActivityPeriod string
 */
export function calculatePeriod(time: string): ActivityPeriod {
    const [hours, minutes] = time.split(':').map(Number);
    const timeInMinutes = hours * 60 + minutes;

    if (timeInMinutes >= 5 * 60 && timeInMinutes < 12 * 60) {
        return 'morning';
    }
    if (timeInMinutes >= 12 * 60 && timeInMinutes < 17 * 60) {
        return 'afternoon';
    }
    if (timeInMinutes >= 17 * 60 && timeInMinutes < 21 * 60) {
        return 'evening';
    }
    return 'night';
}
