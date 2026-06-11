<script lang="ts">
    import TripForm from '$lib/components/organisms/TripForm.svelte';
    import { ArrowLeft } from 'lucide-svelte';
    import { Button } from '$lib/components/ui/button';

    let isSubmitting = $state(false);

    async function handleSaveTrip(tripData: any) {
        isSubmitting = true;
        
        try {
            // In a real app, send API request:
            // await api.post('/trips', tripData);
            
            // Mock delay
            await new Promise(resolve => setTimeout(resolve, 1000));
            
            // Redirect to trips list
            window.location.href = '/trips';
        } catch (error) {
            console.error('Failed to save trip:', error);
            alert('Failed to save trip');
        } finally {
            isSubmitting = false;
        }
    }
</script>

<svelte:head>
    <title>New Trip | Plan Trip Tracker</title>
</svelte:head>

<div class="container max-w-2xl py-8">
    <div class="mb-8">
        <Button variant="ghost" href="/trips" class="mb-4 -ml-4 text-slate-500">
            <ArrowLeft class="w-4 h-4 mr-2" />
            Back to Trips
        </Button>
        <h1 class="text-3xl font-bold tracking-tight">Create New Trip</h1>
        <p class="text-slate-500 dark:text-slate-400">Set up the basic details for your upcoming adventure.</p>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6">
        <TripForm onSubmit={handleSaveTrip} {isSubmitting} />
    </div>
</div>
