import { dispatch } from '@wordpress/data';
import { store as noticesStore } from '@wordpress/notices';
import { create } from 'zustand';
import { getConfig, saveConfig } from '../api/config';
import { Config } from '../models/config';

type ConfigState = {
    config: Config | null;
    loading: boolean;

    load: () => Promise<void>;
    setConfig: (config: Config) => void;

    // APP
    updateApp: <K extends keyof Config['app']>(
        key: K,
        value: Config['app'][K]
    ) => void;

    // LAYOUTS (GENERIC)
    updateLayout: <K extends keyof Config['layouts']>(
        key: K,
        value: Config['layouts'][K]
    ) => void;

    save: () => Promise<void>;
};

export const useConfigStore = create<ConfigState>((set, get) => ({
    config: null,
    loading: true,

    load: async () => {
        try {
            const cfg = await getConfig();
            if (cfg && Object.keys(cfg).length) {
                set({ config: cfg, loading: false });
            }
        } catch (e) {
            console.warn('Load config failed');
        }
    },

    setConfig: (config) => set({ config }),

    /* ================= APP ================= */

    updateApp: (key, value) => {
        set((state) => ({
            config: {
                ...state.config!,
                app: {
                    ...state.config!.app,
                    [key]: value,
                },
            },
        }));
    },

    /* ================= LAYOUTS ================= */

    updateLayout: (key, value) => {
        set((state) => ({
            config: {
                ...state.config!,
                layouts: {
                    ...state.config!.layouts,
                    [key]: value,
                },
            },
        }));
    },

    /* ================= SAVE ================= */

    save: async () => {
        const { config } = get();
        if (!config) return;

        try {
            await saveConfig(config);
            window.scrollTo({ top: 0, behavior: 'smooth' });

            dispatch(noticesStore).createSuccessNotice(
                'Lưu cài đặt thành công',
                { isDismissible: true }
            );
        } catch (e) {
            dispatch(noticesStore).createErrorNotice(
                'Không thể lưu cài đặt',
                { isDismissible: true }
            );
        }
    },
}));
