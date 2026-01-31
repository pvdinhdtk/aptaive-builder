import {
    Card,
    CardBody,
    CardHeader
} from '@wordpress/components';

import Skeleton from 'react-loading-skeleton';
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

    if (loading || !config) {
        return (
            <div className="aptaive-admin">
                <h2>App Layouts</h2>

                <div className="aptaive-layout-grid">
                    {Array.from({ length: 2 }).map((_, i) => (
                        <LayoutCardSkeleton key={i} />
                    ))}
                </div>
            </div>
        );
    }

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
            <h2>App Layouts</h2>

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

function LayoutCardSkeleton() {
    return (
        <Card className="aptaive-layout-card">
            <CardHeader>
                <div style={{ width: '100%' }}>
                    <Skeleton height={18} width="60%" />
                </div>
            </CardHeader>

            <CardBody>
                <Skeleton count={2} height={14} style={{ marginBottom: 6 }} />
                <Skeleton width="40%" height={12} />
            </CardBody>
        </Card>
    );
}
