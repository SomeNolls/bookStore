import React from 'react';
import { LogoMark } from './Header';

const SOCIAL_ICONS = {
    instagram: (
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"
             strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
            <rect x="3" y="3" width="18" height="18" rx="5" />
            <circle cx="12" cy="12" r="4" />
        </svg>
    ),
    facebook: (
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"
             strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
            <path d="M15 3h-2a4 4 0 0 0-4 4v3H7v4h2v7h4v-7h3l1-4h-4V7a1 1 0 0 1 1-1h2z" />
        </svg>
    ),
    youtube: (
        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
            <path d="M8 5v14l11-7z" />
        </svg>
    ),
};

export default function Footer({ siteName = 'Book Store', columns = [], social = [], legal = [], year }) {
    return (
        <footer className="site-footer">
            <div className="site-footer__inner">
                <div className="site-footer__columns">
                    {columns.map((column) => (
                        <div key={column.title} className="site-footer__column">
                            <strong className="site-footer__heading">{column.title}</strong>
                            {column.links.map((link) => (
                                <a key={link.label} href={link.url}>{link.label}</a>
                            ))}
                        </div>
                    ))}

                    <div className="site-footer__column">
                        <strong className="site-footer__heading">Stay in touch</strong>
                        <div className="site-footer__social">
                            {social.map((link) => (
                                <a key={link.label} href={link.url} aria-label={link.label} className="site-footer__social-link">
                                    {SOCIAL_ICONS[link.icon]}
                                </a>
                            ))}
                        </div>
                        <div className="site-footer__wordmark" aria-hidden="true">
                            <LogoMark size={32} />
                            <span>{siteName}</span>
                        </div>
                    </div>
                </div>

                <div className="site-footer__bottom">
                    <span>© {year} {siteName}</span>
                    <nav aria-label="Legal" className="site-footer__legal">
                        {legal.map((link) => (
                            <a key={link.label} href={link.url}>{link.label}</a>
                        ))}
                    </nav>
                </div>
            </div>
        </footer>
    );
}
