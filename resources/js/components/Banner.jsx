import React from 'react';

const COVER_COLORS = ['#C2410C', '#F7F6F2', '#264653'];

// Shown when the store has fewer than three books.
const FALLBACK_COVERS = [
    { title: 'Moby-Dick', subtitle: 'H. Melville' },
    { title: 'Pride & Prejudice', subtitle: 'Jane Austen' },
    { title: 'Dracula', subtitle: 'Bram Stoker' },
];

function Cover({ book, index }) {
    const className = `site-banner__cover site-banner__cover--${index + 1}`;
    const style = { background: COVER_COLORS[index] };
    const content = (
        <>
            <span className="site-banner__cover-title">{book.title}</span>
            {book.subtitle && <span className="site-banner__cover-subtitle">{book.subtitle}</span>}
        </>
    );

    return book.url ? (
        <a href={book.url} className={className} style={style}>{content}</a>
    ) : (
        <div className={className} style={style} aria-hidden="true">{content}</div>
    );
}

export default function Banner({
    eyebrow,
    title,
    text,
    primaryAction,
    secondaryAction,
    books = [],
}) {
    const covers = [...books, ...FALLBACK_COVERS.slice(books.length)].slice(0, 3);

    return (
        <section className="site-banner">
            <div className="site-banner__inner">
                <div className="site-banner__copy">
                    {eyebrow && <span className="site-banner__eyebrow">{eyebrow}</span>}
                    <h1 className="site-banner__title">{title}</h1>
                    {text && <p className="site-banner__text">{text}</p>}
                    <div className="site-banner__actions">
                        {primaryAction && (
                            <a href={primaryAction.url} className="site-banner__btn site-banner__btn--primary">
                                {primaryAction.label}
                            </a>
                        )}
                        {secondaryAction && (
                            <a href={secondaryAction.url} className="site-banner__btn site-banner__btn--outline">
                                {secondaryAction.label}
                            </a>
                        )}
                    </div>
                </div>

                <div className="site-banner__covers">
                    {covers.map((book, i) => (
                        <Cover key={book.url || book.title} book={book} index={i} />
                    ))}
                </div>
            </div>
        </section>
    );
}
