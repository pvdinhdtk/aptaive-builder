import {
    Button,
    CardBody,
    Flex,
    FlexItem,
    __experimentalInputControl as InputControl,
    SelectControl,
    TextControl
} from '@wordpress/components';
import { MediaUpload } from '@wordpress/media-utils';
import { TargetType } from '../models/config';
import { MediaUploadRenderProps, WPImageMedia } from '../types/media';
import { EditorIconItem } from '../types/ui';

type Props = {
    item: EditorIconItem;
    onChange: (next: EditorIconItem) => void;
    onRemove?: () => void;
    dragHandleProps?: any;
};

export function IconItemEditor({
    item,
    onChange,
    onRemove,
    dragHandleProps,
}: Props) {
    return (
        <>
            <CardBody>
                <Flex gap={2} wrap align="flex-start">
                    {/* Drag handle */}
                    <FlexItem
                        {...dragHandleProps}
                        style={{
                            cursor: 'grab',
                            paddingTop: 28, // canh với label input
                            userSelect: 'none',
                        }}
                        className="drag-handle"
                    >
                        ⋮⋮
                    </FlexItem>

                    {/* Label – vừa */}
                    <FlexItem style={{ flexBasis: '200px', flexGrow: 1 }}>
                        <TextControl
                            label="Label"
                            value={item.label}
                            onChange={(v) =>
                                onChange({ ...item, label: v })
                            }
                        />
                    </FlexItem>

                    {/* Icon – RỘNG */}
                    <FlexItem style={{ flexBasis: '360px', flexGrow: 2 }}>
                        <InputControl
                            label="Icon"
                            value={item.icon}
                            onChange={(v) =>
                                onChange({ ...item, icon: v ?? '' })
                            }
                            suffix={
                                <MediaUpload
                                    onSelect={(media: WPImageMedia) =>
                                        onChange({ ...item, icon: media.url })
                                    }
                                    allowedTypes={['image']}
                                    render={({ open }: MediaUploadRenderProps) => (
                                        <Button
                                            variant="secondary"
                                            onClick={open}
                                        >
                                            Chọn ảnh
                                        </Button>
                                    )}
                                />
                            }
                        />
                    </FlexItem>

                    {/* Target – NHỎ */}
                    <FlexItem style={{ flexBasis: '140px' }}>
                        <SelectControl
                            label="Target"
                            value={item.targetType}
                            options={Object.values(TargetType).map(
                                (t) => ({
                                    label: t,
                                    value: t,
                                })
                            )}
                            onChange={(v) =>
                                onChange({
                                    ...item,
                                    targetType: v as TargetType,
                                })
                            }
                        />
                    </FlexItem>

                    {/* ID – RẤT NHỎ */}
                    <FlexItem style={{ flexBasis: '90px' }}>
                        <TextControl
                            label="ID"
                            type="number"
                            value={item.targetId ?? ''}
                            onChange={(v) =>
                                onChange({
                                    ...item,
                                    targetId: v ? Number(v) : null,
                                })
                            }
                        />
                    </FlexItem>

                    {/* Remove */}
                    {onRemove && (
                        <FlexItem style={{ paddingTop: 22 }}>
                            <Button
                                isDestructive
                                variant="tertiary"
                                onClick={onRemove}
                            >
                                Xoá
                            </Button>
                        </FlexItem>
                    )}
                </Flex>
            </CardBody>
        </>
    );
}