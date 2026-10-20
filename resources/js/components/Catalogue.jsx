import React, { useRef } from 'react';
import { BookCard } from './BookList';

const FORM_ID = 'catalogue-filters';

// The current URL with the given query params changed (null removes one); always back to page 1.
function urlWith(params) {
    const url = new URL(window.location.href);
    url.searchParams.delete('page');
    Object.entries(params).forEach(([key, value]) => {
        if (value === null) {
            url.searchParams.delete(key);
        } else {
            url.searchParams.set(key, value);
        }
    });
    return url.toString();
}

function Pagination({ pages, prevUrl, nextUrl }) {
    if (pages.length === 0) {
        return null;
    }

    return (
        <nav aria-label="Pagination" className="site-catalogue__pages">
            {prevUrl && <a href={prevUrl} className="site-catalogue__page site-catalogue__page--wide">← Prev</a>}
            {pages.map((page) => (
                <a
                    key={page.number}
                    href={page.url}
                    className={'site-catalogue__page' + (page.active ? ' is-active' : '')}
                    aria-current={page.active ? 'page' : undefined}
                >
                    {page.number}
                </a>
            ))}
            {nextUrl && <a href={nextUrl} className="site-catalogue__page site-catalogue__page--wide">Next →</a>}
        </nav>
    );
}

export default function Catalogue({
    title = 'All books',
    action,
    filters = {},
    genres = [],
    sorts = [],
    total = 0,
    books = [],
    pagination = { pages: [] },
}) {
    const formRef = useRef(null);
    const submit = () => formRef.current.requestSubmit();

    return (
        <div className="site-catalogue">
            <div className="site-catalogue__head">
                <h1 className="site-catalogue__title">{title}</h1>
                <p className="site-catalogue__count">
                    Showing {total} {total === 1 ? 'result' : 'results'}
                    {filters.search ? ` for “${filters.search}”` : ''}
                </p>
            </div>

            <div className="site-catalogue__body">
                <form
                    id={FORM_ID}
                    ref={formRef}
                    method="get"
                    action={action}
                    className="site-catalogue__filters"
                    aria-label="Filters"
                >
                    <div className="site-catalogue__group">
                        <label htmlFor="catalogue-search" className="site-catalogue__legend">Search</label>
                        <input
                            id="catalogue-search"
                            type="search"
                            name="search"
                            defaultValue={filters.search}
                            placeholder="Title or author"
                            className="site-catalogue__input"
                        />
                    </div>

                    <fieldset className="site-catalogue__group">
                        <legend className="site-catalogue__legend">Genre</legend>
                        {['', ...genres].map((genre) => (
                            <label key={genre || 'all'} className="site-catalogue__genre">
                                <input
                                    type="radio"
                                    name="genre"
                                    value={genre}
                                    defaultChecked={(filters.genre || '') === genre}
                                    onChange={submit}
                                />
                                <span>{genre || 'All'}</span>
                            </label>
                        ))}
                    </fieldset>

                    <fieldset className="site-catalogue__group">
                        <legend className="site-catalogue__legend">Price</legend>
                        <div className="site-catalogue__price">
                            <label htmlFor="catalogue-min" className="site-sr-only">Minimum price</label>
                            <input id="catalogue-min" type="number" name="min" min="0" step="0.01"
                                   defaultValue={filters.min} placeholder="Min" className="site-catalogue__input" />
                            <span aria-hidden="true">–</span>
                            <label htmlFor="catalogue-max" className="site-sr-only">Maximum price</label>
                            <input id="catalogue-max" type="number" name="max" min="0" step="0.01"
                                   defaultValue={filters.max} placeholder="Max" className="site-catalogue__input" />
                        </div>
                    </fieldset>

                    <label className="site-catalogue__check">
                        <input type="checkbox" name="inStock" value="1" defaultChecked={filters.inStock} onChange={submit} />
                        In stock only
                    </label>

                    <div className="site-catalogue__actions">
                        <button type="submit" className="site-catalogue__btn site-catalogue__btn--primary">Apply filters</button>
                        <a href={action} className="site-catalogue__btn">Clear filters</a>
                    </div>
                </form>

                <section className="site-catalogue__results">
                    <div className="site-catalogue__toolbar">
                        <div className="site-catalogue__chips">
                            {filters.genre && (
                                <span className="site-catalogue__chip">
                                    {filters.genre}
                                    <a href={urlWith({ genre: null })} aria-label="Remove genre filter">×</a>
                                </span>
                            )}
                            {filters.search && (
                                <span className="site-catalogue__chip">
                                    “{filters.search}”
                                    <a href={urlWith({ search: null })} aria-label="Remove search">×</a>
                                </span>
                            )}
                        </div>
                        <div className="site-catalogue__sort">
                            <label htmlFor="catalogue-sort">Sort by</label>
                            <select
                                id="catalogue-sort"
                                name="sort"
                                form={FORM_ID}
                                defaultValue={filters.sort}
                                onChange={submit}
                                className="site-catalogue__input"
                            >
                                {sorts.map((sort) => (
                                    <option key={sort.value} value={sort.value}>{sort.label}</option>
                                ))}
                            </select>
                        </div>
                    </div>

                    {books.length > 0 ? (
                        <div className="site-books__grid site-catalogue__grid">
                            {books.map((book) => (
                                <BookCard key={book.url} book={book} />
                            ))}
                        </div>
                    ) : (
                        <p className="site-books__empty">No books match these filters.</p>
                    )}

                    <Pagination {...pagination} />
                </section>
            </div>
        </div>
    );
}
