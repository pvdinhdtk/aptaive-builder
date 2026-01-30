import { NoticeList } from '@wordpress/components';
import { useDispatch, useSelect } from '@wordpress/data';
import { store as noticesStore } from '@wordpress/notices';

import AppLayouts from './pages/AppLayouts';
import AppSettings from './pages/AppSettings';

export default function App() {
    const page = new URLSearchParams(window.location.search).get('page');

    const notices = useSelect(
        (select) => {
            const all = select(noticesStore)
                .getNotices()
                .filter((n) => n.type !== 'snackbar');

            // 👉 chỉ lấy 1 notice (mới nhất)
            return all.length ? [all[all.length - 1]] : [];
        },
        []
    );

    const { removeNotice } = useDispatch(noticesStore);

    return (
        <>
            <div className="aptaive-admin-notices">
                <NoticeList
                    notices={notices}
                    onRemove={removeNotice}
                />
            </div>

            {page === 'aptaive-builder-layouts'
                ? <AppLayouts />
                : <AppSettings />
            }
        </>
    );
}
