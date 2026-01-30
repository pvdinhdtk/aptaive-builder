import {
    SelectControl,
    TextControl,
} from '@wordpress/components';
import { HomeLayout } from '../models/config';
import { useConfigStore } from '../store/configStore';

export function SectionEditor({
    section,
    index,
}: {
    section: HomeLayout;
    index: number;
}) {
    const { config, updateLayout } = useConfigStore();

    const sections = config!.layouts.home;

    const updateSection = (patch: any) => {
        const next = [...sections];
        next[index] = {
            ...next[index],
            ...patch,
        };

        updateLayout('home', next);
    };

    switch (section.type) {
        case 'slider':
            return (
                <TextControl
                    label="Tiêu đề Slider"
                    value={section.title || ''}
                    onChange={(v) =>
                        updateSection({ title: v })
                    }
                />
            );

        case 'productList':
            return (
                <>
                    <TextControl
                        label="Tiêu đề"
                        value={section.title || ''}
                        onChange={(v) =>
                            updateSection({ title: v })
                        }
                    />

                    <SelectControl
                        label="Số cột"
                        value={String(section.columns) as '1' | '2'}
                        options={[
                            { label: '1 cột', value: '1' },
                            { label: '2 cột', value: '2' },
                        ]}
                        onChange={(v) =>
                            updateSection({
                                columns: Number(v),       // 👈 convert ngược lại
                            })
                        }
                    />
                </>
            );

        default:
            return <em>Section này chưa có editor</em>;
    }
}
