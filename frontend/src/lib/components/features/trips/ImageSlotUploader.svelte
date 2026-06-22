<script lang="ts">
  import { ImagePlus, X } from 'lucide-svelte';
  import ImageGallery from '$lib/components/shared/ImageGallery.svelte';

  let { images = $bindable([]) } = $props<{ images: { base64: string, name: string }[] }>();

  interface ImageSlot {
    name: string;
    preview: string;
    base64: string;
  }

  let slots = $state<(ImageSlot | null)[]>([null, null, null]);
  let fileInputs = $state<(HTMLInputElement | null)[]>([null, null, null]);

  // Gallery state
  let isGalleryOpen = $state(false);
  let galleryImages = $state<string[]>([]);
  let galleryIndex = $state(0);

  function openGallery(index: number) {
    if (!slots[index]) return;
    const validSlots = slots.filter((s): s is ImageSlot => s !== null);
    galleryImages = validSlots.map(s => s.preview);
    galleryIndex = validSlots.findIndex(s => s.preview === slots[index]!.preview);
    isGalleryOpen = true;
  }

  function triggerUpload(index: number) {
    fileInputs[index]?.click();
  }

  function handleFileChange(index: number, event: Event) {
    const input = event.target as HTMLInputElement;
    if (!input.files || input.files.length === 0) return;

    const file = input.files[0];
    const preview = URL.createObjectURL(file);

    const reader = new FileReader();
    reader.onload = () => {
      const base64 = reader.result as string;
      slots[index] = {
        name: file.name,
        preview,
        base64
      };
      updateImages();
    };
    reader.readAsDataURL(file);
  }

  function removeImage(index: number, event: Event) {
    event.stopPropagation();
    if (slots[index]) {
      URL.revokeObjectURL(slots[index]!.preview);
      slots[index] = null;
      if (fileInputs[index]) {
        fileInputs[index]!.value = ''; // Reset input value
      }
      updateImages();
    }
  }

  function updateImages() {
    images = slots
      .filter((s): s is ImageSlot => s !== null)
      .map(s => ({
        base64: s.base64,
        name: s.name
      }));
  }
</script>

<div class="grid grid-cols-3 gap-4 mb-8">
  {#each Array(3) as _, i}
    <input
      type="file"
      accept="image/*"
      class="hidden"
      bind:this={fileInputs[i]}
      onchange={(e) => handleFileChange(i, e)}
    />

    {#if slots[i]}
      <!-- svelte-ignore a11y_click_events_have_key_events -->
      <!-- svelte-ignore a11y_no_static_element_interactions -->
      <div 
        class="relative aspect-4/5 md:aspect-video w-full group rounded-xl border border-primary/30 overflow-hidden flex flex-col items-center justify-center bg-card cursor-pointer hover:border-primary/50"
        onclick={() => openGallery(i)}
      >
        <img src={slots[i]!.preview} alt="Preview" class="absolute inset-0 w-full h-full object-cover" />
        <button
          type="button"
          onclick={(e) => removeImage(i, e)}
          class="absolute top-2 right-2 bg-[#0B0C10]/80 border border-primary/50 text-primary p-1 rounded-full hover:bg-primary hover:text-primary-foreground transition-colors z-20"
        >
          <X class="h-3 w-3" />
        </button>
      </div>
    {:else}
      <button
        type="button"
        onclick={() => triggerUpload(i)}
        class="relative aspect-4/5 md:aspect-video w-full group rounded-xl border border-dashed border-border hover:border-primary/50 transition-colors bg-card hover:bg-card/80 overflow-hidden flex flex-col items-center justify-center gap-2"
      >
        <ImagePlus class="h-6 w-6 text-primary group-hover:scale-110 transition-transform shadow-[0_0_15px_rgba(255,42,122,0.3)]" />
        <span class="text-[10px] text-muted-foreground uppercase tracking-widest font-semibold">SLOT_0{i + 1}</span>
      </button>
    {/if}
  {/each}
</div>

<ImageGallery bind:isOpen={isGalleryOpen} images={galleryImages} bind:currentIndex={galleryIndex} />
