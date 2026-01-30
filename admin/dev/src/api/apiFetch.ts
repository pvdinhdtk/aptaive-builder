import apiFetch from '@wordpress/api-fetch';

apiFetch.use((options, next) => {
    return next({
        ...options,
        path: `/aptaive/v1${options.path}`,
    });
});

export default apiFetch;
