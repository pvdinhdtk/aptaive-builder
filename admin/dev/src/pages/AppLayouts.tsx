import {
    Card,
    CardBody,
    CardHeader,
    Spinner,
} from '@wordpress/components';

import { LayoutEditor } from '../layouts/LayoutEditor';
import { Config } from '../models/config';
import { useConfigStore } from '../store/configStore';

export type LayoutKey = keyof Config['layouts'];

export const LAYOUT_META: Record<
    LayoutKey,
    {
        title: string;
        description: string;
    }
> = {
    home: {
        title: 'Home',
        description: 'Quản lý các section hiển thị ở trang chủ ứng dụng',
    },
    bottomNavigation: {
        title: 'Bottom Navigation',
        description: 'Cấu hình thanh menu điều hướng phía dưới app',
    },
};

function isLayoutKey(
    value: string | null,
    layouts: Config['layouts']
): value is LayoutKey {
    return !!value && value in layouts;
}

export default function AppLayouts() {
    const { config, loading } = useConfigStore();

    if (loading || !config) return <Spinner />;

    const params = new URLSearchParams(window.location.search);
    const layoutParam = params.get('layout');

    /**
     * 👉 Có layout param + hợp lệ → render editor
     */
    if (isLayoutKey(layoutParam, config.layouts)) {
        return <LayoutEditor layoutKey={layoutParam} />;
    }

    /**
     * 👉 Không có param → render list
     */
    const layouts = Object.keys(config.layouts) as LayoutKey[];

    return (
        <div className="aptaive-admin">
            <h1>App Layouts</h1>

            <div className="aptaive-layout-grid">
                {layouts.map((key) => {
                    const meta = LAYOUT_META[key];

                    return (
                        <Card
                            key={key}
                            className="aptaive-layout-card aptaive-clickable"
                            onClick={() => {
                                window.location.href =
                                    `admin.php?page=aptaive-builder-layouts&layout=${key}`;
                            }}
                        >
                            <CardHeader>
                                <strong>{meta.title}</strong>
                            </CardHeader>

                            <CardBody>
                                <p>{meta.description}</p>
                                <span className="aptaive-card-hint">
                                    Nhấn để chỉnh sửa →
                                </span>
                            </CardBody>
                        </Card>
                    );
                })}
            </div>
        </div>
    );
}
