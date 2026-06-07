<script lang="ts">
    import { Badge } from '$lib/components/ui/badge';
    import { cn } from '$lib/utils';

    type Status = 
        | 'draft' | 'planned' | 'active' | 'completed' | 'expired' | 'cancelled'
        | 'upcoming' | 'current' | 'skipped' | 'missed';

    let { status, class: className = '' }: { status: Status | string, class?: string } = $props();

    function getStatusConfig(s: string) {
        switch (s.toLowerCase()) {
            case 'active':
            case 'current':
                return { label: s.toUpperCase(), variant: 'default', colorClass: 'bg-green-500 hover:bg-green-600 text-white border-transparent' };
            case 'completed':
                return { label: s.toUpperCase(), variant: 'secondary', colorClass: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 border-transparent' };
            case 'planned':
            case 'upcoming':
                return { label: s.toUpperCase(), variant: 'outline', colorClass: 'border-blue-300 text-blue-600 dark:border-blue-700 dark:text-blue-400' };
            case 'draft':
                return { label: s.toUpperCase(), variant: 'outline', colorClass: 'border-slate-300 text-slate-600 dark:border-slate-700 dark:text-slate-400' };
            case 'cancelled':
            case 'missed':
            case 'expired':
            case 'skipped':
                return { label: s.toUpperCase(), variant: 'destructive', colorClass: 'bg-red-500 hover:bg-red-600 text-white' };
            default:
                return { label: s.toUpperCase(), variant: 'outline', colorClass: '' };
        }
    }

    let config = $derived(getStatusConfig(status));
</script>

<Badge variant={config.variant as any} class={cn(config.colorClass, className)}>
    {config.label}
</Badge>
