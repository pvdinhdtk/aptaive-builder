import {
    closestCenter,
    DndContext,
    DragEndEvent,
    PointerSensor,
    useSensor,
    useSensors,
} from '@dnd-kit/core';
import {
    arrayMove,
    SortableContext,
    verticalListSortingStrategy,
} from '@dnd-kit/sortable';
import { Button, Card, CardDivider } from '@wordpress/components';
import { EditorSliderItem } from '../types/ui';
import { SortableSliderItemEditor } from './SortableSliderItemEditor';

type Props = {
    items: EditorSliderItem[];
    createItem: () => EditorSliderItem;
    onChange: (items: EditorSliderItem[]) => void;
};

export function SliderItemListEditor({
    items,
    createItem,
    onChange,
}: Props) {
    const sensors = useSensors(
        useSensor(PointerSensor, { activationConstraint: { distance: 6 } })
    );

    function handleDragEnd(event: DragEndEvent) {
        const { active, over } = event;
        if (!over || active.id === over.id) return;

        const oldIndex = items.findIndex(
            (item) => item.id === active.id
        );
        const newIndex = items.findIndex(
            (item) => item.id === over.id
        );

        onChange(arrayMove(items, oldIndex, newIndex));
    }

    return (
        <>
            {items.length > 0 && (
                <DndContext
                    sensors={sensors}
                    collisionDetection={closestCenter}
                    onDragEnd={handleDragEnd}
                >
                    <SortableContext
                        items={items.map((i) => i.id)}
                        strategy={verticalListSortingStrategy}
                    >
                        <Card style={{ background: 'rgb(250 250 250)' }}>
                            {items.map((item, index) => (
                                <>
                                    <SortableSliderItemEditor
                                        key={item.id}
                                        id={item.id}
                                        item={item}
                                        onChange={(next) =>
                                            onChange(
                                                items.map((i) =>
                                                    i.id === next.id ? next : i
                                                )
                                            )
                                        }
                                        onRemove={() =>
                                            onChange(
                                                items.filter((i) => i.id !== item.id)
                                            )
                                        }
                                    />
                                    {index < items.length - 1 && <CardDivider />}
                                </>
                            ))}
                        </Card>
                    </SortableContext>
                </DndContext>
            )}

            <Button
                variant="primary"
                onClick={() => onChange([...items, createItem()])}
                style={{ marginTop: 12 }}
            >
                + Thêm item
            </Button>
        </>
    );
}
