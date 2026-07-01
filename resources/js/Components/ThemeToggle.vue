<script setup lang="ts">
import { Sun, Moon, Monitor, Contrast } from 'lucide-vue-next';
import { useTheme } from '@/Composables/useTheme';
import { Button } from '@/Components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
    DropdownMenuCheckboxItem,
} from '@/Components/ui/dropdown-menu';

const { theme, highContrast, setTheme, toggleHighContrast, getThemeIcon, getThemeLabel } =
    useTheme();
</script>

<template>
    <DropdownMenu>
        <DropdownMenuTrigger as-child>
            <Button
                variant="ghost"
                size="icon"
                :aria-label="getThemeLabel()"
                class="hover:bg-accent/10 transition-all duration-300"
            >
                <Sun
                    v-if="getThemeIcon() === 'Sun'"
                    class="h-5 w-5 transition-all duration-300 hover:rotate-90"
                />
                <Moon
                    v-else-if="getThemeIcon() === 'Moon'"
                    class="h-5 w-5 transition-all duration-300 hover:rotate-12"
                />
                <Monitor v-else class="h-5 w-5 transition-all duration-300" />
            </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent align="end" class="min-w-48">
            <DropdownMenuLabel class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">
                Apariencia
            </DropdownMenuLabel>
            <DropdownMenuItem
                class="gap-3"
                :class="theme === 'light' && 'bg-accent/10'"
                @select="setTheme('light')"
            >
                <Sun class="h-4 w-4" />
                <span>Claro</span>
            </DropdownMenuItem>
            <DropdownMenuItem
                class="gap-3"
                :class="theme === 'dark' && 'bg-accent/10'"
                @select="setTheme('dark')"
            >
                <Moon class="h-4 w-4" />
                <span>Oscuro</span>
            </DropdownMenuItem>
            <DropdownMenuItem
                class="gap-3"
                :class="theme === 'system' && 'bg-accent/10'"
                @select="setTheme('system')"
            >
                <Monitor class="h-4 w-4" />
                <span>Sistema</span>
            </DropdownMenuItem>
            <DropdownMenuSeparator />
            <DropdownMenuCheckboxItem
                :model-value="highContrast"
                @update:model-value="toggleHighContrast"
                class="gap-3"
            >
                <Contrast class="h-4 w-4" />
                <span>Alto contraste</span>
            </DropdownMenuCheckboxItem>
        </DropdownMenuContent>
    </DropdownMenu>
</template>
