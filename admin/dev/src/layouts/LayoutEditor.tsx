

import { Config } from '../models/config';
import BottomNavigationLayoutEditor from './bottomNavigation/BottomNavigationLayoutEditor';
import HomeLayoutEditor from './home/HomeLayoutEditor';

export type LayoutKey = keyof Config['layouts'];

export function LayoutEditor({
    layoutKey,
}: {
    layoutKey: LayoutKey;
}) {
    switch (layoutKey) {
        case 'home':
            return <HomeLayoutEditor />;

        case 'bottomNavigation':
            return <BottomNavigationLayoutEditor />;

        default:
            return null;
    }
}
