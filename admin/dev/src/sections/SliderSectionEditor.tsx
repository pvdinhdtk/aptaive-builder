import { BaseControl, TextControl } from "@wordpress/components";
import { useEffect, useState } from "@wordpress/element";
import { TargetType } from "../models/config";
import { EditorSliderItem, EditorSliderSection } from "../types/ui";
import { createId } from "../utils/helper";
import { SliderItemListEditor } from "./SliderItemListEditor";

type Props = {
    section: EditorSliderSection;
    onChange: (next: EditorSliderSection) => void;
};
function createEmptyItem(): EditorSliderItem {
    return {
        id: crypto.randomUUID(),
        image: '',
        targetType: TargetType.home,
        targetId: null,
    };
}

export function SliderSectionEditor({ section, onChange }: Props) {
    const [editorItems, setEditorItems] = useState<EditorSliderItem[]>([]);

    useEffect(() => {
        setEditorItems(
            (section.items ?? []).map((item) => ({
                ...item,
                id: createId(),
            }))
        );
    }, []);

    // number -> "a / b"
    const numberToRatio = (value: number, maxDen = 20) => {
        let bestNum = 1;
        let bestDen = 1;
        let bestErr = Infinity;

        for (let den = 1; den <= maxDen; den++) {
            const num = Math.round(value * den);
            const err = Math.abs(value - num / den);

            if (err < bestErr) {
                bestNum = num;
                bestDen = den;
                bestErr = err;
            }
        }

        return `${bestNum} / ${bestDen}`;
    };

    // "a / b" -> number
    const ratioToNumber = (v: string) => {
        const m = v.match(/(\d+)\s*\/\s*(\d+)/);
        return m ? Number(m[1]) / Number(m[2]) : null;
    };

    const [ratioText, setRatioText] = useState(
        section.aspectRatio
            ? numberToRatio(section.aspectRatio)
            : ''
    );

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

            <TextControl
                label="Tỉ lệ"
                placeholder="Ví dụ: 8 / 3"
                value={ratioText}
                onChange={(v) => {
                    setRatioText(v); // CHO GÕ TỰ DO
                }}
                onBlur={() => {
                    if (!ratioText) {
                        onChange({
                            ...section,
                            aspectRatio: undefined,
                        });
                        return;
                    }

                    const n = ratioToNumber(ratioText);
                    if (n == null) {
                        // sai format → revert
                        setRatioText(
                            section.aspectRatio
                                ? numberToRatio(section.aspectRatio)
                                : ''
                        );
                        return;
                    }

                    onChange({
                        ...section,
                        aspectRatio: n,
                    });
                }}
            />

            <BaseControl label="Items">
                <SliderItemListEditor
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
        </>
    );
}
