import React from 'react';

export function BookCard({ book }) {
    const available = book.available > 0;
    return (
        <a href={book.url} className="site-book__card">
            <div className="site-book__cover">
                {book.img ? (
                    <img src={book.img} alt="" loading="lazy" />
                ) : (
                    <span className="site-book__cover-title">{book.title}</span>
                )}
            </div>
            <div className="site-books__info">
                <span className="site-books__title">{book.title}</span>
                {(book.author || book.genre) && (
                    <span className="site-books__description">
                        {[book.author, book.genre].filter(Boolean).join(' · ')}
                    </span>
                )}
                <div className="site-books__meta">
                    <span className="site-books__price">{book.price.toFixed(2)}$</span>
                    <span className={'site-books__badge' + (available ? '' : ' is-out')}>
                        {available ? `${book.available} available` : 'Out of stock'}
                    </span>
                </div>

            </div>
        </a>
    );
}

export default function BookList({ title = 'Books', id, viewAllUrl, books = [], emptyText = 'No books have been added yet.' }) {
    return (
        <section className="site-section site-books" id={id}>
            <div className="site-books__head">
                <h2 className="site-section__title">{title}</h2>
                {viewAllUrl && <a href={viewAllUrl} className="site-books__all">View all →</a>}
            </div>

            {books.length > 0 ? (
                <div className="site-books__grid">
                    {books.map((book) => (
                        <BookCard key={book.url} book={book} />
                    ))}
                </div>
            ) : (
                <p className="site-books__empty">{emptyText}</p>
            )}
        </section>
    );
}
