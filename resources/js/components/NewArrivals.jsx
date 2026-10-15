import React from 'react';

const COVER_COLORS = ['#264653', '#C2410C', '#6B4E9B', '#1F3A2E', '#8A5A2B'];

function BookCard({ book, index }) {
    const available = book.available > 0;

    return (
        <a href={book.url} className="site-arrivals__card">
            <div className="site-arrivals__cover" style={{ background: COVER_COLORS[index % COVER_COLORS.length] }}>
                {book.img ? (
                    <img src={book.img} alt="" loading="lazy" />
                ) : (
                    <span className="site-arrivals__cover-title">{book.title}</span>
                )}
            </div>
            <div className="site-arrivals__info">
                <span className="site-arrivals__title">{book.title}</span>
                {book.description && <span className="site-arrivals__description">{book.description}</span>}
                <span className={'site-arrivals__badge' + (available ? '' : ' is-out')}>
                    {available ? `${book.available} available` : 'Out of stock'}
                </span>
            </div>
        </a>
    );
}

export default function NewArrivals({ title = 'New arrivals', viewAllUrl, books = [] }) {
    return (
        <section className="site-section site-arrivals" id="new-arrivals">
            <div className="site-arrivals__head">
                <h2 className="site-section__title">{title}</h2>
                {viewAllUrl && <a href={viewAllUrl} className="site-arrivals__all">View all →</a>}
            </div>

            {books.length > 0 ? (
                <div className="site-arrivals__grid">
                    {books.map((book, i) => (
                        <BookCard key={book.url} book={book} index={i} />
                    ))}
                </div>
            ) : (
                <p className="site-arrivals__empty">No books have been added yet.</p>
            )}
        </section>
    );
}
