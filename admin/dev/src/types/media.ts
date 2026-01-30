export type WPImageMedia = {
    id: number;
    url: string;
    alt?: string;
    sizes?: Record<
        string,
        {
            url: string;
            width: number;
            height: number;
        }
    >;
};

export type MediaUploadRenderProps = {
    open: () => void;
};
