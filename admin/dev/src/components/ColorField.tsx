import {
    BaseControl,
    ColorPicker,
    Popover,
} from '@wordpress/components';
import { useState } from '@wordpress/element';

export function ColorField({
    label,
    value,
    onChange,
}: {
    label: string;
    value: string;
    onChange: (v: string) => void;
}) {
    const [open, setOpen] = useState(false);

    return (
        <BaseControl label={label}>
            <div className="aptaive-color-button-group">
                <span
                    className="aptaive-color-swatch"
                    style={{ backgroundColor: value }}
                />

                <button
                    type="button"
                    className="aptaive-color-button"
                    onClick={() => setOpen(!open)}
                >
                    Chọn màu
                </button>
            </div>

            {open && (
                <Popover
                    placement="bottom-start"
                    onClose={() => setOpen(false)}
                >
                    <ColorPicker
                        color={value}
                        onChangeComplete={(c) =>
                            onChange(
                                typeof c === 'string' ? c : c.hex
                            )
                        }
                        enableAlpha={false}
                    />
                </Popover>
            )}
        </BaseControl>
    );
}
