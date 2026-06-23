<script lang="ts">
  import HeroTripCard from '$lib/components/features/trips/HeroTripCard.svelte';
  import StandardTripCard from '$lib/components/features/trips/StandardTripCard.svelte';
  import NeonText from '$lib/components/shared/NeonText.svelte';
  import { api } from '$lib/services/api/client';
  import { Button } from '$lib/components/ui/button';
  import { onMount } from 'svelte';
  import { Plus } from 'lucide-svelte';
  import { getDisplayStatus } from '$lib/utils/tripStatus';
  import { fade, fly } from 'svelte/transition';
  import { flip } from 'svelte/animate';

  let trips = $state<any[]>([]);
  let isLoading = $state(true);

  onMount(async () => {
    try {
      const res = await api.get<any>('/trips');
      trips = (res.data || []).filter((t: any) => 
        t.status !== 'completed' && 
        t.status !== 'cancelled' && 
        t.journey?.status !== 'completed' && 
        t.journey?.status !== 'cancelled'
      );
    } catch (e) {
      console.error('Failed to fetch trips:', e);
    } finally {
      isLoading = false;
    }
  });

  // Get active trip or fallback to first trip
  let activeTrip = $derived.by(() => {
    if (trips.length === 0) return null;
    return trips.find(t => t.status === 'active') || trips[0];
  });

  // Get other trips
  let otherTrips = $derived.by(() => {
    if (!activeTrip) return [];
    return trips.filter(t => t.id !== activeTrip.id);
  });

  // Get image URL
  function getImageUrl(trip: any, w: number = 600) {
    if (trip.first_image_url) {
      if (trip.first_image_url.startsWith('http')) return trip.first_image_url;
      return `http://localhost:8000/${trip.first_image_url}`;
    }
    return `https://images.unsplash.com/photo-1542931287-023b922fa89b?q=80&w=${w}`;
  }
</script>

<div class="flex flex-col gap-8 pb-10 max-w-6xl mx-auto w-full">
  <header class="md:hidden pt-4 flex items-center justify-between">
    <div>
      <NeonText class="text-3xl tracking-widest block mb-1">My Trips</NeonText>
      <p class="text-sm text-muted-foreground">Manage your upcoming trips.</p>
    </div>
  </header>

  <!-- Desktop Header -->
  <header class="hidden md:flex items-center justify-between border-b border-border/50 pb-4">
    <div class="flex items-center gap-4">
      <div class="w-1 h-6 bg-primary"></div>
      <h1 class="text-3xl font-bold tracking-widest uppercase text-white">Upcoming Protocols</h1>
    </div>
    <Button href="/trips/create" class="bg-[#2A0818] border border-primary text-primary hover:bg-primary hover:text-primary-foreground shadow-none hover:shadow-[0_0_15px_rgba(255,42,122,0.6)]">
      INITIALIZE NEW TRIP
    </Button>
  </header>

  {#if isLoading}
    <div class="grid gap-6 animate-pulse">
      <div class="h-80 bg-slate-900/50 border border-border/50 rounded-xl"></div>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4">
        {#each Array(3) as _}
          <div class="h-56 bg-slate-900/50 border border-border/50 rounded-xl"></div>
        {/each}
      </div>
    </div>
  {:else if trips.length === 0}
    <div class="text-center py-20 bg-slate-950/20 rounded-2xl border border-border/50 flex flex-col items-center justify-center">
      <h3 class="text-xl font-bold text-white mb-2">NO ACTIVE PROTOCOLS</h3>
      <p class="text-muted-foreground text-sm mb-6 max-w-sm mx-auto">Establish new trip coordinates to initialize navigation tracking.</p>
      <Button href="/trips/create" class="bg-[#2A0818] border border-primary text-primary hover:bg-primary hover:text-primary-foreground shadow-none hover:shadow-[0_0_15px_rgba(255,42,122,0.6)]">
        INITIALIZE FIRST TRIP
      </Button>
    </div>
  {:else}
    <!-- Desktop Hero Section -->
    {#if activeTrip}
      <section class="hidden md:block">
        <HeroTripCard 
          title={activeTrip.title}
          tripId={activeTrip.id}
          tripSlug={activeTrip.slug}
          journeyId={activeTrip.journey_id}
          imageSrc={getImageUrl(activeTrip, 2000)}
          description={activeTrip.description || "In-situ navigation across the Shibuya quadrant. High-priority mission to recover encoded signals from the underground synth-network."}
          status={getDisplayStatus(activeTrip)}
          startDate={activeTrip.start_date}
          endDate={activeTrip.end_date}
        />
      </section>
    {/if}

    <!-- Trip List -->
    <section>
      <div class="flex items-center justify-between mb-6">
        <div class="flex gap-4 items-center">
          <div class="w-1 h-6 bg-secondary"></div>
          <h2 class="text-xl font-bold tracking-widest uppercase text-white">All Expedition Sectors</h2>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-4 gap-6">
        {#if activeTrip}
          <!-- Mobile only shows the active trip as a standard card at the top -->
          <div class="md:hidden">
            <a href="/trips/{activeTrip.slug || 'trip'}/{activeTrip.id}" class="block">
              <StandardTripCard 
                trip={activeTrip}
                imageSrc={getImageUrl(activeTrip, 600)}
              />
            </a>
          </div>
        {/if}

        {#each otherTrips as trip, i (trip.id)}
          <a 
            href="/trips/{trip.slug || 'trip'}/{trip.id}" 
            class="block"
            in:fly={{ y: 20, duration: 400, delay: i * 50 }}
            animate:flip={{ duration: 300 }}
          >
            <StandardTripCard 
              trip={trip}
              imageSrc={getImageUrl(trip, 600)}
            />
          </a>
        {/each}
      </div>
    </section>
  {/if}
</div>
