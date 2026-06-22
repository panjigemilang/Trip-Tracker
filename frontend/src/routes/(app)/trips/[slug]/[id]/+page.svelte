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
    FileText
  } from 'lucide-svelte';
  import { goto } from '$app/navigation';
  import { toast } from 'svelte-sonner';
  import { formatDate, formatTime } from '$lib/utils/dateFormatter';

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
</script>

<svelte:head>
  <title>{trip.title} | Trip Details</title>
</svelte:head>

<div class="flex flex-col h-full max-w-5xl mx-auto md:py-8 gap-6 md:gap-10 pb-20">
  <!-- Header -->
  <header class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-border/50 pb-6">
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
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
          Journey Completed
        </div>
      {:else if trip.status === 'cancelled'}
        <div class="h-12 flex items-center gap-2 border border-red-500/30 bg-red-500/10 text-red-400 font-bold uppercase tracking-widest px-6 rounded-lg text-xs">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
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

  <!-- Description -->
  {#if trip.description}
    <CyberCard class="p-6 border-border/50 bg-slate-950/20">
      <div class="flex items-center gap-2 mb-2">
        <FileText class="h-4 w-4 text-primary" />
        <span class="text-[10px] uppercase tracking-widest text-primary font-bold">Expedition_Brief</span>
      </div>
      <p class="text-sm text-muted-foreground leading-relaxed">{trip.description}</p>
    </CyberCard>
  {/if}

  <div class="grid grid-cols-1 md:grid-cols-[1fr_400px] gap-8 items-start">
    <!-- Left Column: Activity List / Timeline -->
    <div class="flex flex-col gap-6">
      <div class="flex items-center justify-between">
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

      {#if !trip.activities || trip.activities.length === 0}
        <div class="text-center py-16 bg-slate-950/10 border border-dashed border-border rounded-xl">
          <p class="text-sm text-muted-foreground mb-4">No active checkpoints scheduled for this route.</p>
          <Button onclick={() => { isFormOpen = true; }} variant="outline" class="border-secondary text-secondary hover:bg-secondary/10">
            Initialize First Segment
          </Button>
        </div>
      {:else}
        <div class="relative pl-6 border-l border-border/50 space-y-6 py-2">
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
      {/if}
    </div>

    <!-- Right Column: Add Segment Form -->
    <div class="flex flex-col gap-6">
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
                class="w-full h-11 bg-[#2A0818] border border-primary text-primary hover:bg-primary hover:text-primary-foreground font-bold tracking-widest uppercase"
              >
                {isSubmitting ? 'Processing...' : 'Submit Segment'}
              </Button>
              
              <Button 
                variant="outline" 
                onclick={() => saveActivity(true)} 
                disabled={isSubmitting}
                class="w-full h-11 border-secondary/50 text-secondary hover:bg-secondary/10 hover:text-secondary font-bold tracking-widest uppercase"
              >
                Submit & Add Next
              </Button>
            </div>
          </div>
        </CyberCard>
      {/if}
    </div>
  </div>

<ImageGallery bind:isOpen={isGalleryOpen} images={galleryImages} bind:currentIndex={galleryIndex} /></div>
