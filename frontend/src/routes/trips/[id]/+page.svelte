<script lang="ts">
    import { page } from '$app/stores';
    import { goto } from '$app/navigation';
    import { onMount } from 'svelte';
    import { ArrowLeft, CalendarDays, MapPin, Plus } from 'lucide-svelte';
    import { Button } from '$lib/components/ui/button';
    import StatusBadge from '$lib/components/atoms/StatusBadge.svelte';
    import { journeyStore } from '$lib/stores/journey.svelte';
    import { toast } from 'svelte-sonner';
    import ImportWizard from '$lib/components/organisms/ImportWizard.svelte';
    import { Upload } from 'lucide-svelte';

    let tripId = $state($page.params.id);
    let trip = $state<any>(null);
    let isLoading = $state(true);
    let isStarting = $state(false);
    let showImport = $state(false);

    async function handleStartJourney() {
        if (!trip) return;
        isStarting = true;
        try {
            const journey = await journeyStore.startJourney(trip.id);
            toast.success('Journey started!');
            goto(`/journey/${journey.id}`);
        } catch (e: any) {
            toast.error(e.message || 'Failed to start journey');
        } finally {
            isStarting = false;
        }
    }

    onMount(async () => {
        // Mock data fetch
        // In a real app: trip = await api.get(`/trips/${tripId}`)
        trip = {
            id: tripId,
            title: 'Summer in Bali',
            description: 'A 2-week vacation exploring beaches and temples.',
            status: 'planned',
            start_date: '2026-08-01',
            end_date: '2026-08-14',
            activities: [
                { id: '1', title: 'Arrival at DPS', date: '2026-08-01', time: '14:00', location: 'Ngurah Rai Airport' },
                { id: '2', title: 'Check-in to Villa', date: '2026-08-01', time: '16:00', location: 'Ubud' },
            ]
        };
        isLoading = false;
    });
</script>

<svelte:head>
    <title>{trip?.title || 'Trip Details'} | Plan Trip Tracker</title>
</svelte:head>

<div class="container max-w-4xl py-8">
    <div class="mb-8">
        <Button variant="ghost" href="/trips" class="mb-4 -ml-4 text-slate-500">
            <ArrowLeft class="w-4 h-4 mr-2" />
            Back to Trips
        </Button>

        {#if isLoading}
            <div class="h-10 w-64 bg-slate-200 dark:bg-slate-800 rounded animate-pulse mb-2"></div>
            <div class="h-6 w-96 bg-slate-200 dark:bg-slate-800 rounded animate-pulse"></div>
        {:else if trip}
            <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <h1 class="text-3xl font-bold tracking-tight">{trip.title}</h1>
                        <StatusBadge status={trip.status} />
                    </div>
                    
                    {#if trip.description}
                        <p class="text-slate-600 dark:text-slate-400 mb-4 max-w-2xl">{trip.description}</p>
                    {/if}
                    
                    <div class="flex items-center gap-6 text-sm text-slate-500">
                        <div class="flex items-center gap-2">
                            <CalendarDays class="w-4 h-4" />
                            <span>
                                {trip.start_date ? trip.start_date : 'TBD'} 
                                {#if trip.end_date} &mdash; {trip.end_date} {/if}
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="flex gap-2">
                    <Button variant="outline" href={`/trips/${trip.id}/edit`}>Edit Trip</Button>
                    {#if trip.status === 'planned' || trip.status === 'active'}
                        <Button disabled={isStarting} onclick={handleStartJourney}>
                            {isStarting ? 'Starting...' : (trip.status === 'active' ? 'Resume Journey' : 'Start Journey')}
                        </Button>
                    {/if}
                </div>
            </div>
        {/if}
    </div>

    {#if !isLoading && trip}
        <div class="mt-12">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-semibold tracking-tight">Itinerary</h2>
                <div class="flex gap-2">
                    <Button variant="outline" size="sm" onclick={() => showImport = !showImport}>
                        <Upload class="w-4 h-4 mr-2" />
                        Import
                    </Button>
                    <Button variant="outline" size="sm">
                        <Plus class="w-4 h-4 mr-2" />
                        Add Activity
                    </Button>
                </div>
            </div>

            {#if showImport}
                <div class="mb-8">
                    <ImportWizard tripId={trip.id} onSuccess={() => { showImport = false; window.location.reload(); }} />
                </div>
            {/if}

            {#if trip.activities && trip.activities.length > 0}
                <div class="space-y-4">
                    <!-- Basic mock activity list -->
                    {#each trip.activities as activity}
                        <div class="flex items-start gap-4 p-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900">
                            <div class="min-w-24 text-right pt-1">
                                <div class="font-medium text-slate-900 dark:text-slate-100">{activity.time}</div>
                                <div class="text-xs text-slate-500">{activity.date}</div>
                            </div>
                            <div class="w-3 h-3 rounded-full bg-primary mt-2 flex-shrink-0"></div>
                            <div class="flex-1">
                                <h3 class="font-medium text-lg">{activity.title}</h3>
                                {#if activity.location}
                                    <div class="flex items-center text-sm text-slate-500 mt-1">
                                        <MapPin class="w-3 h-3 mr-1" />
                                        {activity.location}
                                    </div>
                                {/if}
                            </div>
                        </div>
                    {/each}
                </div>
            {:else}
                <div class="text-center py-12 border-2 border-dashed rounded-xl border-slate-200 dark:border-slate-800">
                    <p class="text-slate-500 mb-4">No activities planned yet.</p>
                    <Button>Add your first activity</Button>
                </div>
            {/if}
        </div>
    {/if}
</div>
