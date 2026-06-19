<script lang="ts">
  import { Calendar as CalendarIcon, ChevronLeft, ChevronRight } from 'lucide-svelte';

  let { 
    startDate = $bindable(''), 
    endDate = $bindable(''), 
    placeholder = 'SELECT EXPEDITION TIMELINE...' 
  } = $props<{
    startDate: string;
    endDate: string;
    placeholder?: string;
  }>();

  let isOpen = $state(false);
  let containerEl = $state<HTMLDivElement | null>(null);
  let currentMonth = $state(new Date());
  let hoveredDate = $state<string | null>(null);

  $effect(() => {
    if (startDate) {
      const parsed = new Date(startDate);
      if (!isNaN(parsed.getTime())) {
        currentMonth = parsed;
      }
    }
  });

  const monthNames = [
    "JANUARY", "FEBRUARY", "MARCH", "APRIL", "MAY", "JUNE",
    "JULY", "AUGUST", "SEPTEMBER", "OCTOBER", "NOVEMBER", "DECEMBER"
  ];
  
  const weekDays = ["SU", "MO", "TU", "WE", "TH", "FR", "SA"];

  function toISODateString(date: Date) {
    const y = date.getFullYear();
    const m = String(date.getMonth() + 1).padStart(2, '0');
    const d = String(date.getDate()).padStart(2, '0');
    return `${y}-${m}-${d}`;
  }

  function formatDateFriendly(dateStr: string) {
    if (!dateStr) return '';
    const date = new Date(dateStr);
    if (isNaN(date.getTime())) return '';
    return date.toLocaleDateString(undefined, { 
      month: 'short', 
      day: 'numeric', 
      year: 'numeric' 
    });
  }

  let displayValue = $derived.by(() => {
    if (!startDate && !endDate) return '';
    const startStr = formatDateFriendly(startDate);
    const endStr = formatDateFriendly(endDate);
    if (startStr && endStr) return `${startStr} — ${endStr}`;
    if (startStr) return `${startStr} — SELECT END DATE...`;
    return '';
  });

  let gridDays = $derived.by(() => {
    const year = currentMonth.getFullYear();
    const month = currentMonth.getMonth();
    
    const firstDayIndex = new Date(year, month, 1).getDay();
    const prevMonthDays = new Date(year, month, 0).getDate();
    const currentMonthDays = new Date(year, month + 1, 0).getDate();
    
    const days = [];
    
    // Previous Month padding
    for (let i = firstDayIndex - 1; i >= 0; i--) {
      days.push({
        date: new Date(year, month - 1, prevMonthDays - i),
        isCurrentMonth: false
      });
    }
    
    // Current Month days
    for (let i = 1; i <= currentMonthDays; i++) {
      days.push({
        date: new Date(year, month, i),
        isCurrentMonth: true
      });
    }
    
    // Next Month padding to 42 items
    const remaining = 42 - days.length;
    for (let i = 1; i <= remaining; i++) {
      days.push({
        date: new Date(year, month + 1, i),
        isCurrentMonth: false
      });
    }
    
    return days;
  });

  function prevMonth() {
    currentMonth = new Date(currentMonth.getFullYear(), currentMonth.getMonth() - 1, 1);
  }

  function nextMonth() {
    currentMonth = new Date(currentMonth.getFullYear(), currentMonth.getMonth() + 1, 1);
  }

  function selectDate(date: Date) {
    const dateStr = toISODateString(date);

    // If starting fresh or both dates already selected
    if (!startDate || (startDate && endDate)) {
      startDate = dateStr;
      endDate = '';
    } else if (startDate && !endDate) {
      if (dateStr < startDate) {
        // If clicked date is before start date, make it the new start date
        startDate = dateStr;
      } else {
        // Otherwise, it is the end date
        endDate = dateStr;
        isOpen = false; // Range complete, close popover
      }
    }
  }

  function handleCellMouseEnter(date: Date) {
    if (startDate && !endDate) {
      hoveredDate = toISODateString(date);
    } else {
      hoveredDate = null;
    }
  }

  function handleOutsideClick(e: MouseEvent) {
    if (isOpen && containerEl && !containerEl.contains(e.target as Node)) {
      isOpen = false;
    }
  }
</script>

<svelte:window onclick={handleOutsideClick} />

<div class="relative w-full" bind:this={containerEl}>
  <!-- Trigger Input Button -->
  <button
    type="button"
    onclick={() => isOpen = !isOpen}
    class="flex items-center justify-between w-full h-12 px-4 rounded-md border border-border bg-card text-sm text-left text-foreground hover:border-primary/50 focus:outline-none focus:ring-1 focus:ring-primary focus:shadow-[0_0_10px_rgba(255,42,122,0.4)] transition-all cursor-pointer"
  >
    <span class={startDate ? 'text-white font-medium' : 'text-muted-foreground'}>
      {displayValue || placeholder}
    </span>
    <CalendarIcon class="h-4 w-4 text-muted-foreground" />
  </button>

  <!-- Popover Panel -->
  {#if isOpen}
    <div 
      class="absolute z-50 left-0 right-0 mt-2 p-4 bg-[#0B0C10] border border-secondary/50 rounded-xl shadow-[0_0_20px_rgba(0,230,184,0.2)] animate-in fade-in slide-in-from-top-2 duration-150 max-w-[320px] mx-auto md:max-w-none"
    >
      <!-- Navigation Header -->
      <div class="flex items-center justify-between mb-4">
        <button 
          type="button" 
          onclick={prevMonth}
          class="p-1 text-secondary hover:text-secondary-foreground hover:bg-secondary/10 border border-secondary/20 rounded transition-colors"
        >
          <ChevronLeft class="h-4 w-4" />
        </button>
        <span class="text-xs font-bold tracking-widest text-secondary font-heading">
          {monthNames[currentMonth.getMonth()]} {currentMonth.getFullYear()}
        </span>
        <button 
          type="button" 
          onclick={nextMonth}
          class="p-1 text-secondary hover:text-secondary-foreground hover:bg-secondary/10 border border-secondary/20 rounded transition-colors"
        >
          <ChevronRight class="h-4 w-4" />
        </button>
      </div>

      <!-- Week Days Header -->
      <div class="grid grid-cols-7 gap-1 text-center mb-2">
        {#each weekDays as day}
          <span class="text-[9px] font-bold text-muted-foreground tracking-wider">{day}</span>
        {/each}
      </div>

      <!-- Days Grid -->
      <div class="grid grid-cols-7 gap-1">
        {#each gridDays as day}
          {@const dateStr = toISODateString(day.date)}
          {@const isStart = startDate === dateStr}
          {@const isEnd = endDate === dateStr}
          {@const isSelected = isStart || isEnd}
          
          <!-- Determine range highlight states -->
          {@const isInRange = startDate && endDate && dateStr > startDate && dateStr < endDate}
          {@const isHoverRange = startDate && !endDate && hoveredDate && dateStr > startDate && dateStr <= hoveredDate}
          {@const isHighlight = isInRange || isHoverRange}
          
          <button
            type="button"
            onclick={() => selectDate(day.date)}
            onmouseenter={() => handleCellMouseEnter(day.date)}
            class="
              aspect-square flex items-center justify-center text-[10px] font-semibold rounded transition-all cursor-pointer relative
              {day.isCurrentMonth ? 'text-white' : 'text-muted-foreground/40'}
              {isHighlight ? 'bg-secondary/15 text-secondary border border-dashed border-secondary/35 shadow-[0_0_8px_rgba(0,230,184,0.1)] rounded-none' : ''}
              {isSelected ? 'bg-primary text-white font-bold border border-primary shadow-[0_0_10px_rgba(255,42,122,0.6)] rounded z-10' : ''}
              {!isSelected && !isHighlight ? 'hover:bg-secondary/20 hover:text-secondary border border-transparent hover:border-secondary/30' : ''}
            "
          >
            {day.date.getDate()}
          </button>
        {/each}
      </div>
    </div>
  {/if}
</div>
