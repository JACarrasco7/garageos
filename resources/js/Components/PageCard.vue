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
    <Card :class="['border-0 bg-transparent shadow-none', className]">
        <CardHeader v-if="title">
            <CardTitle>{{ title }}</CardTitle>
        </CardHeader>
        <CardContent>
            <slot />
        </CardContent>
        <CardFooter v-if="$slots.footer">
            <slot name="footer" />
        </CardFooter>
    </Card>
</template>
