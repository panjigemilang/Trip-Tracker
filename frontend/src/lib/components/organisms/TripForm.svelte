<script lang="ts">
    import { Button } from '$lib/components/ui/button';
    import { Label } from '$lib/components/ui/label';
    import { Input } from '$lib/components/ui/input';
    import { Textarea } from '$lib/components/ui/textarea';

    let { 
        trip = { title: '', description: '', status: 'draft' }, 
        onSubmit, 
        isSubmitting = false 
    } = $props();

    let title = $state(trip.title);
    let description = $state(trip.description);
    let status = $state(trip.status);

    function handleSubmit(e: Event) {
        e.preventDefault();
        onSubmit({ title, description, status });
    }
</script>

<form onsubmit={handleSubmit} class="space-y-6">
    <div class="space-y-2">
        <Label for="title">Trip Title</Label>
        <Input 
            id="title" 
            bind:value={title} 
            placeholder="E.g., Summer Trip to Bali" 
            required 
            maxlength={255} 
        />
    </div>

    <div class="space-y-2">
        <Label for="description">Description</Label>
        <Textarea 
            id="description" 
            bind:value={description} 
            placeholder="A brief description of your trip" 
            rows={4} 
        />
    </div>

    <div class="space-y-2">
        <Label for="status">Status</Label>
        <select 
            id="status" 
            bind:value={status} 
            class="flex h-10 w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
        >
            <option value="draft">Draft</option>
            <option value="planned">Planned</option>
            <option value="active">Active</option>
            <option value="completed">Completed</option>
        </select>
    </div>

    <div class="flex justify-end gap-4">
        <Button variant="outline" type="button" href="/trips">Cancel</Button>
        <Button type="submit" disabled={isSubmitting}>
            {isSubmitting ? 'Saving...' : 'Save Trip'}
        </Button>
    </div>
</form>
