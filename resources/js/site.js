import React from 'react';
import { createRoot } from 'react-dom/client';
import Header from './components/Header';
import Banner from './components/Banner';
import Genres from './components/Genres';
import BookList from './components/BookList';
import Features from './components/Features';
import Newsletter from './components/Newsletter';
import Footer from './components/Footer';
import Catalogue from './components/Catalogue';
import BookDetail from './components/BookDetail';

const components = { Header, Banner, Genres, BookList, Features, Newsletter, Footer, Catalogue, BookDetail };

// Mounts every <div data-react="Name" data-props="{...}"> rendered by resources/views/partials/react.blade.php
document.querySelectorAll('[data-react]').forEach((el) => {
    const Component = components[el.dataset.react];

    if (Component) {
        const props = JSON.parse(el.dataset.props || '{}');
        el.removeAttribute('data-props');
        createRoot(el).render(<Component {...props} />);
    }
});
