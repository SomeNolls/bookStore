import React, { useState } from 'react';

export function LogoMark({ size = 34 }) {
    return (
        <svg width={size} height={size} viewBox="0 0 34 34" aria-hidden="true">
            <rect x="2" y="4" width="6" height="26" fill="currentColor" />
            <rect x="10" y="4" width="6" height="26" fill="currentColor" />
            <rect x="18" y="4" width="6" height="26" fill="currentColor" />
            <rect x="25" y="4" width="6" height="26" fill="currentColor" transform="rotate(-12 28 30)" />
        </svg>
    );
}

function CartIcon() {
    return (
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
             strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
            <circle cx="9" cy="21" r="1" />
            <circle cx="20" cy="21" r="1" />
            <path d="M1 1h4l2.7 13.4a2 2 0 0 0 2 1.6h9.7a2 2 0 0 0 2-1.6L23 6H6" />
        </svg>
    );
}

function MenuIcon() {
    return (
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
             strokeWidth="1.8" strokeLinecap="round" aria-hidden="true">
            <path d="M3 6h18M3 12h18M3 18h18" />
        </svg>
    );
}

export default function Header({
    homeUrl,
    siteName = 'Book Store',
    links = [],
    currentPath = '/',
    searchUrl,
    search = '',
    cartUrl,
    cartCount = 0,
    loginUrl,
    registerUrl,
    logoutUrl,
    csrfToken,
    user = null,
    adminUrl = null,
}) {
    const [menuOpen, setMenuOpen] = useState(false);

    const isActive = (url) => {
        try {
            const link = new URL(url, window.location.origin);
            return !link.hash && link.pathname === currentPath;
        } catch (e) {
            return false;
        }
    };

    return (
        <header className="site-header">
            <div className="site-header__inner">
                <a className="site-header__logo" href={homeUrl}>
                    <LogoMark />
                    <span>{siteName}</span>
                </a>

                <button
                    type="button"
                    className="site-header__icon-btn site-header__toggler"
                    aria-label="Toggle navigation"
                    aria-expanded={menuOpen}
                    onClick={() => setMenuOpen(!menuOpen)}
                >
                    <MenuIcon />
                </button>

                <div className={'site-header__menu' + (menuOpen ? ' is-open' : '')}>
                    <nav className="site-header__tabs" aria-label="Main">
                        {links.map((link) => (
                            <a
                                key={link.label}
                                href={link.url}
                                className={'site-header__tab' + (isActive(link.url) ? ' is-active' : '')}
                            >
                                {link.label}
                            </a>
                        ))}
                    </nav>

                    <div className="site-header__actions">
                        <form className="site-header__search" action={searchUrl} method="GET" role="search">
                            <label htmlFor="site-header-search" className="site-header__sr-only">Search books</label>
                            <input
                                id="site-header-search"
                                type="search"
                                name="search"
                                placeholder="Search title, author, ISBN"
                                defaultValue={search}
                            />
                        </form>

                        <a
                            href={cartUrl}
                            className="site-header__icon-btn site-header__cart"
                            aria-label={`Cart, ${cartCount} items`}
                        >
                            <CartIcon />
                            {cartCount > 0 && <span className="site-header__badge">{cartCount}</span>}
                        </a>

                        {adminUrl && (
                            <a href={adminUrl} className="site-header__btn site-header__btn--accent">Admin Panel</a>
                        )}

                        {user ? (
                            <>
                                <a href={user.profileUrl} className="site-header__btn site-header__btn--ghost">{user.name}</a>
                                <form action={logoutUrl} method="POST" className="site-header__logout">
                                    <input type="hidden" name="_token" value={csrfToken} />
                                    <button type="submit" className="site-header__btn site-header__btn--primary">Logout</button>
                                </form>
                            </>
                        ) : (
                            <>
                                {loginUrl && (
                                    <a href={loginUrl} className="site-header__btn site-header__btn--ghost">Login</a>
                                )}
                                {registerUrl && (
                                    <a href={registerUrl} className="site-header__btn site-header__btn--primary">Register</a>
                                )}
                            </>
                        )}
                    </div>
                </div>
            </div>
        </header>
    );
}
