<script lang="ts">
  import { onMount, onDestroy } from 'svelte';
  import { api } from '$lib/services/api/client';
  import { journeyStore } from '$lib/stores/journey.svelte';
  import CyberCard from '$lib/components/shared/CyberCard.svelte';
  import NeonText from '$lib/components/shared/NeonText.svelte';
  import { Button } from '$lib/components/ui/button';
  import { toast } from 'svelte-sonner';
  import { goto } from '$app/navigation';
  import { formatDate, formatTime, parseLocalDate } from '$lib/utils/dateFormatter';

  function isToday(dateStr: string | null | undefined): boolean {
    if (!dateStr) return false;
    try {
      const date = parseLocalDate(dateStr);
      const today = new Date();
      return date.getFullYear() === today.getFullYear() &&
             date.getMonth() === today.getMonth() &&
             date.getDate() === today.getDate();
    } catch (e) {
      return false;
    }
  }
  import { 
    MapPin, 
    Navigation, 
    SkipForward, 
    XCircle, 
    CheckCircle, 
    Clock, 
    AlertCircle,
    Calendar,
    Sparkles
  } from 'lucide-svelte';

  let isLoading = $state(true);
  let activeTrip = $state<any>(null);
  let journey = $state<any>(null);
  let observations = $state('');
  let pollInterval: any = null;

  async function loadActiveJourney() {
    try {
      // Find the first active trip
      const tripsRes = await api.get<{ data: any[] }>('/trips?status=active');
      const activeTrips = tripsRes.data || [];
      
      if (activeTrips.length > 0) {
        activeTrip = activeTrips[0];
        if (activeTrip.journey) {
          const journeyRes = await api.get<{ data: any }>(`/journeys/${activeTrip.journey.id}`);
          journey = journeyRes.data;
          journeyStore.activeJourney = journey;
        } else {
          journey = null;
        }
      } else {
        // If no active trip, check if there is an upcoming trip that starts today
        const allTripsRes = await api.get<{ data: any[] }>('/trips');
        const allTrips = allTripsRes.data || [];
        
        const todayTrip = allTrips.find((t: any) => {
          if (t.status === 'completed' || t.status === 'cancelled') return false;
          return isToday(t.start_date);
        });

        if (todayTrip) {
          if (todayTrip.journey_id) {
            const journeyRes = await api.get<{ data: any }>(`/journeys/${todayTrip.journey_id}`);
            journey = journeyRes.data;
            journeyStore.activeJourney = journey;
            activeTrip = todayTrip;
          } else {
            const newJourney = await journeyStore.startJourney(todayTrip.id);
            journey = newJourney;
            journeyStore.activeJourney = newJourney;
            
            const tripRes = await api.get<any>(`/trips/${todayTrip.id}`);
            activeTrip = tripRes.data;
            toast.success(`Journey '${activeTrip.title}' automatically initialized for today.`);
          }
        } else {
          activeTrip = null;
          journey = null;
        }
      }
    } catch (e) {
      console.error('Failed to load active tracking coordinates:', e);
    } finally {
      isLoading = false;
    }
  }

  onMount(async () => {
    await loadActiveJourney();
    
    // Poll every 15 seconds to fetch updates automatically
    pollInterval = setInterval(async () => {
      if (journey?.id) {
        try {
          const res = await api.get<{ data: any }>(`/journeys/${journey.id}`);
          journey = res.data;
          journeyStore.activeJourney = journey;
        } catch (e) {
          console.error('Failed to sync active tracking telemetry:', e);
        }
      }
    }, 15000);
  });

  onDestroy(() => {
    if (pollInterval) clearInterval(pollInterval);
  });

  // Local observation persistence
  $effect(() => {
    if (journey?.id) {
      observations = localStorage.getItem(`journey_log_${journey.id}`) || '';
    }
  });

  function handleSaveObservations() {
    if (journey?.id) {
      localStorage.setItem(`journey_log_${journey.id}`, observations);
      toast.success('Observations logged to digital core.');
    }
  }

  async function handleUpdateStatus(activityId: string, status: string) {
    try {
      await journeyStore.updateActivityStatus(activityId, status as any);
      toast.success(`Activity status updated.`);
      await loadActiveJourney();
    } catch (e: any) {
      toast.error('Failed to execute activity status update.');
    }
  }

  // Helper functions
  function getMediaUrl(path: string) {
    if (!path) return '';
    if (path.startsWith('http')) return path;
    return `http://localhost:8000/${path}`;
  }

  function getTripImage(trip: any) {
    if (trip?.first_image_url) {
      return getMediaUrl(trip.first_image_url);
    }
    return 'https://images.unsplash.com/photo-1542931287-023b922fa89b?q=80&w=1200';
  }



  // Reactive derived values
  let activities = $derived(journey?.trip?.activities || []);
  
  let journeyActivitiesMap = $derived.by(() => {
    const map: Record<string, any> = {};
    if (journey?.journey_activities) {
      for (const ja of journey.journey_activities) {
        map[ja.activity_id] = ja;
      }
    }
    return map;
  });

  function getActivityStatus(activityId: string): string {
    return journeyActivitiesMap[activityId]?.status || 'upcoming';
  }

  let currentActivity = $derived.by(() => {
    // Return first activity with status = 'current'
    const found = activities.find((a: any) => getActivityStatus(a.id) === 'current');
    if (found) return found;
    // Fallback: first activity with status = 'upcoming'
    return activities.find((a: any) => getActivityStatus(a.id) === 'upcoming') || null;
  });

  let estCompletionTime = $derived.by(() => {
    if (activities.length === 0) return 'TBD';
    const lastAct = activities[activities.length - 1];
    return formatTime(lastAct.time);
  });

  let dayCount = $derived.by(() => {
    if (!journey?.started_at) return 1;
    const start = new Date(journey.started_at);
    return Math.max(1, Math.floor((new Date().getTime() - start.getTime()) / (24 * 3600 * 1000)) + 1);
  });
</script>

<svelte:head>
  <title>Journey Tracker | Plan Trip Tracker</title>
</svelte:head>

<div class="flex flex-col min-h-[calc(100vh-8rem)] md:min-h-[calc(100vh-4rem)] max-w-7xl mx-auto md:p-6 w-full gap-6 pb-20 font-sans">
  {#if isLoading}
    <div class="flex flex-col items-center justify-center py-20 gap-4">
      <div class="w-10 h-10 border-2 border-primary/20 border-t-primary rounded-full animate-spin"></div>
      <span class="text-xs font-mono uppercase tracking-widest text-muted-foreground">SCANNING COGNITIVE STREAM...</span>
    </div>
  {:else if !journey}
    <!-- Empty State -->
    <div class="grow flex flex-col items-center justify-center p-8 text-center max-w-lg mx-auto py-16">
      <div class="w-16 h-16 rounded-full border border-dashed border-primary/40 bg-primary/5 flex items-center justify-center mb-6 animate-pulse">
        <Navigation class="w-8 h-8 text-primary" />
      </div>
      <h2 class="text-xl font-bold tracking-widest text-white uppercase mb-2 font-heading">NO ACTIVE EXPEDITION SIGNAL</h2>
      <p class="text-sm text-muted-foreground mb-8">Establish a journey coordinates sequence from your Trips sector to initialize live telemetry and track routing metrics.</p>
    </div>
  {:else}
    <!-- Live Tracking Banner if completed -->
    {#if journey.status === 'completed'}
      <div class="bg-secondary/10 border border-secondary text-secondary p-4 rounded-xl text-center shadow-[0_0_15px_rgba(0,230,184,0.15)]">
        <h2 class="font-bold text-lg mb-1 uppercase tracking-widest">Expedition Completed 🎉</h2>
        <p class="text-xs font-mono">All route coordinates verified and archived.</p>
      </div>
    {/if}

    <!-- Live Tracking Header -->
    <header class="flex flex-col gap-2 border-b border-border/50 pb-6">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
          <div class="hidden md:block w-1 h-6 bg-secondary"></div>
          <div>
            <h2 class="text-xs font-bold tracking-widest text-secondary font-mono uppercase mb-0.5">
              DAY {dayCount} • {activeTrip.location || 'EXPEDITION TELEMETRY'}
            </h2>
            <h1 class="text-3xl font-bold tracking-widest text-white font-heading uppercase">
              {activeTrip.title}
            </h1>
          </div>
        </div>
        
        <div class="flex flex-col items-end text-right font-mono">
          <span class="text-[8px] text-muted-foreground uppercase tracking-widest mb-1">Est. Completion</span>
          <span class="text-xl font-bold text-white">{estCompletionTime}</span>
        </div>
      </div>
    </header>

    <!-- Content Layout -->
    <div class="flex flex-col md:flex-row gap-8 items-start">
      <!-- Left Column: Route activities (Desktop only) -->
      <div class="hidden md:flex flex-col gap-6 w-80 shrink-0 border-r border-border/50 pr-6">
        <div class="flex items-center gap-2 mb-4">
          <div class="w-1 h-5 bg-secondary"></div>
          <h2 class="text-sm font-bold tracking-widest text-secondary uppercase font-heading">Route Coordinates</h2>
        </div>
        
        <div class="relative flex-1">
          <!-- Timeline Line -->
          <div class="absolute left-3 top-3 bottom-3 w-0.5 bg-border/40 z-0"></div>
          
          <div class="space-y-6 relative z-10 font-mono">
            {#each activities as activity}
              {@const status = getActivityStatus(activity.id)}
              {@const isCurrent = status === 'current'}
              {@const isPast = status === 'completed' || status === 'skipped' || status === 'cancelled' || status === 'missed'}
              
              <div class="flex gap-4">
                <!-- Timeline Dot -->
                <div class="relative flex items-center justify-center shrink-0">
                  {#if isCurrent}
                    <div class="h-6.5 w-6.5 rounded-full border-2 border-primary bg-background flex items-center justify-center shadow-[0_0_10px_rgba(255,42,122,0.8)] animate-pulse">
                      <div class="h-2 w-2 rounded-full bg-primary"></div>
                    </div>
                  {:else if isPast}
                    <div class="h-6 w-6 rounded-full border-2 border-muted-foreground/30 bg-background flex items-center justify-center">
                      <div class="h-1.5 w-1.5 rounded-full bg-muted-foreground/50"></div>
                    </div>
                  {:else}
                    <div class="h-6 w-6 rounded-full border border-border bg-card flex items-center justify-center"></div>
                  {/if}
                </div>
                
                <!-- Card contents -->
                <div class="flex-1 min-w-0">
                  <div class="text-[9px] uppercase tracking-widest mb-0.5 {isCurrent ? 'text-primary font-bold animate-pulse' : 'text-muted-foreground'}">
                    {formatTime(activity.time)} {#if isCurrent}— CURRENT{/if}
                  </div>
                  <h4 class="text-xs font-bold truncate {isCurrent ? 'text-white' : isPast ? 'text-muted-foreground/80 line-through' : 'text-white/80'}">
                    {activity.title}
                  </h4>
                  {#if activity.location}
                    <p class="text-[9px] text-muted-foreground truncate mt-0.5 flex items-center gap-0.5">
                      <MapPin class="w-2.5 h-2.5 shrink-0" /> {activity.location}
                    </p>
                  {/if}
                </div>
              </div>
            {/each}
          </div>
        </div>
      </div>

      <!-- Right Column: Visualizer, Logs & Controls (Desktop) -->
      <div class="hidden md:flex flex-1 flex-col gap-6">
        {#if currentActivity}
          {@const activeStatus = getActivityStatus(currentActivity.id)}
          <CyberCard class="aspect-video relative overflow-hidden p-0 border-primary/30" glowState="primary">
            <!-- Background Image -->
            <img 
              src={getTripImage(journey.trip)} 
              alt={currentActivity.title} 
              class="absolute inset-0 w-full h-full object-cover opacity-35 grayscale"
            />
            <div class="absolute inset-0 bg-linear-to-t from-[#0B0C10] via-transparent to-[#0B0C10]/40 z-10"></div>
            
            <div class="absolute bottom-6 left-6 z-20">
              <div class="flex items-center gap-1.5 text-secondary text-[10px] font-bold uppercase tracking-widest mb-1.5">
                <MapPin class="h-3.5 w-3.5" />
                <span>{currentActivity.location || 'EXPEDITION FIELD'}</span>
              </div>
              <h2 class="text-3xl md:text-4xl font-bold tracking-wider text-white font-heading">{currentActivity.title}</h2>
            </div>
            
            <!-- Active Metrics Overlay (Immersive design decoration) -->
            <div class="absolute top-6 right-6 z-20 w-56 p-4 bg-background/85 backdrop-blur-md border border-border/80 rounded-xl font-mono">
              <div class="text-[9px] text-primary font-bold uppercase tracking-widest mb-3 flex items-center gap-1">
                <Sparkles class="h-3 w-3" /> active_telemetry
              </div>
              <div class="grid grid-cols-2 gap-3">
                <div>
                  <div class="text-[8px] text-muted-foreground uppercase tracking-widest">Est. Time</div>
                  <div class="text-lg font-bold text-white">{formatTime(currentActivity.time)}</div>
                </div>
                <div>
                  <div class="text-[8px] text-muted-foreground uppercase tracking-widest">GPS Drift</div>
                  <div class="text-lg font-bold text-secondary">0 <span class="text-[10px] font-normal text-white">m</span></div>
                </div>
                <div class="col-span-2">
                  <div class="text-[8px] text-muted-foreground uppercase tracking-widest">Target Sector</div>
                  <div class="text-xs font-bold text-yellow-500 truncate">{currentActivity.location || 'TBD'}</div>
                </div>
              </div>
            </div>
          </CyberCard>
          
          <!-- Logging & Controls -->
          <div class="grid grid-cols-2 gap-6 items-stretch">
            <!-- Digital Log -->
            <div class="flex flex-col gap-2">
              <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold tracking-widest uppercase font-mono text-white">// Digital_Log</span>
                {#if observations !== localStorage.getItem(`journey_log_${journey.id}`)}
                  <button onclick={handleSaveObservations} class="text-[9px] text-secondary font-bold hover:underline font-mono">SAVE LOG</button>
                {/if}
              </div>
              <textarea 
                bind:value={observations}
                placeholder="Input active activity observations and tactical logs..." 
                class="flex-1 min-h-32 rounded-lg border border-border bg-card/50 px-3 py-2 text-xs focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-primary focus-visible:shadow-[0_0_10px_rgba(255,42,122,0.3)] transition-all resize-none font-mono"
              ></textarea>
              <div class="flex gap-2">
                <span class="text-[8px] px-2 py-1 bg-[#16171E] border border-border rounded text-muted-foreground uppercase tracking-widest font-mono font-semibold">#expedition</span>
                <span class="text-[8px] px-2 py-1 bg-[#16171E] border border-border rounded text-muted-foreground uppercase tracking-widest font-mono font-semibold">#live_telemetry</span>
              </div>
            </div>
            
            <!-- Controls Card -->
            <CyberCard class="flex flex-col justify-center gap-4 border-dashed border-border bg-transparent p-6 h-full">
              <p class="text-[10px] text-muted-foreground font-mono leading-relaxed text-center mb-1">
                System is currently tracking this activity in high-fidelity mode. Mark complete to advance routing coordinates sequence.
              </p>
              
              <div class="flex flex-col gap-2">
                <Button 
                  onclick={() => handleUpdateStatus(currentActivity.id, 'completed')}
                  class="w-full h-11 bg-secondary text-secondary-foreground hover:bg-secondary/80 font-bold uppercase tracking-widest font-mono"
                >
                  ✓ Complete Activity
                </Button>
                
                <div class="flex gap-2">
                  <Button 
                    onclick={() => handleUpdateStatus(currentActivity.id, 'skipped')}
                    variant="outline" 
                    class="flex-1 h-10 border-border text-muted-foreground hover:text-white font-bold uppercase tracking-widest font-mono text-xs"
                  >
                    <SkipForward class="mr-1.5 h-3.5 w-3.5" /> Skip
                  </Button>
                  <Button 
                    onclick={() => handleUpdateStatus(currentActivity.id, 'cancelled')}
                    variant="outline" 
                    class="flex-1 h-10 border-red-500/30 text-red-500 hover:bg-red-500/10 hover:text-red-400 font-bold uppercase tracking-widest font-mono text-xs"
                  >
                    <XCircle class="mr-1.5 h-3.5 w-3.5" /> Cancel
                  </Button>
                </div>
              </div>
            </CyberCard>
          </div>
        {/if}
      </div>

      <!-- Mobile Timeline & Embedded Controls layout -->
      <div class="md:hidden w-full flex flex-col gap-6">
        <div class="relative pl-[2px]">
          <!-- Vertical timeline line -->
          <div class="absolute left-[21px] top-4 bottom-4 w-px bg-primary z-0 shadow-[0_0_8px_rgba(255,42,122,0.6)]"></div>
          
          <div class="space-y-8 relative z-10">
            {#each activities as activity}
              {@const status = getActivityStatus(activity.id)}
              {@const isCurrent = status === 'current'}
              {@const isPast = status === 'completed' || status === 'skipped' || status === 'cancelled' || status === 'missed'}
              
              <div class="flex gap-5">
                <!-- Left side timeline column -->
                <div class="flex flex-col items-center shrink-0 w-10">
                  <!-- Dot icon -->
                  {#if isCurrent}
                    <div class="h-10 w-10 rounded-[10px] border-2 border-primary bg-background flex items-center justify-center shadow-[0_0_12px_rgba(255,42,122,0.8)] z-10 relative mt-1">
                      <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--color-neon-pink)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/></svg>
                    </div>
                    <span class="text-[11px] font-bold text-primary font-mono tracking-widest mt-2">
                      {formatTime(activity.time)}
                    </span>
                  {:else if isPast}
                    <div class="h-10 w-10 rounded-full border border-dashed border-muted-foreground/30 bg-background flex items-center justify-center text-muted-foreground/50 z-10 relative mt-1">
                      <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/></svg>
                    </div>
                    <span class="text-[11px] text-muted-foreground/70 font-mono tracking-widest mt-2">
                      {formatTime(activity.time)}
                    </span>
                  {:else}
                    <div class="h-10 w-10 rounded-full border border-muted-foreground/30 bg-background flex items-center justify-center text-muted-foreground/30 z-10 relative mt-1">
                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    </div>
                    <span class="text-[11px] text-white font-mono tracking-widest mt-2">
                      {formatTime(activity.time)}
                    </span>
                  {/if}
                </div>
                
                <!-- Card details -->
                <div class="flex-1 min-w-0">
                  {#if isCurrent}
                    <!-- Active Card styled with pink neon border and controls -->
                    <CyberCard class="p-4 border-primary bg-[#0B0C10] relative overflow-hidden shadow-[0_0_15px_rgba(255,42,122,0.15)] rounded-[12px]" glowState="primary">
                      <div class="flex items-center justify-between mb-4 font-mono">
                        <span class="text-[9px] bg-primary/20 text-primary px-2.5 py-1 rounded-sm uppercase tracking-widest font-bold flex items-center gap-1.5">
                          <div class="w-1.5 h-1.5 bg-primary rounded-full animate-pulse"></div> TRACKING
                        </span>
                        <span class="text-[10px] text-blue-400 font-bold tracking-widest bg-blue-500/10 border border-blue-500/30 px-2.5 py-1 rounded-sm">
                          {formatTime(activity.time)}
                        </span>
                      </div>
                      
                      <h3 class="text-[17px] font-bold text-white tracking-wide mb-2 leading-snug">
                        {activity.title}
                      </h3>
                      
                      {#if activity.location}
                        <p class="text-xs text-muted-foreground flex items-center gap-1 mb-5 font-mono">
                          {activity.location}
                        </p>
                      {/if}
                      
                      {#if activity.notes}
                        <div class="bg-[#11131A] border border-secondary/20 rounded-lg p-3.5 mb-5 font-mono text-[10px] leading-relaxed">
                          <div class="text-secondary font-bold uppercase tracking-widest mb-1.5">NOTES</div>
                          <p class="text-muted-foreground/80">{activity.notes}</p>
                        </div>
                      {/if}
                      
                      <!-- Action controls -->
                      <div class="flex gap-3 pt-1 font-mono">
                        <Button 
                          onclick={() => handleUpdateStatus(activity.id, 'skipped')}
                          variant="outline" 
                          class="flex-1 h-11 bg-[#16171E] border-border text-white hover:text-white font-bold uppercase tracking-widest text-[11px] rounded-md"
                        >
                          <SkipForward class="mr-1.5 h-3.5 w-3.5" /> SKIP
                        </Button>
                        <Button 
                          onclick={() => handleUpdateStatus(activity.id, 'cancelled')}
                          variant="outline" 
                          class="flex-1 h-11 bg-[#16171E] border-red-500/30 text-red-500 hover:bg-red-500/10 hover:text-red-400 font-bold uppercase tracking-widest text-[11px] rounded-md"
                        >
                          <XCircle class="mr-1.5 h-3.5 w-3.5" /> CANCEL
                        </Button>
                      </div>
                    </CyberCard>
                  {:else}
                    <!-- Non-active Card simple styled -->
                    <CyberCard class="p-4 border-border/40 bg-[#16171E] opacity-75 rounded-[12px] h-full flex flex-col justify-center">
                      <div class="flex items-start justify-between gap-4">
                        <div class="flex flex-col gap-1.5">
                          <h3 class="text-[15px] font-bold leading-snug {isPast ? 'text-muted-foreground/60 line-through' : 'text-white'}">
                            {activity.title}
                          </h3>
                          {#if activity.location}
                            <p class="text-[11px] text-muted-foreground/70 truncate font-mono">
                              {activity.location}
                            </p>
                          {/if}
                        </div>
                        
                        <div class="shrink-0 mt-0.5">
                          {#if !isPast}
                            <span class="text-[10px] text-yellow-500/80 font-bold tracking-widest bg-yellow-500/10 border border-yellow-500/30 px-2 py-1 rounded-sm font-mono">
                              {formatTime(activity.time)}
                            </span>
                          {:else}
                            <span class="text-[10px] text-muted-foreground/50 font-bold tracking-widest bg-muted-foreground/5 border border-muted-foreground/20 px-2 py-1 rounded-sm font-mono">
                              {formatTime(activity.time)}
                            </span>
                          {/if}
                        </div>
                      </div>
                    </CyberCard>
                  {/if}
                </div>
              </div>
            {/each}
          </div>
        </div>
      </div>
    </div>
  {/if}
</div>
