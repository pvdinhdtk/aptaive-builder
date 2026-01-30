import {
    BaseControl,
    Button,
    Card,
    CardFooter,
    Flex,
    FlexBlock,
    FlexItem,
    __experimentalHeading as Heading
} from '@wordpress/components';
import { useEffect, useState } from '@wordpress/element';

import { TargetType } from '../../models/config';
import { IconItemListEditor } from '../../sections/IconItemListEditor';
import { useConfigStore } from '../../store/configStore';
import { EditorIconItem } from '../../types/ui';
import { createId } from '../../utils/helper';

export default function BottomNavigationLayoutEditor() {
    const { config, updateLayout, save } = useConfigStore();

    const [items, setItems] = useState<EditorIconItem[]>([]);

    /* ================= INIT ================= */
    useEffect(() => {
        if (!config) return;
        if (items.length) return;

        const rawItems = config.layouts.bottomNavigation.items ?? [];

        setItems(
            rawItems.map((item) => ({
                ...item,
                id: createId(),
            }))
        );
    }, [config]);

    /* ================= SYNC ================= */
    const syncToStore = (next: EditorIconItem[]) => {
        setItems(next);
        updateLayout(
            'bottomNavigation',
            {
                items: next.map(({ id, ...rest }) => rest),
            }
        );
    };

    /* ================= RENDER ================= */
    return (
        <Flex gap={6} align="flex-start">
            {/* LEFT – SAVE */}
            <FlexItem style={{ width: 280 }}>
                <Card>
                    <CardFooter>
                        <Button
                            variant="primary"
                            onClick={save}
                            style={{
                                width: '100%',
                                justifyContent: 'center',
                            }}
                        >
                            Save Changes
                        </Button>
                    </CardFooter>
                </Card>
            </FlexItem>

            {/* RIGHT – EDITOR */}
            <FlexBlock>
                <Flex direction="column" gap={3}>
                    <Heading level={4}>Bottom navigation items</Heading>
                    <BaseControl>
                        <IconItemListEditor
                            items={items}
                            maxItems={5}
                            createItem={() => ({
                                id: createId(),
                                icon: '',
                                label: '',
                                targetType: TargetType.home,
                                targetId: null,
                            })}
                            onChange={syncToStore}
                        />
                    </BaseControl>
                </Flex>
            </FlexBlock>
        </Flex>
    );
}
