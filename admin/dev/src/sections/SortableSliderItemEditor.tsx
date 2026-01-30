import { useSortable } from '@dnd-kit/sortable';
import { CSS } from '@dnd-kit/utilities';
import { EditorSliderItem } from '../types/ui';
import { SliderItemEditor } from './SliderItemEditor';

type Props = {
  id: string;
  item: EditorSliderItem;
  onChange: (next: EditorSliderItem) => void;
  onRemove?: () => void;
};

export function SortableSliderItemEditor({
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
      <SliderItemEditor
        item={item}
        onChange={onChange}
        onRemove={onRemove}
        dragHandleProps={{ ...attributes, ...listeners }}
      />
    </div>
  );
}
