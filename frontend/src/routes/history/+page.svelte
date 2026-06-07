<script lang="ts">
  import { onMount } from 'svelte';
  import { apiClient } from '$lib/services/api/client';
  import { History, Calendar, MapPin, ChevronRight, CheckCircle2, XCircle } from 'lucide-svelte';
  import StatusBadge from '$lib/components/atoms/StatusBadge.svelte';

  let histories = $state<any[]>([]);
  let loading = $state(true);

  onMount(async () => {
    try {
      const res = await apiClient.get('/history');
      histories = res.data;
    } catch (e) {
      console.error(e);
    } finally {
      loading = false;
    }
  });
</script>

<svelte:head>
  <title>History | Plan Trip Tracker</title>
</svelte:head>

<div class="container max-w-4xl py-8">
  <div class="flex items-center gap-3 mb-8">
    <History class="w-8 h-8 text-blue-500" />
    <h1 class="text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Trip History</h1>
  </div>

  {#if loading}
    <div class="space-y-4 animate-pulse">
      {#each Array(3) as _}
        <div class="h-32 bg-slate-200 dark:bg-slate-800 rounded-xl"></div>
      {/each}
    </div>
  {:else if histories.length === 0}
    <div class="text-center py-20 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-100 dark:border-slate-800">
      <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-800 mb-4">
        <History class="w-8 h-8 text-slate-400" />
      </div>
      <h3 class="text-xl font-semibold text-slate-900 dark:text-white mb-2">No History Yet</h3>
      <p class="text-slate-500 max-w-sm mx-auto">
        Completed or cancelled trips will appear here. Go plan a trip and start a journey!
      </p>
    </div>
  {:else}
    <div class="space-y-4">
      {#each histories as trip}
        <a 
          href="/history/{trip.id}"
          class="block bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-5 hover:border-blue-300 dark:hover:border-blue-700 transition-all shadow-sm hover:shadow-md"
        >
          <div class="flex items-start justify-between">
            <div class="space-y-2">
              <div class="flex items-center gap-2">
                <h3 class="text-xl font-semibold">{trip.title}</h3>
                {#if trip.status === 'completed'}
                  <span class="inline-flex items-center gap-1 text-xs font-medium text-emerald-700 bg-emerald-100 dark:bg-emerald-900/30 dark:text-emerald-400 px-2 py-0.5 rounded-full">
                    <CheckCircle2 class="w-3 h-3" /> Completed
                  </span>
                {:else if trip.status === 'cancelled'}
                  <span class="inline-flex items-center gap-1 text-xs font-medium text-red-700 bg-red-100 dark:bg-red-900/30 dark:text-red-400 px-2 py-0.5 rounded-full">
                    <XCircle class="w-3 h-3" /> Cancelled
                  </span>
                {/if}
              </div>
              
              {#if trip.description}
                <p class="text-slate-500 text-sm line-clamp-2">{trip.description}</p>
              {/if}

              <div class="flex flex-wrap items-center gap-4 text-sm text-slate-500 mt-4">
                {#if trip.start_date}
                  <div class="flex items-center gap-1.5">
                    <Calendar class="w-4 h-4" />
                    <span>{trip.start_date}</span>
                  </div>
                {/if}
                <div class="flex items-center gap-1.5">
                  <MapPin class="w-4 h-4" />
                  <span>{trip.activities?.length || 0} Activities</span>
                </div>
              </div>
            </div>

            <div class="hidden sm:flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-slate-50 dark:bg-slate-800 text-slate-400">
              <ChevronRight class="w-5 h-5" />
            </div>
          </div>
        </a>
      {/each}
    </div>
  {/if}
</div>
