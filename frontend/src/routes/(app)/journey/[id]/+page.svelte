<script lang="ts">
  import type { PageData } from './$types';
  import JourneyTracker from '$lib/components/organisms/JourneyTracker.svelte';
  import { journeyStore } from '$lib/stores/journey.svelte';
  import type { ActivityStatus } from '$lib/types/journey';
  import { onMount, onDestroy } from 'svelte';
  import { toast } from 'svelte-sonner';

  let { data }: { data: PageData } = $props();
  
  // Set initial state
  journeyStore.activeJourney = data.journey;

  // Poll for updates every 30 seconds
  let interval: any;

  onMount(() => {
    interval = setInterval(() => {
      journeyStore.fetchJourney(data.journey.id).catch(console.error);
    }, 30000);
  });

  onDestroy(() => {
    if (interval) clearInterval(interval);
  });

  async function handleUpdateStatus(activityId: string, status: ActivityStatus) {
    try {
      await journeyStore.updateActivityStatus(activityId, status);
      toast.success('Status updated');
    } catch (e: any) {
      toast.error('Failed to update status');
    }
  }
</script>

<svelte:head>
  <title>Active Journey - {journeyStore.activeJourney?.trip?.title}</title>
</svelte:head>

<div class="container max-w-3xl mx-auto py-8 px-4">
  <div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-2">
      {journeyStore.activeJourney?.trip?.title}
    </h1>
    <p class="text-gray-500">Live Journey Tracking</p>
  </div>

  {#if journeyStore.activeJourney?.status === 'completed'}
    <div class="bg-green-50 text-green-800 p-4 rounded-lg mb-8 border border-green-200 text-center">
      <h2 class="font-bold text-xl mb-1">Journey Completed! 🎉</h2>
      <p>You have finished all activities for this trip.</p>
    </div>
  {/if}

  {#if journeyStore.activeJourney}
    <JourneyTracker journey={journeyStore.activeJourney} onUpdateStatus={handleUpdateStatus} />
  {/if}
</div>
