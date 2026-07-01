<script setup lang="ts">
import type { SidebarProps } from "."
import { cn } from "@/lib/utils"
import { Sheet, SheetContent } from '@/Components/ui/sheet'
import { SIDEBAR_WIDTH_MOBILE, useSidebar } from "./utils"

defineOptions({
  inheritAttrs: false,
})

const props = withDefaults(defineProps<SidebarProps>(), {
  side: "left",
  variant: "sidebar",
  collapsible: "offcanvas",
})

const { isMobile, setOpenMobile, openMobile } = useSidebar()
</script>

<template>
  <!-- Mobile: Sheet drawer -->
  <Sheet v-if="isMobile" :open="openMobile" v-bind="$attrs" @update:open="setOpenMobile">
    <SheetContent
      data-sidebar="sidebar"
      data-mobile="true"
      :side="side"
      class="w-[--sidebar-width] bg-sidebar p-0 text-sidebar-foreground [&>button]:hidden"
      :style="{ '--sidebar-width': SIDEBAR_WIDTH_MOBILE }"
    >
      <div class="flex h-full w-full flex-col">
        <slot />
      </div>
    </SheetContent>
  </Sheet>

  <!-- Desktop: Simple fixed-width sidebar -->
  <div
    v-else
    :class="cn(
      'hidden md:flex',
      'flex-col',
      'w-[--sidebar-width]',
      'h-screen',
      'bg-sidebar',
      'text-sidebar-foreground',
      'border-r',
      'border-sidebar-border',
      props.class
    )"
    v-bind="$attrs"
  >
    <slot />
  </div>
</template>
