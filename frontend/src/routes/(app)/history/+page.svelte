<script lang="ts">
  import CyberCard from '$lib/components/shared/CyberCard.svelte';
  import NeonText from '$lib/components/shared/NeonText.svelte';
  import StatusBadge from '$lib/components/shared/StatusBadge.svelte';
  import { Button } from '$lib/components/ui/button';
  import { Trash2 } from 'lucide-svelte';
  import { onMount } from 'svelte';
  import { api } from '$lib/services/api/client';
  import { toast } from 'svelte-sonner';
  import { formatDate } from '$lib/utils/dateFormatter';

  let histories = $state<any[]>([]);
  let isLoading = $state(true);
  let currentFilter = $state<'ALL' | 'COMPLETED' | 'FAILED'>('ALL');

  async function fetchHistory() {
    isLoading = true;
    try {
      const res = await api.get<{ data: any[] }>('/history');
      histories = res.data;
    } catch (e: any) {
      toast.error('Failed to load journey history.');
    } finally {
      isLoading = false;
    }
  }

  onMount(() => {
    fetchHistory();
  });

  async function handleDelete(tripId: string) {
    if (!confirm('Are you sure you want to delete this archive record?')) return;
    try {
      await api.delete(`/history/${tripId}`);
      toast.success('Archive record deleted.');
      fetchHistory();
    } catch (e: any) {
      toast.error('Failed to delete history record.');
    }
  }

  async function handleClearAll() {
    if (!confirm('Are you sure you want to purge all archive records? This action is permanent.')) return;
    try {
      for (const trip of histories) {
        await api.delete(`/history/${trip.id}`);
      }
      toast.success('All history purged.');
      fetchHistory();
    } catch (e: any) {
      toast.error('Failed to clear history.');
    }
  }

  let filteredHistories = $derived.by(() => {
    if (currentFilter === 'COMPLETED') {
      return histories.filter(h => h.status === 'completed');
    }
    if (currentFilter === 'FAILED') {
      return histories.filter(h => h.status === 'cancelled');
    }
    return histories;
  });

  function getCompletedCheckpoints(trip: any) {
    if (!trip.journey || !trip.journey.activities) return 0;
    return trip.journey.activities.filter((act: any) => act.status === 'completed').length;
  }

  function getMediaUrl(path: string) {
    if (!path) return '';
    if (path.startsWith('http')) return path;
    return `http://localhost:8000/${path}`;
  }

  function getTripImage(trip: any) {
    if (trip.first_image_url) {
      return getMediaUrl(trip.first_image_url);
    }
    return 'https://images.unsplash.com/photo-1542931287-023b922fa89b?q=80&w=400';
  }


</script>

<svelte:head>
  <title>History Log | Plan Trip Tracker</title>
</svelte:head>

<div class="flex flex-col gap-6 md:gap-8 pb-10 max-w-6xl mx-auto font-sans">
  <!-- Mobile Header -->
  <header class="md:hidden pt-4 pb-2 border-b border-border/50">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold tracking-widest text-white">History Log</h1>
        <p class="text-sm text-muted-foreground">Past and expired journeys.</p>
      </div>
      {#if histories.length > 0}
        <button 
          onclick={handleClearAll}
          class="flex items-center gap-1 text-xs text-red-500 uppercase tracking-widest font-semibold bg-red-500/10 px-3 py-1.5 rounded-md border border-red-500/30"
        >
          <Trash2 class="h-3 w-3" /> Clear All
        </button>
      {/if}
    </div>
    
    <div class="flex gap-2 mt-4 overflow-x-auto pb-2">
      <Button 
        onclick={() => currentFilter = 'ALL'} 
        size="sm"
        class={currentFilter === 'ALL' ? 'bg-primary text-primary-foreground hover:bg-primary/80' : 'border-border text-muted-foreground hover:text-white bg-transparent'}
      >
        ALL
      </Button>
      <Button 
        onclick={() => currentFilter = 'COMPLETED'} 
        size="sm"
        class={currentFilter === 'COMPLETED' ? 'bg-primary text-primary-foreground hover:bg-primary/80' : 'border-border text-muted-foreground hover:text-white bg-transparent'}
      >
        COMPLETED
      </Button>
      <Button 
        onclick={() => currentFilter = 'FAILED'} 
        size="sm"
        class={currentFilter === 'FAILED' ? 'bg-primary text-primary-foreground hover:bg-primary/80' : 'border-border text-muted-foreground hover:text-white bg-transparent'}
      >
        FAILED
      </Button>
    </div>
  </header>

  <!-- Desktop Header & Stats -->
  <div class="hidden md:flex flex-col gap-4">
    <div class="flex items-center justify-between border-b border-border/50 pb-6">
      <div class="flex items-center gap-4">
        <div class="w-1 h-6 bg-primary"></div>
        <div>
          <h2 class="text-sm font-bold tracking-widest text-primary mb-1 uppercase">Records Division</h2>
          <h1 class="text-4xl font-bold tracking-widest text-white">Journey History</h1>
        </div>
      </div>
      <div class="flex gap-2">
        <Button 
          onclick={() => currentFilter = 'ALL'} 
          class={currentFilter === 'ALL' ? 'bg-primary text-primary-foreground hover:bg-primary/80' : 'border-border text-muted-foreground hover:text-white bg-transparent'}
        >
          ALL MISSIONS
        </Button>
        <Button 
          onclick={() => currentFilter = 'COMPLETED'} 
          class={currentFilter === 'COMPLETED' ? 'bg-primary text-primary-foreground hover:bg-primary/80' : 'border-border text-muted-foreground hover:text-white bg-transparent'}
        >
          COMPLETED
        </Button>
        <Button 
          onclick={() => currentFilter = 'FAILED'} 
          class={currentFilter === 'FAILED' ? 'bg-primary text-primary-foreground hover:bg-primary/80' : 'border-border text-muted-foreground hover:text-white bg-transparent'}
        >
          FAILED
        </Button>
        
        {#if histories.length > 0}
          <Button 
            onclick={handleClearAll}
            class="bg-[#2A0818] border border-red-500 text-red-500 hover:bg-red-500 hover:text-white font-bold uppercase tracking-widest"
          >
            Clear All
          </Button>
        {/if}
      </div>
    </div>
    
    <p class="text-muted-foreground max-w-2xl">Reviewing logged expeditions across the sectors. Archives are synced with the central database.</p>
  </div>

  <!-- Loading State -->
  {#if isLoading}
    <div class="flex justify-center py-20">
      <div class="w-10 h-10 border-2 border-primary/20 border-t-primary rounded-full animate-spin"></div>
    </div>
  {:else}
    <!-- Mobile List -->
    <div class="md:hidden flex flex-col gap-6">
      {#each filteredHistories as trip}
        <CyberCard class="p-5 border-secondary/30 bg-linear-to-b from-secondary/5 to-transparent relative overflow-hidden">
          <div class="absolute top-0 right-0 p-4">
            <StatusBadge status={trip.status} />
          </div>
          <h3 class="text-xl font-bold tracking-wider text-white mb-1">{trip.title}</h3>
          <p class="text-xs text-secondary font-medium tracking-wide mb-6">
            {#if trip.start_date}
              {formatDate(trip.start_date)} {#if trip.end_date} - {formatDate(trip.end_date)}{/if}
            {:else}
              No Dates Set
            {/if}
          </p>
          
          <div class="grid grid-cols-2 gap-3 mb-6 font-mono">
            <div class="bg-background rounded-lg p-3 flex flex-col items-center justify-center border border-border">
              <div class="h-6 w-6 rounded-full bg-secondary/20 flex items-center justify-center mb-1">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--color-neon-cyan)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
              </div>
              <div class="text-xl font-bold">{getCompletedCheckpoints(trip)}</div>
              <div class="text-[8px] text-muted-foreground uppercase tracking-widest">Completed</div>
            </div>
            <div class="bg-background rounded-lg p-3 flex flex-col items-center justify-center border border-border">
              <div class="h-6 w-6 rounded-full bg-muted flex items-center justify-center mb-1">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground"><polyline points="9 18 15 12 9 6"/></svg>
              </div>
              <div class="text-xl font-bold">{trip.activities?.length || 0}</div>
              <div class="text-[8px] text-muted-foreground uppercase tracking-widest">Total</div>
            </div>
          </div>
          
          <div class="flex items-center justify-between border-t border-border/50 pt-4">
            <button 
              onclick={() => handleDelete(trip.id)} 
              class="flex items-center gap-1 text-xs text-red-500 hover:text-red-400 transition-colors uppercase tracking-widest font-mono font-semibold"
            >
              <Trash2 class="h-3 w-3" /> Delete
            </button>
            <a href="/trips/{trip.slug}/{trip.id}" class="text-xs text-secondary font-bold tracking-widest uppercase flex items-center gap-1">
              View Details <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
            </a>
          </div>
        </CyberCard>
      {:else}
        <div class="text-center py-12 border border-dashed border-border rounded-xl">
          <p class="text-muted-foreground text-xs font-mono uppercase tracking-widest">No matching history records.</p>
        </div>
      {/each}
    </div>

    <!-- Desktop List -->
    <div class="hidden md:flex flex-col gap-4">
      {#each filteredHistories as trip}
        <CyberCard class="p-6 flex flex-row items-center gap-6 group hover:border-secondary/50">
          <div class="h-28 w-48 rounded-lg overflow-hidden shrink-0 relative border border-border">
            <div class="absolute inset-0 bg-secondary/10 group-hover:bg-transparent transition-colors z-10"></div>
            <img src={getTripImage(trip)} alt={trip.title} class="h-full w-full object-cover grayscale opacity-70 group-hover:grayscale-0 transition-all" />
          </div>
          
          <div class="flex-1">
            <div class="flex items-center justify-between mb-2 font-mono">
              <div class="text-[10px] text-muted-foreground uppercase tracking-widest">ID: {trip.slug || 'TRIP'} / EXPEDITION</div>
              <StatusBadge status={trip.status} />
            </div>
            
            <h3 class="text-2xl font-bold tracking-wider text-white mb-4">{trip.title}</h3>
            
            <div class="flex items-center gap-12 font-mono">
              <div>
                <div class="text-[10px] text-muted-foreground uppercase tracking-widest mb-1">Timeline</div>
                <div class="text-sm font-medium">
                  {#if trip.start_date}
                    {formatDate(trip.start_date)} {#if trip.end_date} — {formatDate(trip.end_date)}{/if}
                  {:else}
                    Dates TBD
                  {/if}
                </div>
              </div>
              <div>
                <div class="text-[10px] text-muted-foreground uppercase tracking-widest mb-1">Checkpoints</div>
                <div class="text-sm font-medium">{getCompletedCheckpoints(trip)} / {trip.activities?.length || 0}</div>
              </div>
              <div class="flex-1">
                <div class="text-[10px] text-muted-foreground uppercase tracking-widest mb-1">Status</div>
                <div class="h-1.5 w-full bg-muted rounded-full overflow-hidden">
                  <div class="h-full bg-secondary shadow-[0_0_8px_rgba(0,230,184,0.8)]" style="width: {trip.activities?.length ? (getCompletedCheckpoints(trip) / trip.activities.length) * 100 : 0}%"></div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="pl-6 border-l border-border flex flex-col gap-2 justify-center shrink-0">
            <Button href="/trips/{trip.slug}/{trip.id}" variant="outline" class="w-full whitespace-nowrap border-primary/30 text-primary hover:bg-primary/10 hover:text-primary tracking-widest uppercase font-semibold text-xs py-5">View Details</Button>
            <Button onclick={() => handleDelete(trip.id)} variant="ghost" class="w-full text-red-500 hover:bg-red-500/10 tracking-widest uppercase font-semibold text-xs py-5 flex items-center justify-center gap-2">
              <Trash2 class="h-4 w-4" /> Delete Record
            </Button>
          </div>
        </CyberCard>
      {:else}
        <div class="text-center py-16 border border-dashed border-border rounded-xl">
          <p class="text-muted-foreground font-mono uppercase tracking-widest">No matching records found in database.</p>
        </div>
      {/each}
    </div>
  {/if}
</div>

