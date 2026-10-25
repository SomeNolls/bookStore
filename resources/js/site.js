import React from 'react';
import { createRoot } from 'react-dom/client';
import Header from './components/Header';
import Banner from './components/Banner';
import Genres from './components/Genres';
import BookList from './components/BookList';
import Features from './components/Features';
import HowItWorks from './components/HowItWorks';
import Footer from './components/Footer';
import Catalogue from './components/Catalogue';
import BookDetail from './components/BookDetail';
import Cart from './components/Cart';
import Checkout from './components/Checkout';
import OrderConfirmed from './components/OrderConfirmed';
import Login from './components/Login';
import Register from './components/Register';
import Account from './components/Account';
import Admin from './components/Admin';

const components = { Header, Banner, Genres, BookList, Features, HowItWorks, Footer, Catalogue, BookDetail, Cart, Checkout, OrderConfirmed, Login, Register, Account, Admin };

// Mounts every <div data-react="Name" data-props="{...}"> rendered by resources/views/partials/react.blade.php
document.querySelectorAll('[data-react]').forEach((el) => {
    const Component = components[el.dataset.react];

    if (Component) {
        const props = JSON.parse(el.dataset.props || '{}');
        el.removeAttribute('data-props');
        createRoot(el).render(<Component {...props} />);
    }
});
