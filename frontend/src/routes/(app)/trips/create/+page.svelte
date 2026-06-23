<script lang="ts">
  import { Button } from '$lib/components/ui/button';
  import { Input } from '$lib/components/ui/input';
  import { Label } from '$lib/components/ui/label';
  import NeonText from '$lib/components/shared/NeonText.svelte';
  import ImageSlotUploader from '$lib/components/features/trips/ImageSlotUploader.svelte';
  import DateRangePicker from '$lib/components/shared/DateRangePicker.svelte';
  import { ArrowLeft, AlertCircle } from 'lucide-svelte';
  import { api } from '$lib/services/api/client';
  import { goto } from '$app/navigation';

  let tripTitle = $state('');
  let tripDescription = $state('');
  let startDate = $state('');
  let endDate = $state('');
  let isSubmitting = $state(false);
  let error = $state('');
  let uploadedImages = $state<{ base64: string, name: string }[]>([]);

  async function handleCreateTrip(e: Event) {
    e.preventDefault();
    if (!tripTitle || !startDate || !endDate) {
      error = 'Missing parameters: Title, Start Date, and End Date are required.';
      return;
    }
    
    isSubmitting = true;
    error = '';
    
    try {
      const res = await api.post<any>('/trips', {
        title: tripTitle,
        description: tripDescription || null,
        start_date: startDate,
        end_date: endDate,
        status: 'planned',
        images: uploadedImages
      });
      
      const tripId = res.data.id;
      const slug = res.data.slug || 'trip';
      // Direct deep link to trip details so they can add activities immediately
      goto(`/trips/${slug}/${tripId}`);
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
    <!-- Left Column: Visual Banner -->
    <div class="flex flex-col gap-6 order-2 md:order-1">
      <div class="hidden md:block">
        <h2 class="text-xl font-bold tracking-widest text-secondary mb-2" style="text-shadow: 0 0 10px rgba(0,230,184,0.4);">MISSION_VISUALS</h2>
        <p class="text-sm text-muted-foreground mb-6 max-w-md">Upload high-resolution reconnaissance imagery to establish visual parameters for the tracking sequence.</p>
      </div>

      <div class="flex justify-between items-end mb-2 md:hidden">
        <h3 class="text-xs uppercase tracking-widest text-muted-foreground font-semibold">Mission Visuals</h3>
        <span class="text-[10px] text-neon-yellow">Max. 3 images, 4 MB each</span>
      </div>

      <ImageSlotUploader bind:images={uploadedImages} />
    </div>

    <!-- Right Column: Form Inputs -->
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
        <Label for="date_range" class="text-[10px] uppercase tracking-widest text-secondary font-semibold">Expedition Duration</Label>
        <DateRangePicker bind:startDate bind:endDate placeholder="SELECT EXPEDITION TIMELINE..." />
      </div>

      <div class="space-y-2">
        <Label for="trip_description" class="text-[10px] uppercase tracking-widest text-secondary font-semibold">Description / Brief</Label>
        <textarea 
          id="trip_description"
          placeholder="Additional sequence parameters..." 
          class="w-full h-32 rounded-md border border-input bg-card px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring focus-visible:shadow-[0_0_10px_rgba(255,42,122,0.4)] transition-all resize-none"
          bind:value={tripDescription}
        ></textarea>
      </div>

      <div class="mt-4 flex flex-col md:flex-row gap-4 items-center justify-between border-t border-border/50 pt-6">
        <a href="/trips" class="hidden md:block text-xs text-muted-foreground hover:text-foreground tracking-widest uppercase font-semibold transition-colors">
          Abort_Mission
        </a>
        
        <div class="flex w-full md:w-auto">
          <Button type="submit" disabled={isSubmitting} class="w-full md:w-auto h-12 bg-[#2A0818] border border-primary text-primary hover:bg-primary hover:text-primary-foreground font-bold tracking-widest uppercase md:px-8">
            <span class="md:hidden mr-2">⚡</span> {isSubmitting ? 'Submitting...' : 'Submit'} <span class="hidden md:inline ml-2">⚡</span>
          </Button>
        </div>
      </div>
      
      <div class="md:hidden text-center mt-2 pb-10">
        <p class="text-[8px] text-muted-foreground uppercase tracking-widest">By starting, you agree to the neural log transmission protocols.</p>
      </div>
    </div>
  </form>
</div>
