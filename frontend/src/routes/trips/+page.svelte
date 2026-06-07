<script lang="ts">
    import TripCard from '$lib/components/molecules/TripCard.svelte';
    import { Button } from '$lib/components/ui/button';
    import { Plus } from 'lucide-svelte';
    import { onMount } from 'svelte';

    // In a real app, this would fetch from the backend.
    // For now, we mock it or set up state.
    let trips = $state<any[]>([]);
    let isLoading = $state(true);

    onMount(async () => {
        // Mock data for UI development
        trips = [
            { id: '1', title: 'Summer in Bali', status: 'planned', start_date: '2026-08-01', end_date: '2026-08-14', activity_count: 12 },
            { id: '2', title: 'Tokyo Adventure', status: 'draft', start_date: null, end_date: null, activity_count: 0 },
            { id: '3', title: 'Paris Weekend', status: 'completed', start_date: '2025-05-10', end_date: '2025-05-12', activity_count: 5 },
        ];
        isLoading = false;
    });
</script>

<svelte:head>
    <title>My Trips | Plan Trip Tracker</title>
</svelte:head>

<div class="container max-w-4xl py-8">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold tracking-tight">My Trips</h1>
            <p class="text-slate-500 dark:text-slate-400">Manage your travel itineraries and upcoming journeys.</p>
        </div>
        <Button href="/trips/new">
            <Plus class="w-4 h-4 mr-2" />
            New Trip
        </Button>
    </div>

    {#if isLoading}
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            {#each Array(3) as _}
                <div class="h-48 rounded-xl bg-slate-100 dark:bg-slate-800 animate-pulse"></div>
            {/each}
        </div>
    {:else if trips.length === 0}
        <div class="text-center py-12 border-2 border-dashed rounded-xl border-slate-200 dark:border-slate-800">
            <h3 class="text-lg font-medium text-slate-900 dark:text-slate-100 mb-1">No trips found</h3>
            <p class="text-sm text-slate-500 mb-4">You haven't created any trips yet.</p>
            <Button href="/trips/new">Create your first trip</Button>
        </div>
    {:else}
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            {#each trips as trip (trip.id)}
                <TripCard {trip} />
            {/each}
        </div>
    {/if}
</div>
