import React from 'react';
import { createRoot } from 'react-dom/client';
import Header from './components/Header';

const container = document.getElementById('site-header');

if (container) {
    const props = JSON.parse(container.dataset.props || '{}');
    createRoot(container).render(<Header {...props} />);
}
