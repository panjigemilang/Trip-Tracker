<script lang="ts">
  import { Button } from '$lib/components/ui/button';
  import { Input } from '$lib/components/ui/input';
  import { Label } from '$lib/components/ui/label';
  import NeonText from '$lib/components/shared/NeonText.svelte';
  import StatusBadge from '$lib/components/shared/StatusBadge.svelte';
  import ImageSlotUploader from '$lib/components/features/trips/ImageSlotUploader.svelte';
  import { Calendar, MapPin, Rocket, ArrowLeft, AlertCircle } from 'lucide-svelte';
  import { api } from '$lib/services/api/client';
  import { goto } from '$app/navigation';

  let tripTitle = $state('');
  let activityTitle = $state('');
  let date = $state('');
  let time = $state('');
  let location = $state('');
  let notes = $state('');
  let isSubmitting = $state(false);
  let error = $state('');

  async function handleCreateTrip(e: Event) {
    e.preventDefault();
    if (!tripTitle || !activityTitle || !date || !time) {
      error = 'Missing parameters: Title, Activity, Date, and Time are required.';
      return;
    }
    
    isSubmitting = true;
    error = '';
    
    try {
      // 1. Create Trip
      const tripRes = await api.post<any>('/trips', {
        title: tripTitle,
        description: notes,
        status: 'planned'
      });
      
      const tripId = tripRes.data.id;
      
      // 2. Create first Activity
      await api.post<any>(`/trips/${tripId}/activities`, {
        title: activityTitle,
        date: date,
        time: time,
        location: location || null,
        notes: notes || null,
        sort_order: 1
      });
      
      goto('/trips');
    } catch (err: any) {
      console.error(err);
      error = err.message || 'Failed to initialize trip protocol.';
    } finally {
      isSubmitting = false;
    }
  }
</script>

<div class="flex flex-col h-full max-w-5xl mx-auto md:py-8 gap-6 md:gap-10">
  <!-- Mobile Header -->
  <header class="md:hidden flex items-center justify-between py-4">
    <a href="/trips" class="text-muted-foreground hover:text-foreground">
      <ArrowLeft class="h-6 w-6" />
    </a>
    <NeonText class="text-xl tracking-widest uppercase">Initialize Trip</NeonText>
    <div class="w-6"></div> <!-- spacer -->
  </header>

  <!-- Desktop Header -->
  <header class="hidden md:flex items-center justify-between">
    <div class="flex items-center gap-4">
      <div class="w-1 h-6 bg-primary"></div>
      <h1 class="text-3xl font-bold tracking-widest uppercase">Initialize Trip</h1>
    </div>
  </header>

  <form onsubmit={handleCreateTrip} class="grid grid-cols-1 md:grid-cols-[1fr_400px] gap-8 w-full">
    <!-- Left Column: Visuals & Mobile Title -->
    <div class="flex flex-col gap-6 order-2 md:order-1">
      <div class="hidden md:block">
        <h2 class="text-xl font-bold tracking-widest text-secondary mb-2" style="text-shadow: 0 0 10px rgba(0,230,184,0.4);">MISSION_VISUALS</h2>
        <p class="text-sm text-muted-foreground mb-6 max-w-md">Upload high-resolution reconnaissance imagery to establish visual parameters for the tracking sequence.</p>
      </div>

      <!-- Mobile ONLY Banner -->
      <div class="md:hidden relative h-40 w-full rounded-xl overflow-hidden border border-primary/30 shadow-[0_0_15px_rgba(255,42,122,0.2)] mb-4">
        <div class="absolute inset-0 bg-linear-to-t from-[#0B0C10] to-transparent z-10"></div>
        <img src="https://images.unsplash.com/photo-1605806616949-1e87b487cb2a?q=80&w=600" alt="Cyberpunk City" class="h-full w-full object-cover" />
        <div class="absolute bottom-4 left-4 z-20">
          <StatusBadge status="active" class="mb-2 bg-transparent border-none text-secondary shadow-none px-0 tracking-widest font-bold" />
          <h2 class="text-3xl font-bold font-heading">Create Trip</h2>
        </div>
      </div>

      <div class="flex justify-between items-end mb-2 md:hidden">
        <h3 class="text-xs uppercase tracking-widest text-muted-foreground font-semibold">Mission Visuals</h3>
        <span class="text-[10px] text-neon-yellow">Max. 3 images, 4 MB each</span>
      </div>

      <ImageSlotUploader />
    </div>

    <!-- Right Column: Form -->
    <div class="order-1 md:order-2 flex flex-col gap-6">
      {#if error}
        <div class="p-3 border border-red-500/30 bg-red-500/5 text-red-500 text-xs rounded-lg flex items-start gap-2 animate-in fade-in duration-200">
          <AlertCircle class="h-4 w-4 shrink-0 mt-0.5" />
          <span>{error}</span>
        </div>
      {/if}

      <div class="space-y-2">
        <Label for="trip_title" class="text-[10px] uppercase tracking-widest text-primary font-semibold">Trip_Title</Label>
        <Input id="trip_title" placeholder="ENTER_MISSION_CODE..." class="bg-card border-border h-12" bind:value={tripTitle} required />
      </div>

      <div class="space-y-2">
        <Label for="activity_title" class="text-[10px] uppercase tracking-widest text-secondary font-semibold">First Activity</Label>
        <Input id="activity_title" placeholder="e.g. Neo-Kyoto Sector 7" class="bg-card border-border" bind:value={activityTitle} required />
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div class="space-y-2">
          <Label for="activity_date" class="text-[10px] uppercase tracking-widest text-secondary font-semibold">Date</Label>
          <div class="relative">
            <Input id="activity_date" type="date" class="bg-card border-border pr-10" bind:value={date} required />
            <Calendar class="absolute right-3 top-2.5 h-4 w-4 text-muted-foreground pointer-events-none" />
          </div>
        </div>
        <div class="space-y-2">
          <Label for="activity_time" class="text-[10px] uppercase tracking-widest text-secondary font-semibold">Time</Label>
          <div class="relative">
            <Input id="activity_time" type="time" class="bg-card border-border" bind:value={time} required />
          </div>
        </div>
      </div>

      <div class="space-y-2">
        <Label for="activity_location" class="text-[10px] uppercase tracking-widest text-secondary font-semibold">Location</Label>
        <div class="relative">
          <Input id="activity_location" placeholder="e.g. Sector 4 Data Hub" class="bg-card border-border" bind:value={location} />
          <MapPin class="absolute right-3 top-2.5 h-4 w-4 text-muted-foreground" />
        </div>
      </div>

      <div class="space-y-2">
        <Label for="activity_notes" class="text-[10px] uppercase tracking-widest text-secondary font-semibold">Notes</Label>
        <textarea 
          id="activity_notes"
          placeholder="Additional sequence parameters..." 
          class="w-full h-32 rounded-md border border-input bg-card px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring focus-visible:shadow-[0_0_10px_rgba(255,42,122,0.4)] transition-all resize-none"
          bind:value={notes}
        ></textarea>
      </div>

      <div class="mt-4 flex flex-col md:flex-row gap-4 items-center justify-between border-t border-border/50 pt-6">
        <a href="/trips" class="hidden md:block text-xs text-muted-foreground hover:text-foreground tracking-widest uppercase font-semibold transition-colors">
          Abort_Mission
        </a>
        
        <div class="flex w-full md:w-auto gap-0">
          <div class="hidden md:flex h-12 w-12 items-center justify-center bg-secondary/20 text-secondary border border-secondary rounded-l-md">
            <Rocket class="h-5 w-5" />
          </div>
          <Button type="submit" disabled={isSubmitting} class="w-full md:w-auto h-12 md:rounded-l-none bg-[#2A0818] border border-primary text-primary hover:bg-primary hover:text-primary-foreground font-bold tracking-widest uppercase md:px-8">
            <span class="md:hidden mr-2">⚡</span> {isSubmitting ? 'Executing...' : 'Execute_Trip'} <span class="hidden md:inline ml-2">⚡</span>
          </Button>
        </div>
      </div>
      
      <div class="md:hidden text-center mt-2 pb-10">
        <p class="text-[8px] text-muted-foreground uppercase tracking-widest">By starting, you agree to the neural log transmission protocols.</p>
      </div>
    </div>
  </form>
</div>
