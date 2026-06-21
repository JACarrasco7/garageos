/* eslint-disable @typescript-eslint/no-explicit-any */
declare module '@vue/runtime-core' {
    interface ComponentCustomProperties {
        $page: any
        route: any
    }
}

export {}
