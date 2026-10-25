import React from 'react';

function Detail({ label, children }) {
    return (
        <div>
            <dt>{label}</dt>
            <dd>{children}</dd>
        </div>
    );
}

function AddToCartForm({ cart }) {
    return (
        <form method="post" action={cart.action} className="site-book-detail__actions">
            <input type="hidden" name="_token" value={cart.csrfToken} />
            <input type="hidden" name="book_id" value={cart.bookId} />
            <button type="submit" className="site-book-detail__btn site-book-detail__btn--primary">Add to cart</button>
        </form>
    );
}

function RentForm({ rent, rentPrice, loginUrl }) {
    if (!rent) {
        return (
            <div className="site-book-detail__actions">
                <a href={loginUrl} className="site-book-detail__btn site-book-detail__btn--secondary">Sign in to rent</a>
            </div>
        );
    }

    return (
        <form method="post" action={rent.action} className="site-book-detail__actions">
            <input type="hidden" name="_token" value={rent.csrfToken} />
            <input type="hidden" name="book_id" value={rent.bookId} />
            <label htmlFor="rent-months" className="site-sr-only">Rent for</label>
            <select id="rent-months" name="months" defaultValue={rent.months[0]} className="site-book-detail__select">
                {rent.months.map((months) => (
                    <option key={months} value={months}>
                        Rent for {months} {months === 1 ? 'month' : 'months'} · {(rentPrice * months).toFixed(2)}$
                    </option>
                ))}
            </select>
            <button type="submit" className="site-book-detail__btn site-book-detail__btn--secondary">Rent</button>
        </form>
    );
}

export default function BookDetail({ book, catalogueUrl, genreUrl, authorUrl, cart, rent = null, loginUrl }) {
    const available = book.available > 0;
    const rentable = book.forRent > 0;

    return (
        <div className="site-book-detail">
            <nav aria-label="Breadcrumb" className="site-book-detail__crumbs">
                <a href={catalogueUrl}>Books</a>
                {book.genre && (
                    <>
                        <span aria-hidden="true">/</span>
                        <a href={genreUrl}>{book.genre}</a>
                    </>
                )}
                <span aria-hidden="true">/</span>
                <span aria-current="page">{book.title}</span>
            </nav>

            <div className="site-book-detail__main">
                <div className="site-book-detail__cover">
                    {book.img ? (
                        <img src={book.img} alt={`Cover of ${book.title}`} />
                    ) : (
                        <>
                            <span className="site-book-detail__cover-title">{book.title}</span>
                            {book.author && <span className="site-book-detail__cover-author">{book.author}</span>}
                        </>
                    )}
                </div>

                <div className="site-book-detail__info">
                    {book.genre && <span className="site-book-detail__eyebrow">{book.genre}</span>}
                    <h1 className="site-book-detail__title">{book.title}</h1>
                    {book.author && (
                        <p className="site-book-detail__author">
                            by <a href={authorUrl}>{book.author}</a>
                        </p>
                    )}

                    <div className="site-book-detail__price-row">
                        <span className="site-book-detail__price">{book.price.toFixed(2)}$</span>
                        <span className={'site-books__badge' + (available ? '' : ' is-out')}>
                            {available ? `${book.available} available` : 'Out of stock'}
                        </span>
                    </div>
                    {rentable && <p className="site-book-detail__rent-price">or rent for {book.rentPrice.toFixed(2)}$ / month</p>}

                    {book.description && <p className="site-book-detail__description">{book.description}</p>}

                    {(available || rentable) && (
                        <div className="site-book-detail__buy">
                            {available && <AddToCartForm cart={cart} />}
                            {rentable && <RentForm rent={rent} rentPrice={book.rentPrice} loginUrl={loginUrl} />}
                        </div>
                    )}

                    <dl className="site-book-detail__facts">
                        {book.genre && <Detail label="Genre">{book.genre}</Detail>}
                        <Detail label="Copies available">{book.available}</Detail>
                        <Detail label="Copies to rent">{book.forRent}</Detail>
                        {rentable && <Detail label="Rent price">{book.rentPrice.toFixed(2)}$ / month</Detail>}
                        {book.added && <Detail label="Added">{book.added}</Detail>}
                    </dl>
                </div>
            </div>
        </div>
    );
}
