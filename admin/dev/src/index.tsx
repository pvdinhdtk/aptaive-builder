import { createRoot } from '@wordpress/element';
import 'react-loading-skeleton/dist/skeleton.css';
import App from './App';
import { useConfigStore } from './store/configStore';
import './styles/admin.css';

const el = document.getElementById('aptaive-admin-root');

if (el) {
    useConfigStore.getState().load();
    createRoot(el).render(<App />);
}