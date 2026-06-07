<script lang="ts">
    import imageCompression from 'browser-image-compression';
    import { UploadCloud, X } from 'lucide-svelte';
    import { Button } from '$lib/components/ui/button';
    import { cn } from '$lib/utils';

    type FileWithPreview = {
        file: File;
        preview: string;
        compressed: boolean;
    };

    let {
        onFilesSelected,
        maxFiles = 3,
        class: className = ''
    }: {
        onFilesSelected: (files: File[]) => void;
        maxFiles?: number;
        class?: string;
    } = $props();

    let files = $state<FileWithPreview[]>([]);
    let isDragging = $state(false);
    let isCompressing = $state(false);
    let fileInput: HTMLInputElement;

    async function handleFiles(newFiles: FileList | File[]) {
        const allowedFiles = Array.from(newFiles).filter(f => f.type.startsWith('image/'));
        
        if (files.length + allowedFiles.length > maxFiles) {
            alert(`You can only upload up to ${maxFiles} images.`);
            return;
        }

        isCompressing = true;
        try {
            for (const file of allowedFiles) {
                const options = {
                    maxSizeMB: 1,
                    maxWidthOrHeight: 1920,
                    useWebWorker: true
                };
                
                const compressedFile = await imageCompression(file, options);
                
                files = [...files, {
                    file: compressedFile,
                    preview: URL.createObjectURL(compressedFile),
                    compressed: true
                }];
            }
            onFilesSelected(files.map(f => f.file));
        } catch (error) {
            console.error("Error compressing image:", error);
            alert("Failed to compress image");
        } finally {
            isCompressing = false;
        }
    }

    function removeFile(index: number) {
        const file = files[index];
        URL.revokeObjectURL(file.preview);
        files = files.filter((_, i) => i !== index);
        onFilesSelected(files.map(f => f.file));
    }

    function onDragOver(e: DragEvent) {
        e.preventDefault();
        isDragging = true;
    }

    function onDragLeave(e: DragEvent) {
        e.preventDefault();
        isDragging = false;
    }

    function onDrop(e: DragEvent) {
        e.preventDefault();
        isDragging = false;
        if (e.dataTransfer?.files) {
            handleFiles(e.dataTransfer.files);
        }
    }
</script>

<div class={cn("w-full", className)}>
    <!-- svelte-ignore a11y_click_events_have_key_events -->
    <!-- svelte-ignore a11y_no_static_element_interactions -->
    <div 
        class={cn(
            "border-2 border-dashed rounded-lg p-6 flex flex-col items-center justify-center transition-colors cursor-pointer",
            isDragging ? "border-primary bg-primary/10" : "border-slate-300 dark:border-slate-700 hover:border-primary/50",
            files.length >= maxFiles ? "opacity-50 pointer-events-none" : ""
        )}
        onclick={() => fileInput.click()}
        ondragover={onDragOver}
        ondragleave={onDragLeave}
        ondrop={onDrop}
    >
        <UploadCloud class="w-10 h-10 text-slate-400 mb-2" />
        <p class="text-sm text-slate-600 dark:text-slate-400 text-center">
            <span class="font-semibold text-primary">Click to upload</span> or drag and drop
        </p>
        <p class="text-xs text-slate-500 mt-1">
            PNG, JPG, WEBP up to 5MB (Max {maxFiles} images)
        </p>
        <input 
            type="file" 
            bind:this={fileInput}
            onchange={(e) => handleFiles(e.currentTarget.files!)}
            accept="image/png, image/jpeg, image/webp"
            multiple
            class="hidden" 
        />
        
        {#if isCompressing}
            <div class="mt-4 text-sm text-primary animate-pulse">Compressing images...</div>
        {/if}
    </div>

    {#if files.length > 0}
        <div class="mt-4 grid grid-cols-3 gap-4">
            {#each files as file, i}
                <div class="relative group aspect-square rounded-md overflow-hidden bg-slate-100 dark:bg-slate-800">
                    <img src={file.preview} alt="Preview" class="object-cover w-full h-full" />
                    <button 
                        class="absolute top-1 right-1 bg-black/50 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-500"
                        onclick={() => removeFile(i)}
                        aria-label="Remove image"
                    >
                        <X class="w-4 h-4" />
                    </button>
                </div>
            {/each}
        </div>
    {/if}
</div>
