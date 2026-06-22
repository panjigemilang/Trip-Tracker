<script lang="ts">
  import CyberCard from '$lib/components/shared/CyberCard.svelte';
  import NeonText from '$lib/components/shared/NeonText.svelte';
  import { Button } from '$lib/components/ui/button';
  import { toast } from 'svelte-sonner';
  import { api } from '$lib/services/api/client';
  import { onMount } from 'svelte';
  import { goto } from '$app/navigation';
  import { FileSpreadsheet, UploadCloud, AlertTriangle, FileText } from 'lucide-svelte';

  let trips = $state<any[]>([]);
  let selectedTripId = $state<string>('');
  let isLoadingTrips = $state(true);
  
  let isDragging = $state(false);
  let isUploading = $state(false);
  
  let selectedFile = $state<File | null>(null);
  
  let validationErrors = $state<any[]>([]);
  let hasAttemptedUpload = $state(false);

  onMount(async () => {
    try {
      const res = await api.get<{ data: any[] }>('/trips');
      // Filter out trips that are completed or cancelled; only allow importing into active/planned/draft trips
      trips = res.data.filter((t: any) => t.status !== 'completed' && t.status !== 'cancelled');
      if (trips.length > 0) {
        selectedTripId = trips[0].id;
      }
    } catch (e: any) {
      toast.error('Failed to load trips for target coordinates.');
    } finally {
      isLoadingTrips = false;
    }
  });

  function handleDragOver(e: DragEvent) {
    e.preventDefault();
    isDragging = true;
  }

  function handleDragLeave() {
    isDragging = false;
  }

  function handleDrop(e: DragEvent) {
    e.preventDefault();
    isDragging = false;
    
    if (e.dataTransfer?.files && e.dataTransfer.files.length > 0) {
      selectedFile = e.dataTransfer.files[0];
    }
  }

  function handleFileSelect(e: Event) {
    const input = e.target as HTMLInputElement;
    if (input.files && input.files.length > 0) {
      selectedFile = input.files[0];
    }
  }

  async function handleIngest() {
    if (!selectedTripId) {
      toast.error('Please select a target expedition trip.');
      return;
    }
    if (!selectedFile) {
      toast.error('Please drop or browse a data file to ingest.');
      return;
    }

    isUploading = true;
    validationErrors = [];
    hasAttemptedUpload = true;

    const formData = new FormData();
    formData.append('file', selectedFile);

    try {
      const token = localStorage.getItem('auth_token');
      const res = await fetch(`http://localhost:8000/api/v1/trips/${selectedTripId}/import`, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${token}`,
          'Accept': 'application/json'
        },
        body: formData
      });

      const data = await res.json();

      if (res.status === 422) {
        // Validation errors returned
        if (data.errors) {
          validationErrors = Object.keys(data.errors).map(key => {
            const errVal = data.errors[key];
            return Array.isArray(errVal) ? errVal[0] : errVal;
          });
        } else {
          validationErrors = [data.message];
        }
        toast.error('Data validation failed. Check ingestion issues below.');
      } else if (!res.ok) {
        throw new Error(data.message || 'Ingestion failed.');
      } else {
        toast.success('Data ingestion sequence completed successfully.');
        const targetTrip = trips.find(t => t.id === selectedTripId);
        if (targetTrip) {
          goto(`/trips/${targetTrip.slug}/${targetTrip.id}`);
        } else {
          goto('/trips');
        }
      }
    } catch (e: any) {
      console.error(e);
      toast.error(e.message || 'Fatal error during ingestion sequence.');
    } finally {
      isUploading = false;
    }
  }

  function handleDiscard() {
    selectedFile = null;
    validationErrors = [];
    hasAttemptedUpload = false;
  }
</script>

<svelte:head>
  <title>Data Ingestion | Plan Trip Tracker</title>
</svelte:head>

<div class="flex flex-col gap-6 md:gap-8 pb-10 max-w-6xl mx-auto font-sans">
  <div class="flex items-center gap-4 border-b border-border/50 pb-6">
    <div class="w-1 h-6 bg-primary"></div>
    <div>
      <h2 class="text-sm font-bold tracking-widest text-primary mb-1 uppercase">Portal // Import_Trip_Sequence</h2>
      <h1 class="text-4xl font-bold tracking-widest text-white">DATA_INGESTION</h1>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
    <!-- Left Column: Guidelines -->
    <div class="lg:col-span-5 flex flex-col gap-6">
      <CyberCard class="p-6 border-primary/30" glowState="primary">
        <div class="flex items-center gap-2 mb-4 border-b border-border/50 pb-3">
          <FileText class="w-5 h-5 text-primary" />
          <h3 class="font-bold uppercase tracking-widest text-white">System_Guidelines</h3>
        </div>
        
        <ol class="space-y-4 font-mono text-xs text-muted-foreground">
          <li class="flex gap-3">
            <span class="text-primary font-bold">01</span>
            <span>Ensure all timestamps are in ISO 8601 format (YYYY-MM-DD HH:mm:ss).</span>
          </li>
          <li class="flex gap-3">
            <span class="text-primary font-bold">02</span>
            <span>Segments must map chronologically matching your expedition timeline.</span>
          </li>
          <li class="flex gap-3">
            <span class="text-primary font-bold">03</span>
            <span>Maximum file size per ingestion sequence: 2MB.</span>
          </li>
        </ol>

        <div class="mt-8">
          <h4 class="text-[10px] font-bold text-muted-foreground uppercase tracking-widest mb-3">Structure Templates</h4>
          <div class="flex gap-3">
            <a 
              href="http://localhost:8000/api/v1/trips/import/template"
              class="flex-1 bg-[#16171E] border border-primary/30 hover:border-primary text-xs uppercase font-semibold font-mono tracking-widest py-3 flex items-center justify-center gap-2 text-white rounded-md transition-colors"
            >
              <FileSpreadsheet class="w-4 h-4 text-primary" /> CSV_Template.csv
            </a>
          </div>
        </div>
      </CyberCard>

      <CyberCard class="p-5 border-secondary/20 bg-linear-to-r from-secondary/5 to-transparent flex items-center gap-4">
        <div class="h-2.5 w-2.5 rounded-full bg-secondary animate-pulse shadow-[0_0_10px_rgba(0,230,184,0.8)]"></div>
        <div class="font-mono text-[10px] uppercase tracking-widest text-secondary">Global Sync Active // Encrypted_Stream_V3</div>
      </CyberCard>
    </div>

    <!-- Right Column: Drop Zone & Target -->
    <div class="lg:col-span-7 flex flex-col gap-6">
      <CyberCard class="p-6">
        <div class="mb-6">
          <label for="trip-select" class="block text-[10px] uppercase tracking-widest text-muted-foreground mb-2 font-mono">Select Target Expedition (Trip)</label>
          {#if isLoadingTrips}
            <div class="h-10 w-full bg-muted animate-pulse rounded-md"></div>
          {:else if trips.length === 0}
            <div class="p-3 border border-yellow-500/20 bg-yellow-500/5 text-yellow-500 text-xs rounded-lg flex items-center gap-2">
              <AlertTriangle class="h-4 w-4 shrink-0" />
              <span>No active trips found. Please establish a trip coordinates protocol first.</span>
            </div>
          {:else}
            <select 
              id="trip-select"
              bind:value={selectedTripId}
              class="w-full bg-[#0B0C10] border border-border text-white text-sm rounded-md p-3 font-mono focus:border-primary/50 focus:ring-1 focus:ring-primary/50 outline-none"
            >
              {#each trips as trip}
                <option value={trip.id}>{trip.title} ({trip.slug})</option>
              {/each}
            </select>
          {/if}
        </div>

        <!-- Dropzone -->
        <!-- svelte-ignore a11y_no_static_element_interactions -->
        <div 
          class="border-2 border-dashed rounded-xl p-10 flex flex-col items-center justify-center gap-4 transition-all min-h-64 cursor-pointer relative overflow-hidden bg-card/20
            {isDragging ? 'border-primary bg-primary/5 shadow-[0_0_20px_rgba(255,42,122,0.15)]' : 'border-border hover:border-muted-foreground'}"
          ondragover={handleDragOver}
          ondragleave={handleDragLeave}
          ondrop={handleDrop}
        >
          <input 
            type="file" 
            id="file-upload" 
            class="absolute inset-0 opacity-0 cursor-pointer" 
            accept=".csv,.txt,.xlsx,.xls"
            onchange={handleFileSelect}
            disabled={isUploading}
          />
          
          <UploadCloud class="w-12 h-12 text-primary animate-pulse" />
          
          {#if selectedFile}
            <div class="text-center font-mono">
              <p class="text-sm font-semibold text-white">{selectedFile.name}</p>
              <p class="text-xs text-muted-foreground">{(selectedFile.size / 1024).toFixed(1)} KB</p>
            </div>
          {:else}
            <div class="text-center font-mono">
              <p class="text-lg font-bold text-white mb-1">Ready for Ingestion</p>
              <p class="text-xs text-muted-foreground">Drag and drop your trip data archives here or <span class="text-primary hover:underline">browse files</span></p>
            </div>
          {/if}

          <div class="flex gap-2 mt-4">
            <span class="text-[8px] bg-[#16171E] border border-secondary/30 text-secondary px-2.5 py-1 rounded-sm uppercase tracking-widest font-mono">Verified CSV</span>
            <span class="text-[8px] bg-[#16171E] border border-yellow-500/30 text-yellow-500 px-2.5 py-1 rounded-sm uppercase tracking-widest font-mono font-semibold">Raw Excel</span>
          </div>
        </div>

        <!-- Action Button -->
        {#if selectedFile}
          <div class="flex gap-4 mt-6">
            <Button 
              onclick={handleDiscard}
              variant="outline"
              class="flex-1 font-mono tracking-widest uppercase border-border text-muted-foreground hover:text-white"
            >
              Discard
            </Button>
            <Button 
              onclick={handleIngest}
              disabled={isUploading}
              class="flex-1 bg-primary text-primary-foreground hover:bg-primary/80 font-mono tracking-widest uppercase font-bold shadow-[0_0_15px_rgba(255,42,122,0.4)]"
            >
              {isUploading ? 'Executing Ingestion...' : 'Execute Ingestion'}
            </Button>
          </div>
        {/if}
      </CyberCard>
    </div>
  </div>

  <!-- Bottom Column: Validation Errors -->
  {#if hasAttemptedUpload && validationErrors.length > 0}
    <section class="animate-in fade-in slide-in-from-bottom-4 duration-300">
      <div class="border border-red-500/20 bg-red-500/5 rounded-xl overflow-hidden">
        <div class="bg-red-500/10 border-b border-red-500/20 px-6 py-4 flex items-center justify-between">
          <div class="flex items-center gap-2 font-mono text-red-500">
            <AlertTriangle class="w-5 h-5 shrink-0" />
            <span class="font-bold tracking-widest uppercase">Validation_Failures_Detected ({validationErrors.length})</span>
          </div>
          <span class="text-[10px] font-mono tracking-widest uppercase text-red-500 bg-red-500/10 border border-red-500/30 px-2 py-0.5 rounded-sm">Sequence_Halted</span>
        </div>

        <div class="divide-y divide-red-500/15">
          {#each validationErrors as error, index}
            <div class="px-6 py-4 flex gap-4 items-start font-mono">
              <span class="text-red-500 font-bold text-lg">{String(index + 1).padStart(2, '0')}</span>
              <div class="flex-1">
                <div class="flex items-center justify-between mb-1">
                  <h4 class="font-bold text-white text-sm">Ingestion Error</h4>
                  <span class="text-[8px] bg-[#16171E] border border-red-500/30 text-red-500 px-2 py-0.5 rounded-sm uppercase tracking-widest font-semibold">ERR_INGESTION_VAL</span>
                </div>
                <p class="text-xs text-muted-foreground">
                  {error}
                </p>
              </div>
            </div>
          {/each}
        </div>
      </div>
    </section>
  {/if}
</div>
