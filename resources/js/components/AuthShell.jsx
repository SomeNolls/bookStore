import React from 'react';
import { LogoMark } from './Header';

// The centred layout shared by Login and Register: the logo, the form's card, and a way back to the store.
export default function AuthShell({ homeUrl, children }) {
    return (
        <div className="site-auth">
            <a href={homeUrl} className="site-auth__logo">
                <LogoMark />
                <span>Book Store</span>
            </a>

            {children}

            <a href={homeUrl} className="site-auth__back">← Back to the store</a>
        </div>
    );
}
