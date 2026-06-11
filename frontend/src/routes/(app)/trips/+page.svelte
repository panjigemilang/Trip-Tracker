<script lang="ts">
  import HeroTripCard from '$lib/components/features/trips/HeroTripCard.svelte';
  import StandardTripCard from '$lib/components/features/trips/StandardTripCard.svelte';
  import NeonText from '$lib/components/shared/NeonText.svelte';
</script>

<div class="flex flex-col gap-8 pb-10">
  <header class="md:hidden pt-4">
    <NeonText class="text-3xl tracking-widest block mb-1">My Trips</NeonText>
    <p class="text-sm text-muted-foreground">Manage your upcoming trips.</p>
  </header>

  <!-- Desktop Hero Section -->
  <section class="hidden md:block">
    <HeroTripCard 
      title="NEO-TOKYO EXPLORATION"
      imageSrc="https://images.unsplash.com/photo-1542931287-023b922fa89b?q=80&w=2000"
      description="In-situ navigation across the Shibuya quadrant. High-priority mission to recover encoded signals from the underground synth-network."
      status="active"
    />
  </section>

  <!-- Trip List -->
  <section>
    <div class="flex items-center justify-between mb-6">
      <div class="hidden md:flex gap-4 items-center">
        <div class="w-1 h-6 bg-primary"></div>
        <h2 class="text-2xl font-bold tracking-widest uppercase">Upcoming Protocols</h2>
      </div>
      <button class="hidden md:block text-xs text-secondary hover:text-secondary/80 uppercase tracking-widest font-semibold transition-colors">
        View All Nodes
      </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
      <StandardTripCard 
        title="Neo-Tokyo Exploration"
        imageSrc="https://images.unsplash.com/photo-1542931287-023b922fa89b?q=80&w=600"
        dateRange="Oct 12 - Oct 28"
        location="Sector 7G"
        status="active"
      />
      <StandardTripCard 
        title="Off-World Colony Visit"
        imageSrc="https://images.unsplash.com/photo-1451187580459-43490279c0fa?q=80&w=600"
        dateRange="Nov 05 - Nov 15"
        location="Luna Prime"
        status="upcoming"
      />
      <StandardTripCard 
        title="Underground Circuit"
        imageSrc="https://images.unsplash.com/photo-1514924013411-cbf25faa35bb?q=80&w=600"
        dateRange="TBD"
        location="Undercity"
        status="planning"
      />
    </div>
  </section>
</div>
