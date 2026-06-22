export function parseLocalDate(dStr: string | Date): Date {
  if (dStr instanceof Date) return dStr;
  
  // Check for YYYY-MM-DD or YYYY/MM/DD format
  const dateParts = dStr.match(/^(\d{4})[-/](\d{1,2})[-/](\d{1,2})$/);
  if (dateParts) {
    const year = parseInt(dateParts[1], 10);
    const month = parseInt(dateParts[2], 10) - 1; // 0-indexed
    const day = parseInt(dateParts[3], 10);
    return new Date(year, month, day);
  }
  
  // Fallback to standard Date parsing
  return new Date(dStr);
}

export function formatDate(dStr: string | Date | null | undefined): string {
  if (!dStr) return '';
  const date = parseLocalDate(dStr);
  if (isNaN(date.getTime())) return String(dStr);
  
  const day = date.getDate();
  const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
  const month = monthNames[date.getMonth()];
  const year = date.getFullYear();
  
  return `${day} ${month}, ${year}`;
}

export function formatTime(tStr: string | Date | null | undefined): string {
  if (!tStr) return '';
  let date: Date;
  
  if (tStr instanceof Date) {
    date = tStr;
  } else if (typeof tStr === 'string' && tStr.includes(':')) {
    // Extract time parts (HH:MM:SS or HH:MM)
    const timeParts = tStr.match(/(\d{1,2}):(\d{2})(?::(\d{2}))?/);
    if (timeParts) {
      const hours = parseInt(timeParts[1], 10);
      const minutes = parseInt(timeParts[2], 10);
      date = new Date();
      date.setHours(hours, minutes, 0, 0);
    } else {
      date = new Date(tStr);
    }
  } else {
    date = new Date(tStr);
  }
  
  if (isNaN(date.getTime())) return String(tStr);
  
  let hours = date.getHours();
  const minutes = String(date.getMinutes()).padStart(2, '0');
  const ampm = hours >= 12 ? 'PM' : 'AM';
  hours = hours % 12;
  hours = hours ? hours : 12; // the hour '0' should be '12'
  const strHours = String(hours).padStart(2, '0');
  
  return `${strHours}:${minutes} ${ampm}`;
}
