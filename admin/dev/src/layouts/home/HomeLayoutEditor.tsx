import {
    DndContext,
    DragEndEvent,
    PointerSensor,
    closestCenter,
    useSensor,
    useSensors,
} from '@dnd-kit/core';
import {
    SortableContext,
    arrayMove,
    verticalListSortingStrategy,
} from '@dnd-kit/sortable';
import { Flex, FlexBlock, FlexItem } from '@wordpress/components';
import { useEffect, useState } from '@wordpress/element';

import { CategoryGridSectionEditor } from '../../sections/CategoryGridSectionEditor';
import { PostListSectionEditor } from '../../sections/PostListSectionEditor';
import { ProductListSectionEditor } from '../../sections/ProductListSectionEditor';
import { SliderSectionEditor } from '../../sections/SliderSectionEditor';
import { useConfigStore } from '../../store/configStore';
import { EditorHomeLayout } from '../../types/ui';
import { createId } from '../../utils/helper';
import { SectionLibrary } from './SectionLibrary';
import { SortableSectionCard } from './SortableSectionCard';

export default function HomeLayoutEditor() {
    const { config, updateLayout } = useConfigStore();

    const [sections, setSections] = useState<EditorHomeLayout[]>([]);
    const [activeId, setActiveId] = useState<string | null>(null);

    /* ================= DND ================= */
    const sensors = useSensors(
        useSensor(PointerSensor, {
            activationConstraint: { distance: 6 },
        })
    );

    /* ================= INIT ================= */
    useEffect(() => {
        if (!config) return;

        setSections((prev) => {
            if (prev.length) return prev;

            return (config.layouts.home as any[]).map((s) => ({
                ...s,
                id: createId(),
            }));
        });
    }, [config]);

    /* ================= STORE SYNC ================= */
    const syncToStore = (next: EditorHomeLayout[]) => {
        setSections(next);
        updateLayout(
            'home',
            next.map(({ id, ...rest }) => rest)
        );
    };

    /* ================= DND HANDLER ================= */
    const handleDragEnd = ({ active, over }: DragEndEvent) => {
        if (!over || active.id === over.id) return;

        const oldIndex = sections.findIndex((s) => s.id === active.id);
        const newIndex = sections.findIndex((s) => s.id === over.id);

        if (oldIndex === -1 || newIndex === -1) return;

        syncToStore(arrayMove(sections, oldIndex, newIndex));
    };

    /* ================= ACTIONS ================= */
    const addSection = (type: EditorHomeLayout['type']) => {
        const next: EditorHomeLayout[] = [
            ...sections,
            {
                id: createId(),
                type,
                title: type,
            } as EditorHomeLayout,
        ];

        syncToStore(next);
        setActiveId(next[next.length - 1].id);
    };

    const deleteSection = (id: string) => {
        syncToStore(sections.filter((s) => s.id !== id));
        if (activeId === id) setActiveId(null);
    };

    const updateSection = <T extends EditorHomeLayout>(
        id: string,
        updater: (section: T) => T
    ) => {
        syncToStore(
            sections.map((s) =>
                s.id === id ? updater(s as T) : s
            )
        );
    };

    /* ================= RENDER ================= */
    return (
        <Flex gap={6} align="flex-start">
            {/* LEFT */}
            <FlexItem style={{ width: 280 }}>
                <Flex direction="column" gap={3}>
                    <SectionLibrary onAdd={addSection} />
                </Flex>
            </FlexItem>

            {/* RIGHT */}
            <FlexBlock>
                <DndContext
                    sensors={sensors}
                    collisionDetection={closestCenter}
                    onDragEnd={handleDragEnd}
                >
                    <SortableContext
                        items={sections.map((s) => s.id)}
                        strategy={verticalListSortingStrategy}
                    >
                        {sections.map((section, index) => (
                            <SortableSectionCard
                                key={section.id}
                                id={section.id}
                                index={index}
                                title={`${index + 1}. ${section.type}`}
                                opened={activeId === section.id}
                                onToggle={() =>
                                    setActiveId(
                                        activeId === section.id
                                            ? null
                                            : section.id
                                    )
                                }
                                onDelete={() =>
                                    deleteSection(section.id)
                                }
                            >
                                {/* ===== EDITOR BY TYPE ===== */}
                                {section.type === 'slider' && (
                                    <SliderSectionEditor
                                        section={section}
                                        onChange={(next) =>
                                            updateSection(section.id, () => next)
                                        }
                                    />
                                )}

                                {section.type === 'categoryGrid' && (
                                    <CategoryGridSectionEditor
                                        section={section}
                                        onChange={(next) =>
                                            updateSection(section.id, () => next)
                                        }
                                    />
                                )}

                                {section.type === 'productList' && (
                                    <ProductListSectionEditor
                                        section={section}
                                        onChange={(next) =>
                                            updateSection(section.id, () => next)
                                        }
                                    />
                                )}

                                {section.type === 'postList' && (
                                    <PostListSectionEditor
                                        section={section}
                                        onChange={(next) =>
                                            updateSection(section.id, () => next)
                                        }
                                    />
                                )}
                            </SortableSectionCard>
                        ))}
                    </SortableContext>
                </DndContext>
            </FlexBlock>
        </Flex>
    );
}