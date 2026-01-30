import { useSortable } from '@dnd-kit/sortable';
import { CSS } from '@dnd-kit/utilities';
import { EditorIconItem } from '../types/ui';
import { IconItemEditor } from './IconItemEditor';

type Props = {
  id: string;
  item: EditorIconItem;
  onChange: (next: EditorIconItem) => void;
  onRemove?: () => void;
};

export function SortableIconItemEditor({
  id,
  item,
  onChange,
  onRemove,
}: Props) {
  const {
    setNodeRef,
    attributes,
    listeners,
    transform,
    transition,
  } = useSortable({ id });

  const style: React.CSSProperties = {
    transform: CSS.Transform.toString(
      transform ? { ...transform, scaleX: 1, scaleY: 1 } : null
    ),
    transition,
  };

  return (
    <div ref={setNodeRef} style={style}>
      <IconItemEditor
        item={item}
        onChange={onChange}
        onRemove={onRemove}
        dragHandleProps={{ ...attributes, ...listeners }}
      />
    </div>
  );
}
