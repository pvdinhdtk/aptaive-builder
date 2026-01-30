
import { Config } from '../models/config';
import apiFetch from './apiFetch';

export const getConfig = (): Promise<Config> => {
    return apiFetch({ path: '/config' });
};

export const saveConfig = (config: Config) => {
    return apiFetch({
        path: '/config',
        method: 'POST',
        data: config,
    });
};

