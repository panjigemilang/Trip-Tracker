<script lang="ts">
  import { Clock } from 'lucide-svelte';
  import { onMount } from 'svelte';

  let { value = $bindable(''), placeholder = 'Select time', disabled = false } = $props<{
    value: string;
    placeholder?: string;
    disabled?: boolean;
  }>();

  let isOpen = $state(false);
  let containerEl = $state<HTMLDivElement | null>(null);

  const hours = Array.from({ length: 24 }, (_, i) => i.toString().padStart(2, '0'));
  const minutes = Array.from({ length: 12 }, (_, i) => (i * 5).toString().padStart(2, '0'));

  let selectedHour = $derived(value ? value.split(':')[0] : '12');
  let selectedMinute = $derived(value ? value.split(':')[1] : '00');

  function setHour(h: string) {
    if (!value) value = `12:00`;
    value = `${h}:${selectedMinute}`;
  }

  function setMinute(m: string) {
    if (!value) value = `12:00`;
    value = `${selectedHour}:${m}`;
    isOpen = false; // Close after selecting minute
  }

  function toggleOpen(e: Event) {
    e.stopPropagation();
    if (!disabled) {
      isOpen = !isOpen;
    }
  }

  onMount(() => {
    function handleClickOutside(event: MouseEvent) {
      if (containerEl && !containerEl.contains(event.target as Node)) {
        isOpen = false;
      }
    }
    document.addEventListener('click', handleClickOutside);
    return () => {
      document.removeEventListener('click', handleClickOutside);
    };
  });
</script>

<div class="relative w-full" bind:this={containerEl}>
  <button
    type="button"
    onclick={toggleOpen}
    {disabled}
    class="flex items-center justify-start w-full h-10 px-3 py-2 rounded-md border border-border bg-card text-sm text-left text-foreground hover:border-primary/50 focus:outline-none focus:ring-1 focus:ring-primary focus:shadow-[0_0_10px_rgba(255,42,122,0.4)] transition-all cursor-pointer {value ? 'text-white font-medium' : 'text-muted-foreground'}"
  >
    <Clock class="mr-2 h-4 w-4 text-secondary shrink-0" />
    <span>{value || placeholder}</span>
  </button>

  {#if isOpen}
    <div class="absolute z-50 mt-1 w-auto p-3 flex gap-2 border border-primary/30 bg-[#0B0C10] shadow-[0_0_20px_rgba(255,42,122,0.15)] rounded-md">
      <div class="flex flex-col items-center">
        <div class="text-[10px] font-semibold text-muted-foreground mb-2 uppercase tracking-widest">Hour</div>
        <div class="h-48 w-16 overflow-y-auto rounded-md border border-border bg-card scrollbar-thin">
          <div class="flex flex-col p-1">
            {#each hours as h}
              <button 
                type="button"
                class="w-full py-1 text-xs text-center rounded-sm transition-colors {selectedHour === h && value ? 'bg-primary/20 text-primary font-bold' : 'text-muted-foreground hover:bg-muted/50 hover:text-foreground'}"
                onclick={() => setHour(h)}
              >
                {h}
              </button>
            {/each}
          </div>
        </div>
      </div>
      
      <div class="flex flex-col items-center">
        <div class="text-[10px] font-semibold text-muted-foreground mb-2 uppercase tracking-widest">Minute</div>
        <div class="h-48 w-16 overflow-y-auto rounded-md border border-border bg-card scrollbar-thin">
          <div class="flex flex-col p-1">
            {#each minutes as m}
              <button 
                type="button"
                class="w-full py-1 text-xs text-center rounded-sm transition-colors {selectedMinute === m && value ? 'bg-secondary/20 text-secondary font-bold' : 'text-muted-foreground hover:bg-muted/50 hover:text-foreground'}"
                onclick={() => setMinute(m)}
              >
                {m}
              </button>
            {/each}
          </div>
        </div>
      </div>
    </div>
  {/if}
</div>

<style>
  .scrollbar-thin::-webkit-scrollbar {
    width: 4px;
  }
  .scrollbar-thin::-webkit-scrollbar-track {
    background: transparent;
  }
  .scrollbar-thin::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 2px;
  }
  .scrollbar-thin::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.2);
  }
</style>
