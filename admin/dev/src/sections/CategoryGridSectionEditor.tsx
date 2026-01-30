import { BaseControl, SelectControl, TextControl } from '@wordpress/components';
import { useEffect, useState } from '@wordpress/element';
import { CategoryGridMode, TargetType } from '../models/config';
import { EditorCategoryGridSection, EditorIconItem } from '../types/ui';
import { createId } from '../utils/helper';
import { IconItemListEditor } from './IconItemListEditor';

type Props = {
    section: EditorCategoryGridSection;
    onChange: (next: EditorCategoryGridSection) => void;
};

function createEmptyItem(): EditorIconItem {
    return {
        id: createId(),
        icon: '',
        label: '',
        targetType: TargetType.home,
        targetId: null,
    };
}

export function CategoryGridSectionEditor({
    section,
    onChange,
}: Props) {
    const [editorItems, setEditorItems] = useState<EditorIconItem[]>([]);

    /* ================= INIT EDITOR ITEMS ================= */
    useEffect(() => {
        if (section.mode !== CategoryGridMode.custom) return;

        setEditorItems(
            (section.items ?? []).map((item) => ({
                ...item,
                id: createId(),
            }))
        );
    }, [section.mode]);

    return (
        <>
            <TextControl
                label="Tiêu đề"
                value={section.title ?? ''}
                onChange={(value) =>
                    onChange({
                        ...section,
                        title: value || null,
                    })
                }
            />

            <SelectControl
                label="Chế độ"
                value={section.mode}
                options={Object.values(CategoryGridMode).map((mode) => ({
                    label: mode,
                    value: mode,
                }))}
                onChange={(value) =>
                    onChange({
                        ...section,
                        mode: value as CategoryGridMode,
                        items:
                            value === CategoryGridMode.custom
                                ? section.items ?? []
                                : undefined,
                    })
                }
            />

            <TextControl
                label="Số hàng"
                type="number"
                min={1}
                max={2}
                value={section.rows}
                onChange={(value) =>
                    onChange({
                        ...section,
                        rows: Number(value),
                    })
                }
            />

            {section.mode === CategoryGridMode.custom && (
                <BaseControl label="Items">
                    <IconItemListEditor
                        items={editorItems}
                        createItem={createEmptyItem}
                        onChange={(items) => {
                            setEditorItems(items);

                            // 👇 STRIP ID TRƯỚC KHI LƯU
                            onChange({
                                ...section,
                                items: items.map(({ id, ...rest }) => rest),
                            });
                        }}
                    />
                </BaseControl>
            )}
        </>
    );
}
