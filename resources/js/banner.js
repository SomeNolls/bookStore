import React from 'react';
import { createRoot } from 'react-dom/client';
import Banner from './components/Banner';

const container = document.getElementById('site-banner');

if (container) {
    const props = JSON.parse(container.dataset.props || '{}');
    createRoot(container).render(<Banner {...props} />);
}
