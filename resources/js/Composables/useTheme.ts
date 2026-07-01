import { ref, watch, onMounted, onUnmounted } from 'vue';

type Theme = 'light' | 'dark' | 'system';

const STORAGE_KEY = 'theme';
const HIGH_CONTRAST_KEY = 'theme-hc';

export function useTheme() {
    const isDark = ref<boolean>(false);
    const theme = ref<Theme>((localStorage.getItem(STORAGE_KEY) as Theme) || 'system');
    const highContrast = ref<boolean>(localStorage.getItem(HIGH_CONTRAST_KEY) === '1');

    const setTheme = (newTheme: Theme) => {
        theme.value = newTheme;
        localStorage.setItem(STORAGE_KEY, newTheme);
        applyTheme(newTheme);
    };

    const applyTheme = (currentTheme: Theme) => {
        const isSystemDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        const shouldDark = currentTheme === 'dark' || (currentTheme === 'system' && isSystemDark);

        isDark.value = shouldDark;
        document.documentElement.classList.toggle('dark', shouldDark);
    };

    const toggleTheme = () => {
        const current = theme.value;
        if (current === 'light') setTheme('dark');
        else if (current === 'dark') setTheme('system');
        else setTheme('light');
    };

    const toggleHighContrast = () => {
        highContrast.value = !highContrast.value;
        localStorage.setItem(HIGH_CONTRAST_KEY, highContrast.value ? '1' : '0');
        document.documentElement.classList.toggle('high-contrast', highContrast.value);
    };

    const getThemeIcon = () => {
        if (theme.value === 'system') return 'Monitor';
        return isDark.value ? 'Moon' : 'Sun';
    };

    const getThemeLabel = () => {
        if (theme.value === 'system') return 'Sistema';
        return isDark.value ? 'Oscuro' : 'Claro';
    };

    const onSystemChange = () => {
        if (theme.value === 'system') applyTheme('system');
    };

    onMounted(() => {
        applyTheme(theme.value);
        if (highContrast.value) {
            document.documentElement.classList.add('high-contrast');
        }
        const mq = window.matchMedia('(prefers-color-scheme: dark)');
        mq.addEventListener('change', onSystemChange);
    });

    onUnmounted(() => {
        const mq = window.matchMedia('(prefers-color-scheme: dark)');
        mq.removeEventListener('change', onSystemChange);
    });

    watch(theme, applyTheme);

    return {
        theme,
        isDark,
        highContrast,
        setTheme,
        toggleTheme,
        toggleHighContrast,
        getThemeIcon,
        getThemeLabel,
    };
}
