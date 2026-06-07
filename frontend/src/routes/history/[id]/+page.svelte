<script lang="ts">
  import { onMount } from 'svelte';
  import { page } from '$app/stores';
  import { apiClient } from '$lib/services/api/client';
  import { Calendar, MapPin, ArrowLeft, Clock, CheckCircle2, XCircle } from 'lucide-svelte';
  import { Button } from '$lib/components/ui/button';

  let tripId = $state($page.params.id);
  let trip = $state<any>(null);
  let loading = $state(true);

  onMount(async () => {
    try {
      const res = await apiClient.get(`/history/${tripId}`);
      trip = res.data;
    } catch (e) {
      console.error(e);
    } finally {
      loading = false;
    }
  });

  function getActivityStatus(activityId: string) {
    if (!trip?.journey?.activities) return 'upcoming';
    const ja = trip.journey.activities.find((a: any) => a.activity_id === activityId);
    return ja ? ja.status : 'upcoming';
  }
</script>

<svelte:head>
  <title>{trip?.title || 'Trip History'} | Plan Trip Tracker</title>
</svelte:head>

<div class="container max-w-4xl py-8">
  <div class="mb-8">
    <Button variant="ghost" size="sm" href="/history" class="-ml-3 text-slate-500 mb-4">
      <ArrowLeft class="w-4 h-4 mr-2" /> Back to History
    </Button>
    
    {#if loading}
      <div class="h-10 w-2/3 bg-slate-200 dark:bg-slate-800 rounded animate-pulse mb-4"></div>
      <div class="h-4 w-1/3 bg-slate-200 dark:bg-slate-800 rounded animate-pulse"></div>
    {:else if trip}
      <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
        <div>
          <h1 class="text-3xl font-bold tracking-tight text-slate-900 dark:text-white mb-2">{trip.title}</h1>
          <div class="flex items-center gap-4 text-sm text-slate-500">
            {#if trip.status === 'completed'}
              <span class="inline-flex items-center gap-1 font-medium text-emerald-700 dark:text-emerald-400">
                <CheckCircle2 class="w-4 h-4" /> Completed
              </span>
            {:else}
              <span class="inline-flex items-center gap-1 font-medium text-red-700 dark:text-red-400">
                <XCircle class="w-4 h-4" /> Cancelled
              </span>
            {/if}
            
            {#if trip.start_date}
              <span class="flex items-center gap-1">
                <Calendar class="w-4 h-4" /> {trip.start_date}
              </span>
            {/if}
          </div>
        </div>
      </div>
      
      {#if trip.description}
        <p class="text-slate-600 dark:text-slate-400 mt-6 max-w-3xl leading-relaxed">
          {trip.description}
        </p>
      {/if}
    {/if}
  </div>

  {#if !loading && trip}
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 md:p-8">
      <h2 class="text-xl font-semibold mb-6">Activity Timeline</h2>
      
      {#if trip.activities && trip.activities.length > 0}
        <div class="relative border-l-2 border-slate-200 dark:border-slate-800 ml-4 space-y-8 pb-4">
          {#each trip.activities as activity}
            {@const status = getActivityStatus(activity.id)}
            <div class="relative pl-8">
              <!-- Timeline node -->
              <div class="absolute -left-[9px] top-1.5 w-4 h-4 rounded-full border-2 bg-white dark:bg-slate-900 {
                status === 'completed' ? 'border-emerald-500 bg-emerald-500' :
                status === 'missed' ? 'border-red-500 bg-red-500' :
                status === 'skipped' ? 'border-slate-400 bg-slate-400' :
                'border-slate-300 dark:border-slate-600'
              }"></div>

              <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-5 border border-slate-100 dark:border-slate-700/50">
                <div class="flex items-start justify-between gap-4">
                  <div>
                    <div class="flex items-center gap-2 mb-1.5">
                      <h4 class="font-semibold text-slate-900 dark:text-white text-lg">{activity.title}</h4>
                      <span class="text-xs uppercase tracking-wider font-bold px-2 py-0.5 rounded {
                        status === 'completed' ? 'text-emerald-700 bg-emerald-100 dark:bg-emerald-900/30 dark:text-emerald-400' :
                        status === 'missed' ? 'text-red-700 bg-red-100 dark:bg-red-900/30 dark:text-red-400' :
                        status === 'skipped' ? 'text-slate-700 bg-slate-200 dark:bg-slate-700 dark:text-slate-300' :
                        'text-slate-500 bg-slate-100 dark:bg-slate-800'
                      }">
                        {status}
                      </span>
                    </div>
                    
                    <div class="flex items-center gap-4 text-sm text-slate-500 mb-3">
                      <span class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-300">
                        <Clock class="w-4 h-4 text-blue-500" /> {activity.time.slice(0, 5)}
                      </span>
                      {#if activity.location}
                        <span class="flex items-center gap-1.5">
                          <MapPin class="w-4 h-4" /> {activity.location}
                        </span>
                      {/if}
                    </div>

                    {#if activity.notes}
                      <p class="text-slate-600 dark:text-slate-400 text-sm">{activity.notes}</p>
                    {/if}
                  </div>
                </div>
              </div>
            </div>
          {/each}
        </div>
      {:else}
        <p class="text-slate-500 text-center py-8">No activities recorded for this trip.</p>
      {/if}
    </div>
  {/if}
</div>
