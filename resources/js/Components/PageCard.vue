<script setup lang="ts">
import Card from '@/Components/ui/card/Card.vue';
import CardHeader from '@/Components/ui/card/CardHeader.vue';
import CardTitle from '@/Components/ui/card/CardTitle.vue';
import CardContent from '@/Components/ui/card/CardContent.vue';
import CardFooter from '@/Components/ui/card/CardFooter.vue';

defineProps<{
    title?: string;
    className?: string;
}>();
</script>

<template>
    <div :class="['glass-panel rounded-3xl overflow-hidden transition-all duration-300 hover:shadow-lg h-full', className]">
        <Card :class="['border-0 bg-transparent shadow-none h-full flex flex-col']">
            <CardHeader v-if="title" class="p-5 pb-3">
                <CardTitle>{{ title }}</CardTitle>
            </CardHeader>
            <CardContent :class="['p-5 pt-0 flex-1']">
                <slot />
            </CardContent>
            <CardFooter v-if="$slots.footer" class="p-5 pt-0">
                <slot name="footer" />
            </CardFooter>
        </Card>
    </div>
</template>
