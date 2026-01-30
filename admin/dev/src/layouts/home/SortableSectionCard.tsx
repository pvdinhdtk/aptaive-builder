import { useSortable } from '@dnd-kit/sortable';
import { CSS } from '@dnd-kit/utilities';
import { Button, Card, CardBody, CardHeader } from '@wordpress/components';
import { closeSmall } from '@wordpress/icons';
import clsx from 'clsx';

export function SortableSectionCard({
    id,
    index,
    title,
    opened,
    onToggle,
    onDelete,
    children,
}: {
    id: string;
    index: number;
    title: string;
    opened: boolean;
    onToggle: () => void;
    onDelete: () => void;
    children: React.ReactNode;
}) {
    const {
        setNodeRef,
        attributes,
        listeners,
        transform,
        transition,
        isDragging,
    } = useSortable({ id });

    const style: React.CSSProperties = {
        transform: CSS.Transform.toString(
            transform
                ? { ...transform, scaleX: 1, scaleY: 1 }
                : null
        ),
        transition,
    };

    return (
        <Card
            ref={setNodeRef}
            style={style}
            className={clsx(
                'aptaive-section-card',
                isDragging && 'is-dragging',
                opened && 'is-opened'
            )}
        >
            {/* HEADER */}
            <CardHeader onClick={onToggle}  {...attributes}
                {...listeners}>
                <div>
                    <span
                        className="drag-handle"
                    >
                        ⋮⋮
                    </span>

                    <strong> {title}</strong>
                </div>

                <Button
                    icon={closeSmall}
                    size="small"
                    onClick={onDelete}
                    label="Xoá section"
                />
            </CardHeader>

            {/* BODY */}
            {opened && (
                <CardBody>
                    {children}
                </CardBody>
            )}
        </Card>
    );
}
