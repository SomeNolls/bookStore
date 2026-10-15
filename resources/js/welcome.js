import React from 'react';
import { createRoot } from 'react-dom/client';

function Welcome() {
    return (
        <div>
            <p>Welcome to the Book Store</p>
            <button>Hello</button>
        </div>
    );
}

const container = document.querySelector('.js-container');

if (container) {
    createRoot(container).render(<Welcome />);
}
