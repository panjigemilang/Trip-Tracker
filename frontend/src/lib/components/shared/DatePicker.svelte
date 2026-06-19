<script lang="ts">
  import { Calendar as CalendarIcon, ChevronLeft, ChevronRight } from 'lucide-svelte';
  import { onMount } from 'svelte';

  let { 
    value = $bindable(''), 
    min = '', 
    max = '', 
    placeholder = 'Select date...' 
  } = $props<{
    value: string;
    min?: string;
    max?: string;
    placeholder?: string;
  }>();

  let isOpen = $state(false);
  let containerEl = $state<HTMLDivElement | null>(null);
  
  // Initialize current month view to selected date or today
  let currentMonth = $state(new Date());
  
  $effect(() => {
    if (value) {
      const parsed = new Date(value);
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

  // Helper to format date as YYYY-MM-DD
  function toISODateString(date: Date) {
    const y = date.getFullYear();
    const m = String(date.getMonth() + 1).padStart(2, '0');
    const d = String(date.getDate()).padStart(2, '0');
    return `${y}-${m}-${d}`;
  }

  // Display date formatter (e.g. "Jun 20, 2026")
  let displayValue = $derived.by(() => {
    if (!value) return '';
    const date = new Date(value);
    if (isNaN(date.getTime())) return '';
    return date.toLocaleDateString(undefined, { 
      year: 'numeric', 
      month: 'short', 
      day: 'numeric' 
    });
  });

  // Check if date is disabled based on min/max constraints
  function isDateDisabled(date: Date) {
    const dateStr = toISODateString(date);
    if (min && dateStr < min.substring(0, 10)) return true;
    if (max && dateStr > max.substring(0, 10)) return true;
    return false;
  }

  // Generate grid days for calendar
  let gridDays = $derived.by(() => {
    const year = currentMonth.getFullYear();
    const month = currentMonth.getMonth();
    
    const firstDayIndex = new Date(year, month, 1).getDay();
    const prevMonthDays = new Date(year, month, 0).getDate();
    const currentMonthDays = new Date(year, month + 1, 0).getDate();
    
    const days = [];
    
    // Padding from previous month
    for (let i = firstDayIndex - 1; i >= 0; i--) {
      const d = new Date(year, month - 1, prevMonthDays - i);
      days.push({
        date: d,
        isCurrentMonth: false,
        disabled: isDateDisabled(d)
      });
    }
    
    // Days in current month
    for (let i = 1; i <= currentMonthDays; i++) {
      const d = new Date(year, month, i);
      days.push({
        date: d,
        isCurrentMonth: true,
        disabled: isDateDisabled(d)
      });
    }
    
    // Padding to complete 6 rows (42 days)
    const remaining = 42 - days.length;
    for (let i = 1; i <= remaining; i++) {
      const d = new Date(year, month + 1, i);
      days.push({
        date: d,
        isCurrentMonth: false,
        disabled: isDateDisabled(d)
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
    if (isDateDisabled(date)) return;
    value = toISODateString(date);
    isOpen = false;
  }

  function handleOutsideClick(e: MouseEvent) {
    if (isOpen && containerEl && !containerEl.contains(e.target as Node)) {
      isOpen = false;
    }
  }
</script>

<svelte:window onclick={handleOutsideClick} />

<div class="relative w-full" bind:this={containerEl}>
  <!-- Trigger Button -->
  <button
    type="button"
    onclick={() => isOpen = !isOpen}
    class="flex items-center justify-between w-full h-12 px-4 rounded-md border border-border bg-card text-sm text-left text-foreground hover:border-primary/50 focus:outline-none focus:ring-1 focus:ring-primary focus:shadow-[0_0_10px_rgba(255,42,122,0.4)] transition-all cursor-pointer"
  >
    <span class={value ? 'text-white font-medium' : 'text-muted-foreground'}>
      {displayValue || placeholder}
    </span>
    <CalendarIcon class="h-4 w-4 text-muted-foreground" />
  </button>

  <!-- Popover Panel -->
  {#if isOpen}
    <div 
      class="absolute z-50 left-0 right-0 mt-2 p-4 bg-[#0B0C10] border border-secondary/50 rounded-xl shadow-[0_0_20px_rgba(0,230,184,0.2)] animate-in fade-in slide-in-from-top-2 duration-150 max-w-[320px] mx-auto md:max-w-none"
    >
      <!-- Month/Year Navigation -->
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
          {@const isSelected = value === toISODateString(day.date)}
          <button
            type="button"
            onclick={() => selectDate(day.date)}
            disabled={day.disabled}
            class="
              aspect-square flex items-center justify-center text-[10px] font-semibold rounded transition-all cursor-pointer
              {day.disabled ? 'text-slate-800 cursor-not-allowed bg-transparent' : ''}
              {!day.disabled && !isSelected && day.isCurrentMonth ? 'text-white hover:bg-secondary/20 hover:text-secondary border border-transparent hover:border-secondary/30' : ''}
              {!day.disabled && !isSelected && !day.isCurrentMonth ? 'text-muted-foreground/45 hover:bg-secondary/20 hover:text-secondary border border-transparent' : ''}
              {isSelected ? 'bg-primary text-white font-bold border border-primary shadow-[0_0_10px_rgba(255,42,122,0.6)]' : ''}
            "
          >
            {day.date.getDate()}
          </button>
        {/each}
      </div>
    </div>
  {/if}
</div>
