import { TextControl } from "@wordpress/components";
import { EditorPostListSection } from "../types/ui";

type Props = {
    section: EditorPostListSection;
    onChange: (next: EditorPostListSection) => void;
};

export function PostListSectionEditor({
    section,
    onChange,
}: Props) {
    return (
        <>
            <TextControl
                label="Tiêu đề"
                value={section.title ?? ''}
                placeholder="Tiêu đề"
                onChange={(value) =>
                    onChange({
                        ...section,
                        title: value || null,
                    })
                }
            />

            <TextControl
                label="Số cột"
                type="number"
                help="Số cột hiển thị bài viết giá trị cho phép 1 hoặc 2"
                min={1}
                max={2}
                value={section.columns}
                onChange={(value) =>
                    onChange({
                        ...section,
                        columns: Number(value) === 2 ? 2 : 1,
                    })
                }
            />

            <TextControl
                label="Giới hạn"
                type="number"
                help="Số lượng bài viết hiển thị hoặc bỏ trống để hiển thị tất cả bài viết"
                min={1}
                max={2}
                value={section.limit ?? ''}
                onChange={(value) =>
                    onChange({
                        ...section,
                        limit: Number(value) || null,
                    })
                }
            />

            <TextControl
                label="ID chuyên mục"
                value={section.categoryIds?.join(', ') ?? ''}
                placeholder="ID chuyên mục bài viết, ví dụ: 1, 2, 3 (để trống = tất cả)"
                onChange={(value) => {
                    const ids =
                        value.trim() === ''
                            ? null
                            : value
                                .split(',')
                                .map((v) => v.trim())
                                .filter(Boolean)
                                .map(Number)
                                .filter((n) => !Number.isNaN(n));

                    onChange({
                        ...section,
                        categoryIds: ids == null ? [] : ids,
                    });
                }}
            />
        </>
    );
}
