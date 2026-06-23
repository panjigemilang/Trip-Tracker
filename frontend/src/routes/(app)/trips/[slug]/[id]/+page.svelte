<script lang="ts">
  import { api } from '$lib/services/api/client';
  import { journeyStore } from '$lib/stores/journey.svelte';
  import { Button } from '$lib/components/ui/button';
  import { Input } from '$lib/components/ui/input';
  import { Label } from '$lib/components/ui/label';
  import CyberCard from '$lib/components/shared/CyberCard.svelte';
  import NeonText from '$lib/components/shared/NeonText.svelte';
  import StatusBadge from '$lib/components/shared/StatusBadge.svelte';
  import DatePicker from '$lib/components/shared/DatePicker.svelte';
  import TimePicker from '$lib/components/shared/TimePicker.svelte';
  import ImageGallery from '$lib/components/shared/ImageGallery.svelte';
  import { 
    Calendar,
    Clock, 
    MapPin, 
    ArrowLeft, 
    Plus, 
    Navigation, 
    AlertCircle, 
    Image as ImageIcon,
    FileText,
    CheckCircle,
    X,
    Bell,
    Check,
    Loader
  } from 'lucide-svelte';
  import { goto } from '$app/navigation';
  import { toast } from 'svelte-sonner';
  import { formatDate, formatTime } from '$lib/utils/dateFormatter';
  import { fade, fly } from 'svelte/transition';
  import { cubicOut } from 'svelte/easing';

  let { data } = $props<{ data: { trip: any } }>();
  let trip = $state(data.trip);

  const isUpcoming = $derived.by(() => {
    if (!trip.start_date) return false;
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const start = new Date(trip.start_date);
    start.setHours(0, 0, 0, 0);
    return start > today;
  });

  // New Activity form fields
  let activityTitle = $state('');
  let activityDate = $state('');
  let activityTime = $state('');
  let activityLocation = $state('');
  let activityNotes = $state('');
  let isSubmitting = $state(false);
  let error = $state('');
  let isFormOpen = $state(false);

  // Gallery state
  let isGalleryOpen = $state(false);
  let galleryImages = $state<string[]>([]);
  let galleryIndex = $state(0);

  function openGallery(images: any[], index: number) {
    galleryImages = images.map(img => getMediaUrl(img.path));
    galleryIndex = index;
    isGalleryOpen = true;
  }



  // Get image URL
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

  // Reload trip data
  async function reloadTrip() {
    try {
      const res = await api.get<any>(`/trips/${trip.id}`);
      trip = res.data;
    } catch (e) {
      console.error('Failed to reload trip:', e);
    }
  }

  // Start or resume journey tracking
  async function handleStartJourney() {
    try {
      if (trip.journey_id) {
        goto('/track');
      } else {
        const journey = await journeyStore.startJourney(trip.id);
        toast.success('Journey tracking initialized successfully.');
        goto('/track');
      }
    } catch (e: any) {
      console.error(e);
      toast.error(e.message || 'Failed to initialize journey tracking.');
    }
  }

  // Handle activity save
  async function saveActivity(addAnother = false) {
    if (!activityTitle || !activityDate || !activityTime) {
      error = 'Title, Date, and Time are required.';
      return;
    }

    isSubmitting = true;
    error = '';

    try {
      await api.post(`/trips/${trip.id}/activities`, {
        title: activityTitle,
        date: activityDate,
        time: activityTime,
        location: activityLocation || null,
        notes: activityNotes || null
      });

      toast.success('New segment initialized.');
      await reloadTrip();

      // Reset form fields
      activityTitle = '';
      activityDate = '';
      activityTime = '';
      activityLocation = '';
      activityNotes = '';

      if (!addAnother) {
        isFormOpen = false;
      }
    } catch (e: any) {
      console.error(e);
      error = e.message || 'Failed to create new segment.';
    } finally {
      isSubmitting = false;
    }
  }

  $effect(() => {
    if (isFormOpen && window.innerWidth < 768) {
      document.body.style.overflow = 'hidden';
    } else {
      document.body.style.overflow = '';
    }
    return () => {
      document.body.style.overflow = '';
    };
  });

  const totalActivities = $derived(trip.activities?.length || 0);
  const completedActivities = $derived(
    trip.journey?.activities 
      ? trip.journey.activities.filter((act: any) => act.status === 'completed').length
      : 0
  );
  const progressPercentage = $derived(
    totalActivities === 0 ? 0 : (completedActivities / totalActivities) * 100
  );
</script>

<svelte:head>
  <title>{trip.title} | Trip Details</title>
</svelte:head>

<div class="flex flex-col h-full max-w-5xl mx-auto md:py-8 gap-6 md:gap-10 pb-20">
  <!-- Desktop Header -->
  <header class="hidden md:flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-border/50 pb-6">
    <div class="flex items-center gap-4">
      <a href="/trips" class="text-muted-foreground hover:text-foreground">
        <ArrowLeft class="h-6 w-6" />
      </a>
      <div>
        <div class="flex items-center gap-3 mb-1">
          <NeonText class="text-3xl font-bold tracking-widest uppercase">{trip.title}</NeonText>
          <StatusBadge status={trip.status} />
        </div>
        <p class="text-xs text-muted-foreground uppercase tracking-widest">
          {#if trip.start_date}
            {formatDate(trip.start_date)}
            {#if trip.end_date} — {formatDate(trip.end_date)}{/if}
          {:else}
            Dates TBD
          {/if}
        </p>
      </div>
    </div>

    <!-- Active Tracking controls -->
    <div class="flex gap-4 font-mono">
      {#if trip.status === 'completed'}
        <div class="h-12 flex items-center gap-2 border border-green-500/30 bg-green-500/10 text-green-400 font-bold uppercase tracking-widest px-6 rounded-lg text-xs">
          <CheckCircle class="h-4 w-4" />
          Journey Completed
        </div>
      {:else if trip.status === 'cancelled'}
        <div class="h-12 flex items-center gap-2 border border-red-500/30 bg-red-500/10 text-red-400 font-bold uppercase tracking-widest px-6 rounded-lg text-xs">
          <X class="h-4 w-4" />
          Journey Cancelled
        </div>
      {:else if trip.journey_id}
        <Button 
          onclick={handleStartJourney}
          class="h-12 bg-[#2A0818] border border-primary text-primary hover:bg-primary hover:text-primary-foreground font-bold tracking-widest uppercase px-6 shadow-none hover:shadow-[0_0_15px_rgba(255,42,122,0.6)]"
        >
          <Navigation class="h-4 w-4 mr-2" />
          Resume Tracking
        </Button>
      {:else if isUpcoming}
        <Button 
          disabled
          class="h-12 bg-muted border border-border text-muted-foreground font-bold tracking-widest uppercase px-6 cursor-not-allowed opacity-60"
        >
          <Clock class="h-4 w-4 mr-2" />
          Start Journey (Upcoming)
        </Button>
      {:else}
        <Button 
          onclick={handleStartJourney}
          class="h-12 bg-[#2A0818] border border-primary text-primary hover:bg-primary hover:text-primary-foreground font-bold tracking-widest uppercase px-6 shadow-none hover:shadow-[0_0_15px_rgba(255,42,122,0.6)]"
        >
          <Navigation class="h-4 w-4 mr-2" />
          Start Journey
        </Button>
      {/if}
    </div>
  </header>

  <!-- Desktop Description -->
  {#if trip.description}
    <CyberCard class="hidden md:block p-6 border-border/50 bg-slate-950/20">
      <div class="flex items-center gap-2 mb-2">
        <FileText class="h-4 w-4 text-primary" />
        <span class="text-[10px] uppercase tracking-widest text-primary font-bold">Expedition_Brief</span>
      </div>
      <p class="text-sm text-muted-foreground leading-relaxed">{trip.description}</p>
    </CyberCard>
  {/if}

  <!-- Mobile Layout Header & Floating Card -->
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
          {#if trip.status === 'active' || trip.journey_id}ACTIVE EXPEDITION{:else}{trip.status}{/if}
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

      <div class="flex items-center justify-between font-mono text-[10px] uppercase tracking-widest mb-2">
        <span class="text-muted-foreground">PROGRESS</span>
        <span class="text-muted-foreground">ACTIVITIES</span>
      </div>
      {#if trip.journey_id || trip.journey}
        <div class="flex items-center gap-4">
          <div class="flex-1 h-2 bg-[#16171E] rounded-full overflow-hidden">
            <div class="h-full bg-primary shadow-[0_0_8px_rgba(255,42,122,0.8)]" style="width: {progressPercentage}%"></div>
          </div>
          <div class="shrink-0 text-xs font-bold font-mono">
            <span class="text-yellow-500">{totalActivities} FOUND</span>
          </div>
        </div>
      {:else}
        <div class="flex items-center justify-between gap-4">
          <span class="text-xs text-muted-foreground italic">Telemetry offline. Start journey to track progress.</span>
          <span class="bg-amber-500/10 border border-amber-500/30 text-amber-500 px-2 py-0.5 rounded text-[8px] uppercase tracking-widest font-bold font-mono">Not Started</span>
        </div>
      {/if}
    </CyberCard>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-[1fr_400px] gap-8 items-start">
    <!-- Left Column: Activity List / Timeline -->
    <div class="flex flex-col gap-6">
      <!-- Desktop Header -->
      <div class="hidden md:flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="w-1 h-5 bg-secondary"></div>
          <h2 class="text-lg font-bold tracking-widest uppercase text-white">Route Segments</h2>
        </div>
        
        {#if !isFormOpen}
          <Button 
            size="sm" 
            onclick={() => { isFormOpen = true; }} 
            class="bg-secondary/10 border border-secondary text-secondary hover:bg-secondary hover:text-secondary-foreground"
          >
            <Plus class="h-4 w-4 mr-1" /> Add Segment
          </Button>
        {/if}
      </div>

      <!-- Mobile Header -->
      <div class="md:hidden flex items-center gap-4 mt-2 mb-2">
        <div class="w-8 h-px bg-primary/60"></div>
        <h2 class="text-[11px] font-bold tracking-[0.2em] uppercase text-white">Sequence Timeline</h2>
      </div>

      {#if !trip.activities || trip.activities.length === 0}
        <div class="text-center py-16 bg-slate-950/10 border border-dashed border-border rounded-xl">
          <p class="text-sm text-muted-foreground mb-4">No active checkpoints scheduled for this route.</p>
          <Button onclick={() => { isFormOpen = true; }} variant="outline" class="border-secondary text-secondary hover:bg-secondary/10">
            Initialize First Segment
          </Button>
        </div>
      {:else}
        <!-- Desktop Timeline -->
        <div class="hidden md:block relative pl-6 border-l border-border/50 space-y-6 py-2">
          {#each trip.activities as act}
            <div class="relative">
              <!-- Timeline Marker -->
              <div class="absolute -left-9 top-1 h-5 w-5 rounded-full border-2 border-secondary bg-[#0B0C10] flex items-center justify-center shadow-[0_0_8px_rgba(0,230,184,0.3)]">
                <div class="h-1.5 w-1.5 rounded-full bg-secondary"></div>
              </div>

              <CyberCard class="p-5 hover:border-secondary/50 transition-all duration-300">
                <div class="flex justify-between items-start gap-4 mb-2">
                  <div>
                    <h3 class="text-base font-bold text-white uppercase tracking-wider">{act.title}</h3>
                    <div class="flex items-center gap-4 text-xs text-muted-foreground mt-1">
                      <span class="flex items-center gap-1">
                        <Calendar class="h-3 w-3" /> {formatDate(act.date)}
                      </span>
                      <span class="flex items-center gap-1">
                        <Clock class="h-3 w-3" /> {formatTime(act.time)}
                      </span>
                    </div>
                  </div>
                  {#if act.location}
                    <span class="flex items-center gap-1 text-xs text-secondary bg-secondary/5 border border-secondary/20 px-2 py-0.5 rounded uppercase tracking-wider">
                      <MapPin class="h-3 w-3" /> {act.location}
                    </span>
                  {/if}
                </div>

                {#if act.notes}
                  <p class="text-xs text-muted-foreground mt-3 border-t border-border/20 pt-3 leading-relaxed">{act.notes}</p>
                {/if}

                <!-- Activity Images -->
                {#if act.images && act.images.length > 0}
                  <div class="grid grid-cols-3 gap-3 mt-4">
                    {#each act.images as img, i}
                      <!-- svelte-ignore a11y_click_events_have_key_events -->
                      <!-- svelte-ignore a11y_no_static_element_interactions -->
                      <div 
                        class="relative aspect-video rounded-lg overflow-hidden border border-border/50 hover:border-primary/50 hover:scale-[1.02] hover:shadow-[0_0_15px_rgba(255,42,122,0.3)] transition-all cursor-pointer"
                        onclick={() => openGallery(act.images, i)}
                      >
                        <img src={getMediaUrl(img.path)} alt={img.original_name} class="w-full h-full object-cover" />
                      </div>
                    {/each}
                  </div>
                {/if}
              </CyberCard>
            </div>
          {/each}
        </div>
        
        <!-- Mobile Timeline -->
        <div class="md:hidden relative space-y-6">
          <div class="absolute left-5 top-4 bottom-4 w-px bg-[#1F2937] z-0"></div>
          {#each trip.activities as act, i}
            {@const journeyAct = trip.journey?.activities?.find((a: any) => a.id === act.id)}
            {@const status = journeyAct?.status || 'upcoming'}
            {@const isCompleted = status === 'completed'}
            {@const isActive = status === 'current'}
            {@const isUpcoming = status === 'upcoming' || status === 'skipped' || status === 'cancelled'}
            
            <div class="flex gap-4 relative z-10">
              <div class="flex flex-col items-center shrink-0 w-10">
                {#if isCompleted}
                  <div class="h-10 w-10 rounded-xl border-[1.5px] border-primary bg-[#0B0C10] flex items-center justify-center relative mt-1">
                    <Check class="h-4 w-4" stroke="var(--color-neon-pink)" strokeWidth={3} />
                  </div>
                  {#if i !== trip.activities.length - 1}
                    <div class="absolute top-11 left-1/2 -translate-x-1/2 -bottom-6 w-px bg-primary shadow-[0_0_8px_rgba(255,42,122,0.6)] z-[-1]"></div>
                  {/if}
                {:else if isActive}
                  <div class="h-10 w-10 rounded-xl border-[1.5px] border-secondary bg-[#0B0C10] flex items-center justify-center relative mt-1 shadow-[0_0_12px_rgba(0,230,184,0.4)]">
                    <Loader class="h-4 w-4" stroke="var(--color-neon-cyan)" strokeWidth={2} />
                  </div>
                  {#if i !== trip.activities.length - 1}
                    <div class="absolute top-11 left-1/2 -translate-x-1/2 -bottom-6 w-px bg-secondary shadow-[0_0_8px_rgba(0,230,184,0.6)] z-[-1]"></div>
                  {/if}
                {:else}
                  <div class="h-10 w-10 rounded-full border border-muted-foreground/30 bg-[#16171E] flex items-center justify-center text-muted-foreground/40 relative mt-1">
                    <Clock class="h-4 w-4" />
                  </div>
                {/if}
              </div>

              <div class="flex-1 min-w-0">
                {#if isCompleted}
                  <div class="flex items-center justify-between mb-1.5 mt-0.5">
                    <span class="text-[12px] font-mono text-muted-foreground/80"><span class="text-white">{formatDate(act.date)}</span> • {formatTime(act.time)}</span>
                    <span class="text-[8px] border border-primary/30 text-primary px-2 py-0.5 rounded-sm uppercase tracking-widest font-bold bg-primary/10">COMPLETED</span>
                  </div>
                  <h3 class="text-[16px] font-bold text-white mb-1.5">{act.title}</h3>
                  {#if act.notes || act.location}
                    <p class="text-[12px] text-muted-foreground leading-relaxed italic">{act.notes || `Activity at ${act.location}`}</p>
                  {/if}
                {:else if isActive}
                  <CyberCard class="p-4 border-secondary/50 bg-[#11131A] relative shadow-[0_0_15px_rgba(0,230,184,0.1)] rounded-xl">
                    <div class="flex items-center justify-between mb-3">
                      <span class="text-[12px] font-mono text-secondary font-bold"><span class="text-white">{formatDate(act.date)}</span> • {formatTime(act.time)}</span>
                      <span class="text-[9px] border border-secondary text-secondary px-2 py-0.5 rounded-sm uppercase tracking-widest font-bold">ACTIVE</span>
                    </div>
                    <h3 class="text-[17px] font-bold text-white mb-2">{act.title}</h3>
                    {#if act.notes || act.location}
                      <p class="text-[12px] text-muted-foreground leading-relaxed">{act.notes || `Infiltrating data node at ${act.location}.`}</p>
                    {/if}
                  </CyberCard>
                {:else}
                  <div class="flex items-center justify-between mb-1.5 mt-0.5">
                    <span class="text-[12px] font-mono text-muted-foreground/50"><span class="text-white/70">{formatDate(act.date)}</span> • {formatTime(act.time)}</span>
                    <span class="text-[8px] border border-muted-foreground/20 text-muted-foreground/60 px-2 py-0.5 rounded-sm uppercase tracking-widest font-bold bg-muted-foreground/5">UPCOMING</span>
                  </div>
                  <h3 class="text-[16px] font-bold text-white/70 mb-1.5">{act.title}</h3>
                  {#if act.notes || act.location}
                    <p class="text-[12px] text-muted-foreground/60 leading-relaxed line-clamp-2">{act.notes || `Location: ${act.location}`}</p>
                  {/if}
                {/if}
              </div>
            </div>
          {/each}
          
          <!-- Add new segment button at the bottom for mobile -->
          <div class="flex gap-4 mt-6">
            <div class="w-10 shrink-0 flex justify-center">
              <div class="h-10 w-10 rounded-full border border-dashed border-muted-foreground/40 bg-transparent flex items-center justify-center text-muted-foreground/50">
                <Plus class="h-4 w-4" />
              </div>
            </div>
            <div class="flex-1 min-w-0 flex items-center">
              <Button
                variant="outline"
                size="lg"
                onclick={() => { isFormOpen = true; }}
                class="w-full text-left p-4 border border-dashed border-muted-foreground/30 rounded-xl hover:bg-white/5 transition-colors group flex justify-between items-center"
              >
                <span class="text-xs font-bold uppercase tracking-widest text-muted-foreground group-hover:text-white transition-colors">Add New Segment</span>
                <Plus class="h-4 w-4 text-muted-foreground group-hover:text-white transition-colors" />
              </Button>
            </div>
          </div>
        </div>
      {/if}
    </div>

    <!-- Right Column: Add Segment Form -->
    <div class="hidden md:flex flex-col gap-6">
      {#if isFormOpen}
        <CyberCard class="p-6 border-secondary/50" glowState="secondary">
          <div class="flex items-center justify-between mb-6">
            <h3 class="text-sm font-bold tracking-widest text-secondary uppercase">Initialize Segment</h3>
            <button onclick={() => { isFormOpen = false; }} class="text-muted-foreground hover:text-foreground text-xs uppercase tracking-widest">Close</button>
          </div>

          {#if error}
            <div class="p-3 border border-red-500/30 bg-red-500/5 text-red-500 text-xs rounded-lg flex items-start gap-2 mb-4">
              <AlertCircle class="h-4 w-4 shrink-0 mt-0.5" />
              <span>{error}</span>
            </div>
          {/if}

          {@render activityFormBody()}
        </CyberCard>
      {/if}
    </div>
  </div>

  <!-- Mobile Create Activity Drawer -->
  {#if isFormOpen}
    <div class="md:hidden fixed inset-0 z-100 w-full h-dvh">
      <!-- Backdrop -->
      <!-- svelte-ignore a11y_click_events_have_key_events -->
      <!-- svelte-ignore a11y_no_static_element_interactions -->
      <div 
        class="absolute inset-0 bg-[#0B0C10]/80 backdrop-blur-sm" 
        transition:fade={{ duration: 200 }} 
        onclick={() => { isFormOpen = false; }}
      ></div>
      
      <!-- Drawer Content -->
      <div 
        class="absolute inset-x-0 bottom-0 top-12 bg-[#06080E] flex flex-col overflow-y-auto rounded-t-2xl shadow-[0_-10px_40px_rgba(255,42,122,0.15)] border-t border-border/50"
        transition:fly={{ y: '100%', duration: 300, easing: cubicOut }}
      >
        <header class="flex items-center justify-between p-4 border-b border-border/20 sticky top-0 bg-[#06080E]/90 backdrop-blur z-10 rounded-t-2xl">
          <button onclick={() => { isFormOpen = false; }} class="text-muted-foreground hover:text-white transition-colors p-2 -ml-2">
            <ArrowLeft class="h-5 w-5" />
          </button>
          <NeonText class="text-sm font-bold tracking-widest uppercase text-secondary">NEW SEQUENCE</NeonText>
          <div class="w-9 h-9"></div>
        </header>

        <div class="flex-1 p-6 flex flex-col gap-6">
          <div>
            <h1 class="text-3xl font-bold tracking-widest text-white font-heading uppercase">Add Segment</h1>
            <p class="text-xs text-muted-foreground mt-2">Input telemetry data for your next journey.</p>
          </div>
          
          {#if error}
            <div class="p-3 border border-red-500/30 bg-red-500/5 text-red-500 text-xs rounded-lg flex items-start gap-2">
              <AlertCircle class="h-4 w-4 shrink-0 mt-0.5" />
              <span>{error}</span>
            </div>
          {/if}
          
          {@render activityFormBody()}
        </div>
      </div>
    </div>
  {/if}

  <ImageGallery bind:isOpen={isGalleryOpen} images={galleryImages} bind:currentIndex={galleryIndex} />
</div>

{#snippet activityFormBody()}
  <div class="space-y-4">
    <div class="space-y-2">
      <Label for="act_title" class="text-[10px] uppercase tracking-widest text-secondary font-semibold">Segment Name <span class="text-red-500">*</span></Label>
      <Input id="act_title" placeholder="e.g. Shibuya Cyber Grid" class="bg-card border-border" bind:value={activityTitle} required />
    </div>

    <div class="grid grid-cols-2 gap-4">
      <div class="space-y-2">
        <Label for="act_date" class="text-[10px] uppercase tracking-widest text-secondary font-semibold">Date <span class="text-red-500">*</span></Label>
        <DatePicker 
          bind:value={activityDate} 
          min={trip.start_date} 
          max={trip.end_date} 
          placeholder="Date" 
        />
      </div>
      <div class="space-y-2">
        <Label for="act_time" class="text-[10px] uppercase tracking-widest text-secondary font-semibold">Time <span class="text-red-500">*</span></Label>
        <TimePicker bind:value={activityTime} />
      </div>
    </div>

    <div class="space-y-2">
      <Label for="act_location" class="text-[10px] uppercase tracking-widest text-secondary font-semibold">Location</Label>
      <Input id="act_location" placeholder="e.g. Chuo City Sector 4" class="bg-card border-border" bind:value={activityLocation} />
    </div>

    <div class="space-y-2">
      <Label for="act_notes" class="text-[10px] uppercase tracking-widest text-secondary font-semibold">Notes</Label>
      <textarea 
        id="act_notes"
        placeholder="Specific segment instructions..." 
        class="w-full h-24 rounded-md border border-input bg-card px-3 py-2 text-xs focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring focus-visible:shadow-[0_0_10px_rgba(0,230,184,0.4)] transition-all resize-none"
        bind:value={activityNotes}
      ></textarea>
    </div>

    <!-- Action buttons -->
    <div class="flex flex-col gap-3 pt-4 border-t border-border/50">
      <Button 
        onclick={() => saveActivity(false)} 
        disabled={isSubmitting}
        class="w-full h-11 bg-[#2A0818] border border-primary text-primary hover:bg-primary hover:text-primary-foreground font-bold tracking-widest uppercase animate-scale"
      >
        {isSubmitting ? 'Processing...' : 'Submit Segment'}
      </Button>
      
      <Button 
        variant="outline" 
        onclick={() => saveActivity(true)} 
        disabled={isSubmitting}
        class="w-full h-11 border-secondary/50 text-secondary hover:bg-secondary/10 hover:text-secondary font-bold tracking-widest uppercase animate-scale"
      >
        Submit & Add Next
      </Button>
    </div>
  </div>
{/snippet}
