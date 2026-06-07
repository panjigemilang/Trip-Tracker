<script lang="ts">
  import { FileUp, File, AlertCircle } from 'lucide-svelte';
  import { Button } from '$lib/components/ui/button';

  let { onUpload, isUploading = false } = $props();
  let fileInput: HTMLInputElement;
  let selectedFile = $state<File | null>(null);

  function handleFileSelect(e: Event) {
    const target = e.target as HTMLInputElement;
    if (target.files && target.files.length > 0) {
      selectedFile = target.files[0];
    }
  }

  function handleDrop(e: DragEvent) {
    e.preventDefault();
    if (e.dataTransfer?.files && e.dataTransfer.files.length > 0) {
      selectedFile = e.dataTransfer.files[0];
    }
  }

  function handleDragOver(e: DragEvent) {
    e.preventDefault();
  }

  function submitFile() {
    if (selectedFile) {
      onUpload(selectedFile);
    }
  }
</script>

<div class="space-y-4">
  <!-- svelte-ignore a11y_no_static_element_interactions -->
  <div 
    class="border-2 border-dashed border-slate-300 dark:border-slate-700 rounded-xl p-8 text-center hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors cursor-pointer"
    ondrop={handleDrop}
    ondragover={handleDragOver}
    onclick={() => fileInput.click()}
  >
    <div class="flex justify-center mb-4 text-slate-400">
      <FileUp class="w-10 h-10" />
    </div>
    
    {#if selectedFile}
      <div class="flex items-center justify-center gap-2 text-slate-900 dark:text-slate-100 font-medium">
        <File class="w-5 h-5 text-blue-500" />
        {selectedFile.name}
      </div>
      <p class="text-sm text-slate-500 mt-1">{(selectedFile.size / 1024).toFixed(1)} KB</p>
    {:else}
      <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Click to upload or drag and drop</h3>
      <p class="text-sm text-slate-500 mt-1">XLSX, XLS, or CSV files only</p>
    {/if}
    
    <input 
      type="file" 
      class="hidden" 
      accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
      bind:this={fileInput}
      onchange={handleFileSelect}
    />
  </div>

  <div class="flex justify-end gap-2">
    {#if selectedFile}
      <Button variant="outline" onclick={(e) => { e.stopPropagation(); selectedFile = null; }}>Cancel</Button>
      <Button onclick={(e) => { e.stopPropagation(); submitFile(); }} disabled={isUploading}>
        {isUploading ? 'Uploading...' : 'Import Activities'}
      </Button>
    {/if}
  </div>
</div>
