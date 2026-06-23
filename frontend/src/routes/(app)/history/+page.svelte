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

  function getSkippedCheckpoints(trip: any) {
    if (!trip.journey || !trip.journey.activities) return 0;
    return trip.journey.activities.filter((act: any) => act.status === 'skipped').length;
  }

  function getMissedCheckpoints(trip: any) {
    if (!trip.journey || !trip.journey.activities) return 0;
    return trip.journey.activities.filter((act: any) => act.status === 'missed').length;
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
  <header class="md:hidden pt-4 pb-2">
    <div class="flex items-start justify-between">
      <div>
        <h1 class="text-[28px] font-bold tracking-widest text-white">History Log</h1>
        <p class="text-sm text-muted-foreground">Past and expired journeys.</p>
      </div>
      {#if histories.length > 0}
        <button 
          onclick={handleClearAll}
          class="flex items-center gap-1 text-[10px] text-primary uppercase tracking-widest font-semibold mt-2"
        >
          <Trash2 class="h-3.5 w-3.5" /> CLEAR ALL
        </button>
      {/if}
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
        {@const statusColor = trip.status === 'completed' ? 'secondary' : (trip.status === 'cancelled' ? 'muted' : 'primary')}
        {@const statusBorderClass = trip.status === 'completed' ? 'border-secondary' : (trip.status === 'cancelled' ? 'border-muted-foreground/30' : 'border-primary')}
        {@const statusShadowClass = trip.status === 'completed' ? 'shadow-[0_0_15px_rgba(0,230,184,0.1)]' : (trip.status === 'cancelled' ? 'shadow-none' : 'shadow-[0_0_15px_rgba(255,42,122,0.1)]')}
        
        <CyberCard class="p-5 {statusBorderClass} {statusShadowClass} bg-[#0B0C10] relative overflow-hidden" glowState={statusColor === 'muted' ? 'none' : statusColor}>
          <div class="flex items-start justify-between mb-2">
            <div>
              <h3 class="text-xl font-bold tracking-wider text-white mb-1">{trip.title}</h3>
              <p class="text-xs {trip.status === 'completed' ? 'text-secondary' : (trip.status === 'cancelled' ? 'text-muted-foreground' : 'text-primary')} font-medium tracking-wide">
                {#if trip.start_date}
                  {formatDate(trip.start_date)} {#if trip.end_date} - {formatDate(trip.end_date)}{/if}
                {:else}
                  No Dates Set
                {/if}
              </p>
            </div>
            
            <!-- Custom Badges based on status -->
            {#if trip.status === 'completed'}
              <span class="text-[9px] border border-secondary text-secondary px-2 py-0.5 uppercase tracking-widest font-bold rounded-sm bg-secondary/5">COMPLETED</span>
            {:else if trip.status === 'cancelled'}
              <span class="text-[9px] border border-red-500/50 text-red-500 px-2 py-0.5 uppercase tracking-widest font-bold rounded-sm bg-red-500/5">CANCELLED</span>
            {:else}
              <span class="text-[9px] bg-[#2A0818] text-primary px-2 py-0.5 uppercase tracking-widest font-bold rounded-sm">EXPIRED</span>
            {/if}
          </div>
          
          {#if trip.status === 'cancelled'}
            <!-- Cancelled State Content -->
            <div class="bg-[#11131A] rounded border border-border flex flex-col items-center justify-center p-6 my-5 font-mono">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground mb-3"><circle cx="12" cy="12" r="10"/><path d="m4.9 4.9 14.2 14.2"/></svg>
              <div class="text-[9px] text-muted-foreground uppercase tracking-widest text-center">Trip cancelled before commencement</div>
            </div>
          {:else}
            <!-- Completed / Expired Stats -->
            <div class="grid grid-cols-2 gap-3 my-5 font-mono">
              <!-- Completed Block -->
              <div class="bg-[#11131A] rounded p-3 flex flex-col items-center justify-center border {trip.status === 'completed' ? 'border-border' : 'border-border'}">
                <div class="h-6 w-6 rounded-full flex items-center justify-center mb-1 {trip.status === 'completed' ? 'bg-secondary/20 text-secondary' : 'bg-muted text-muted-foreground'}">
                  <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
                <div class="text-xl font-bold text-white">{getCompletedCheckpoints(trip)}</div>
                <div class="text-[8px] text-muted-foreground uppercase tracking-widest">Completed</div>
              </div>
              
              <!-- Skipped / Missed Block -->
              <div class="bg-[#11131A] rounded p-3 flex flex-col items-center justify-center border {trip.status === 'completed' ? 'border-border' : 'border-border'}">
                <div class="h-6 w-6 rounded-full flex items-center justify-center mb-1 {trip.status === 'completed' ? 'bg-muted text-muted-foreground' : 'bg-primary/20 text-primary'}">
                  {#if trip.status === 'completed'}
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="5 4 15 12 5 20 5 4"/><line x1="19" y1="5" x2="19" y2="19"/></svg>
                  {:else}
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                  {/if}
                </div>
                <div class="text-xl font-bold text-white">{trip.status === 'completed' ? getSkippedCheckpoints(trip) : getMissedCheckpoints(trip)}</div>
                <div class="text-[8px] text-muted-foreground uppercase tracking-widest">{trip.status === 'completed' ? 'Skipped' : 'Missed'}</div>
              </div>
            </div>
          {/if}
          
          <div class="flex items-center justify-between border-t border-border/50 pt-4">
            <button 
              onclick={() => handleDelete(trip.id)} 
              class="flex items-center gap-1.5 text-xs text-muted-foreground hover:text-white transition-colors uppercase tracking-widest font-mono"
            >
              <Trash2 class="h-3.5 w-3.5" /> Delete
            </button>
            <a href="/trips/{trip.slug}/{trip.id}" class="text-xs {trip.status === 'completed' ? 'text-secondary' : (trip.status === 'cancelled' ? 'text-white' : 'text-primary')} font-bold tracking-widest uppercase flex items-center gap-1 font-mono">
              VIEW DETAILS <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
            </a>
          </div>
        </CyberCard>
      {:else}
        <div class="text-center py-12 border border-dashed border-border rounded-xl">
          <p class="text-muted-foreground text-xs font-mono uppercase tracking-widest">No matching history records.</p>
        </div>
      {/each}

      {#if filteredHistories.length > 0}
        <div class="text-center mt-4">
          <span class="text-[10px] text-muted-foreground/60 uppercase tracking-widest font-mono">END OF HISTORY LOG</span>
        </div>
      {/if}
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

