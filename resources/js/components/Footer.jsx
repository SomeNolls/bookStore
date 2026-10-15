import React from 'react';

export default function Footer({ siteName = 'Book Store', details = [], columns = [] }) {
    return (
        <footer className="site-footer">
            <div className="site-footer__inner">
                <div className="site-footer__brand">
                    <span className="site-footer__name">{siteName}</span>
                    {details.map((line) => (
                        <span key={line}>{line}</span>
                    ))}
                </div>

                {columns.map((column) => (
                    <div key={column.title} className="site-footer__column">
                        <strong>{column.title}</strong>
                        {column.links.map((link) => (
                            <a key={link.label} href={link.url}>{link.label}</a>
                        ))}
                    </div>
                ))}
            </div>
        </footer>
    );
}
