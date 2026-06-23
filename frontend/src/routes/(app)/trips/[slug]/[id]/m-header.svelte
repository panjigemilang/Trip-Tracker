<script lang="ts">
  import { ArrowLeft, Bell, MapPin } from 'lucide-svelte';
  import NeonText from '$lib/components/shared/NeonText.svelte';
  import CyberCard from '$lib/components/shared/CyberCard.svelte';
  import { formatDate } from '$lib/utils/dateFormatter';

  let { trip, getTripImage, progressPercentage, totalActivities } = $props<{ 
    trip: any, 
    getTripImage: (t: any) => string, 
    progressPercentage: number, 
    totalActivities: number 
  }>();
</script>

<div class="md:hidden w-screen relative left-1/2 -translate-x-1/2 pb-6 -mt-6">
  <!-- Top Nav -->
  <div class="absolute top-0 inset-x-0 z-20 flex items-center justify-between p-4 bg-linear-to-b from-[#0B0C10] to-transparent">
    <div class="flex items-center gap-4">
      <a href="/trips" class="text-white hover:text-white/80 transition-colors bg-black/20 p-2 rounded-full backdrop-blur-md">
        <ArrowLeft class="h-5 w-5" />
      </a>
      <NeonText class="text-lg font-bold tracking-widest uppercase truncate max-w-45">{trip.title}</NeonText>
    </div>
    <div class="flex items-center gap-3">
      <button aria-label="Notifications" class="text-white hover:text-white/80 transition-colors bg-black/20 p-2 rounded-full backdrop-blur-md">
        <Bell class="size-4.5" />
      </button>
    </div>
  </div>

  <!-- Hero Image -->
  <div class="w-full h-64 relative">
    <img src={getTripImage(trip)} alt={trip.title} class="w-full h-full object-cover grayscale opacity-80" />
    <div class="absolute inset-0 bg-linear-to-t from-[#0B0C10] via-transparent to-[#0B0C10]/30"></div>
    
    <!-- Pill -->
    <div class="absolute bottom-16 left-4 z-10">
      <span class="bg-[#2A0818] border border-primary text-primary px-3 py-1 rounded-full text-[9px] uppercase tracking-widest font-bold">
        {#if trip.status === 'active' || trip.journey_id}ACTIVE{:else}{trip.status}{/if}
      </span>
    </div>
  </div>

  <!-- Floating Card -->
  <CyberCard class="mx-4 -mt-12 relative z-10 p-5 bg-[#11131A] border-border/50 shadow-[0_10px_30px_rgba(0,0,0,0.5)] rounded-2xl">
    <div class="flex items-start justify-between mb-2">
      <h2 class="text-2xl font-bold tracking-wide text-white">{trip.title}</h2>
      <div class="h-10 w-10 shrink-0 bg-secondary/10 border border-secondary/30 rounded-xl flex items-center justify-center text-secondary">
        <MapPin class="h-5 w-5" />
      </div>
    </div>
    
    <p class="text-[13px] text-secondary font-medium tracking-wide mb-4">
      {#if trip.start_date}
        {formatDate(trip.start_date)} {#if trip.end_date} — {formatDate(trip.end_date)}{/if}
      {:else}
        Dates TBD
      {/if}
    </p>

    {#if trip.description}
      <p class="text-[13px] text-muted-foreground leading-relaxed mb-6">
        {trip.description}
      </p>
    {/if}

    {#if trip.journey_id || trip.journey}
      <div class="grid grid-cols-2 gap-4">
        <div>
          <div class="font-mono text-[10px] uppercase tracking-widest mb-2 text-muted-foreground">PROGRESS</div>
          <div class="flex items-center gap-2 h-5">
            {#if progressPercentage > 0}
              <div class="flex-1 h-2 bg-[#16171E] rounded-full overflow-hidden">
                <div class="h-full bg-primary shadow-[0_0_8px_rgba(255,42,122,0.8)]" style="width: {progressPercentage}%"></div>
              </div>
            {/if}
            <span class="shrink-0 text-xs font-bold font-mono text-primary">{Math.round(progressPercentage)}%</span>
          </div>
        </div>
        <div class="text-right">
          <div class="font-mono text-[10px] uppercase tracking-widest mb-2 text-muted-foreground">ACTIVITIES</div>
          <div class="text-xs font-bold font-mono text-yellow-500 h-5 flex items-center justify-end">
            {totalActivities} FOUND
          </div>
        </div>
      </div>
    {:else}
      <div class="flex items-center justify-between font-mono text-[10px] uppercase tracking-widest mb-2">
        <span class="text-muted-foreground">PROGRESS</span>
        <span class="text-muted-foreground">ACTIVITIES</span>
      </div>
      <div class="flex items-center justify-between gap-4">
        <span class="text-xs text-muted-foreground italic">Telemetry offline. Start journey to track progress.</span>
        <span class="bg-amber-500/10 border border-amber-500/30 text-amber-500 px-2 py-0.5 rounded text-[8px] uppercase tracking-widest font-bold font-mono">Not Started</span>
      </div>
    {/if}
  </CyberCard>
</div>
