import {
    BaseControl,
    Button,
} from '@wordpress/components';
import { MediaUpload } from '@wordpress/media-utils';
import { MediaUploadRenderProps, WPImageMedia } from '../types/media';

export function CardImageField({
    label,
    help,
    value,
    onChange,
}: {
    label: string;
    help?: string;
    value: string;
    onChange: (url: string) => void;
}) {
    return (
        <BaseControl label={label} help={help}>
            <MediaUpload
                allowedTypes={['image']}
                onSelect={(media: WPImageMedia) => onChange(media.url)}
                render={({ open }: MediaUploadRenderProps) => (
                    <>
                        {value ? (
                            <img
                                src={value}
                                alt={label}
                                className="aptaive-image-preview"
                                onClick={open}
                            />
                        ) : (
                            <div
                                className="aptaive-image-placeholder"
                                onClick={open}
                                role="button"
                                tabIndex={0}
                                onKeyDown={(e) => {
                                    if (e.key === 'Enter' || e.key === ' ') {
                                        open();
                                    }
                                }}
                            >
                                No file selected
                            </div>
                        )}

                        <Button
                            variant="secondary"
                            className="aptaive-image-button"
                            onClick={open}
                        >
                            {value ? 'Đổi ảnh' : 'Chọn ảnh'}
                        </Button>
                    </>
                )}
            />
        </BaseControl>
    );

}
