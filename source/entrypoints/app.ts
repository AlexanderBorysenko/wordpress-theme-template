import '../styles/app.scss';

import initHeaderComponent from '../components/header-component';
import PageAnchors from '../scripts/anchors';

window.addEventListener('load', () => {
    initHeaderComponent();
    PageAnchors.init();
});