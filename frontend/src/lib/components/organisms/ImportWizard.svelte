<script lang="ts">
  import ImportDropzone from '$lib/components/molecules/ImportDropzone.svelte';
  import { Button } from '$lib/components/ui/button';
  import { Download, AlertCircle, CheckCircle2 } from 'lucide-svelte';
  import { apiClient } from '$lib/services/api/client';
  import { toast } from 'svelte-sonner';

  let { tripId, onSuccess }: { tripId: string, onSuccess: () => void } = $props();
  
  let isUploading = $state(false);
  let validationErrors = $state<any[]>([]);

  async function downloadTemplate() {
    // In a real app, maybe fetch Blob and trigger download
    window.open(`${import.meta.env.VITE_API_URL}/v1/trips/import/template`, '_blank');
  }

  async function handleUpload(file: File) {
    isUploading = true;
    validationErrors = [];
    
    const formData = new FormData();
    formData.append('file', file);

    try {
      // apiClient doesn't support FormData directly well if headers are overridden, 
      // but assuming standard fetch setup
      const res = await fetch(`${import.meta.env.VITE_API_URL}/v1/trips/${tripId}/import`, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
        },
        body: formData
      });

      const data = await res.json();

      if (!res.ok) {
        if (res.status === 422 && data.errors) {
           // Format errors
           validationErrors = Object.entries(data.errors).map(([key, messages]) => ({
             row: key,
             messages: messages as string[]
           }));
           toast.error('Import validation failed. Please check the file.');
        } else {
           throw new Error(data.message || 'Import failed');
        }
      } else {
        toast.success('Activities imported successfully!');
        onSuccess();
      }
    } catch (e: any) {
      toast.error(e.message || 'An unexpected error occurred during import.');
    } finally {
      isUploading = false;
    }
  }
</script>

<div class="space-y-6">
  <div class="bg-slate-50 dark:bg-slate-800/50 p-4 rounded-xl border border-slate-200 dark:border-slate-700">
    <div class="flex items-start justify-between gap-4">
      <div>
        <h3 class="font-semibold text-slate-900 dark:text-slate-100">Need a template?</h3>
        <p class="text-sm text-slate-500 mt-1">Download the CSV template with the required columns: date, time, title, location, notes.</p>
      </div>
      <Button variant="outline" size="sm" onclick={downloadTemplate}>
        <Download class="w-4 h-4 mr-2" />
        Download
      </Button>
    </div>
  </div>

  <ImportDropzone onUpload={handleUpload} {isUploading} />

  {#if validationErrors.length > 0}
    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4">
      <div class="flex items-center gap-2 text-red-800 dark:text-red-400 font-semibold mb-3">
        <AlertCircle class="w-5 h-5" />
        Fix the following errors and re-upload:
      </div>
      <ul class="space-y-2 text-sm text-red-700 dark:text-red-300">
        {#each validationErrors as error}
          <li class="flex items-start gap-2">
            <span class="font-medium bg-red-100 dark:bg-red-900/50 px-1.5 py-0.5 rounded text-xs">Row {error.row}</span>
            <span>{error.messages.join(', ')}</span>
          </li>
        {/each}
      </ul>
    </div>
  {/if}
</div>
