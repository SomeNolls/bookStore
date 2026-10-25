import React, { useState } from 'react';
import Field from './Field';

const money = (amount) => amount.toFixed(2) + '$';
const monthsLabel = (months) => `${months} ${months === 1 ? 'month' : 'months'}`;

const TABS = [
    { id: 'books', label: 'All books' },
    { id: 'add', label: 'Add a book' },
    { id: 'orders', label: 'Orders' },
    { id: 'rentals', label: 'Rented books' },
];

function Hidden({ csrfToken, method = null }) {
    return (
        <>
            <input type="hidden" name="_token" value={csrfToken} />
            {method && <input type="hidden" name="_method" value={method} />}
        </>
    );
}

function Books({ books, csrfToken, onAdd }) {
    const confirmDelete = (event, title) => {
        if (!window.confirm(`Delete "${title}"? This can't be undone.`)) {
            event.preventDefault();
        }
    };

    return (
        <div className="site-cart__panel site-account__panel">
            <div className="site-admin__panel-head">
                <h2>All books <span className="site-cart__muted">({books.length})</span></h2>
                <a href="?tab=add" className="site-account__secondary site-admin__add" onClick={onAdd}>Add a book</a>
            </div>
            {books.length === 0 ? (
                <p className="site-cart__muted">There are no books in the database yet.</p>
            ) : (
                <div className="site-account__table-wrap">
                    <table className="site-account__table site-admin__table">
                        <thead>
                            <tr>
                                <th scope="col">Book</th>
                                <th scope="col">Genre</th>
                                <th scope="col">Price</th>
                                <th scope="col">Rent / month</th>
                                <th scope="col">For rent</th>
                                <th scope="col">In stock</th>
                                <th scope="col"><span className="site-sr-only">Actions</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            {books.map((book) => (
                                <tr key={book.id}>
                                    <td>
                                        <div className="site-admin__book">
                                            {book.img && <img src={book.img} alt="" />}
                                            <div>
                                                <strong>{book.title}</strong>
                                                {book.author && <div className="site-cart__muted site-cart__small">{book.author}</div>}
                                            </div>
                                        </div>
                                    </td>
                                    <td>{book.genre || '—'}</td>
                                    <td>{money(book.price)}</td>
                                    <td>{money(book.rentPrice)}</td>
                                    <td>
                                        <span className={book.forRent === 0 ? 'site-admin__out' : undefined}>{book.forRent}</span>
                                    </td>
                                    <td>
                                        <span className={book.available === 0 ? 'site-admin__out' : undefined}>{book.available}</span>
                                        <span className="site-cart__muted site-cart__small"> of {book.stock}</span>
                                    </td>
                                    <td className="site-account__actions">
                                        <div className="site-admin__actions">
                                            <a href={book.viewUrl} className="site-account__link">View</a>
                                            <a href={book.editUrl} className="site-account__link">Edit</a>
                                            <form method="post" action={book.deleteUrl} onSubmit={(event) => confirmDelete(event, book.title)}>
                                                <Hidden csrfToken={csrfToken} method="DELETE" />
                                                <button type="submit" className="site-account__link site-admin__danger">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            )}
        </div>
    );
}

function BookForm({ form, genres, csrfToken, booksUrl }) {
    const { values, errors, editing } = form;
    const fieldProps = { values, errors, idPrefix: 'book' };
    const fieldError = (name) => errors[name] && <span id={`book-${name}-error`} className="site-field__error">{errors[name]}</span>;
    const invalid = (name) => (errors[name] ? { 'aria-invalid': true, 'aria-describedby': `book-${name}-error` } : {});

    return (
        <form method="post" action={form.action} encType="multipart/form-data" className="site-cart__panel site-account__panel" noValidate>
            <Hidden csrfToken={csrfToken} method={editing ? 'PUT' : null} />
            {editing ? (
                <div className="site-admin__panel-head">
                    <h2>Edit “{editing.title}”</h2>
                    <a href={booksUrl} className="site-account__link">Back to all books</a>
                </div>
            ) : (
                <h2>Add a book</h2>
            )}

            <div className="site-checkout__grid">
                <Field name="title" label="Title" wide {...fieldProps} />
                <Field name="author" label="Author" {...fieldProps} />
                <div className="site-field">
                    <label htmlFor="book-genre">Genre</label>
                    <input id="book-genre" name="genre" list="book-genres" defaultValue={values.genre || ''} {...invalid('genre')} />
                    <datalist id="book-genres">
                        {genres.map((genre) => <option key={genre} value={genre} />)}
                    </datalist>
                    {fieldError('genre')}
                </div>
                <div className="site-field is-wide">
                    <label htmlFor="book-description">Description</label>
                    <textarea id="book-description" name="description" rows="5" defaultValue={values.description || ''} {...invalid('description')} />
                    {fieldError('description')}
                </div>
                <Field name="price" label="Price ($)" type="number" {...fieldProps} />
                <Field name="stock" label="Copies in stock" type="number" hint="Every copy the shop owns." {...fieldProps} />
                <Field name="forRent" label="Copies for rent" type="number" hint="Copies free to rent right now." {...fieldProps} />
                <Field name="rentPrice" label="Rent price ($ / month)" type="number" hint="What renting one copy costs per month." {...fieldProps} />
                <div className="site-field is-wide">
                    <label htmlFor="book-img">Cover image</label>
                    <div className="site-admin__cover">
                        {editing && <img src={editing.img} alt="Current cover" />}
                        <input id="book-img" name="img" type="file" accept="image/*" {...invalid('img')} />
                    </div>
                    {editing && <span className="site-field__hint">Leave empty to keep the current cover.</span>}
                    {fieldError('img')}
                </div>
            </div>

            <div>
                <button type="submit" className="site-cart__btn site-account__save">{editing ? 'Save changes' : 'Add book'}</button>
            </div>
        </form>
    );
}

function OrderRow({ order, statuses, csrfToken }) {
    const [open, setOpen] = useState(false);
    const detailsId = `order-${order.number}-details`;

    return (
        <>
            <tr>
                <td><strong>#{order.number}</strong></td>
                <td>{order.date}</td>
                <td>
                    {order.customer}
                    <div className="site-cart__muted site-cart__small">{order.email}</div>
                </td>
                <td>{order.count}</td>
                <td><strong>{money(order.total)}</strong></td>
                <td>
                    {/* Saves as soon as a new status is picked. */}
                    <form method="post" action={order.statusUrl}>
                        <Hidden csrfToken={csrfToken} method="PATCH" />
                        <label htmlFor={`order-${order.number}-status`} className="site-sr-only">Status of order #{order.number}</label>
                        <select
                            id={`order-${order.number}-status`}
                            name="status"
                            defaultValue={order.status}
                            className={`site-admin__status is-${order.status}`}
                            onChange={(event) => event.target.form.submit()}
                        >
                            {Object.entries(statuses).map(([value, label]) => <option key={value} value={value}>{label}</option>)}
                        </select>
                    </form>
                </td>
                <td className="site-account__actions">
                    <button type="button" className="site-account__link" aria-expanded={open} aria-controls={detailsId} onClick={() => setOpen(!open)}>
                        {open ? 'Hide details' : 'Details'}
                    </button>
                </td>
            </tr>
            {open && (
                <tr id={detailsId} className="site-account__details">
                    <td colSpan="7">
                        <div className="site-account__details-grid">
                            <div>
                                {order.items.map((item, index) => (
                                    <div key={index} className="site-cart__row">
                                        <span>
                                            {item.url ? <a href={item.url}>{item.title}</a> : item.title}
                                            {item.months ? ` · rent for ${monthsLabel(item.months)}` : ` × ${item.quantity}`}
                                        </span>
                                        <span>{money(item.price * (item.months || item.quantity))}</span>
                                    </div>
                                ))}
                                <div className="site-cart__row">
                                    <span className="site-cart__muted">Delivery</span>
                                    <span>{order.delivery === 0 ? 'Free' : money(order.delivery)}</span>
                                </div>
                            </div>
                            <dl>
                                <dt>Contact</dt>
                                <dd>{order.email}<br />{order.phone}</dd>
                                <dt>Delivery</dt>
                                <dd>{order.deliveryMethod}{order.address && <><br />{order.address}</>}</dd>
                                <dt>Payment</dt>
                                <dd>{order.payment}</dd>
                                {order.notes && (
                                    <>
                                        <dt>Notes</dt>
                                        <dd>{order.notes}</dd>
                                    </>
                                )}
                            </dl>
                        </div>
                    </td>
                </tr>
            )}
        </>
    );
}

function Orders({ orders, statuses, csrfToken }) {
    return (
        <div className="site-cart__panel site-account__panel">
            <h2>Orders <span className="site-cart__muted">({orders.length})</span></h2>
            {orders.length === 0 ? (
                <p className="site-cart__muted">No one has placed an order yet.</p>
            ) : (
                <div className="site-account__table-wrap">
                    <table className="site-account__table site-admin__table">
                        <thead>
                            <tr>
                                <th scope="col">Order</th>
                                <th scope="col">Date</th>
                                <th scope="col">Customer</th>
                                <th scope="col">Items</th>
                                <th scope="col">Total</th>
                                <th scope="col">Status</th>
                                <th scope="col"><span className="site-sr-only">Details</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            {orders.map((order) => <OrderRow key={order.number} order={order} statuses={statuses} csrfToken={csrfToken} />)}
                        </tbody>
                    </table>
                </div>
            )}
        </div>
    );
}

function RentalStatus({ rental }) {
    if (!rental.started) {
        return <span className="site-account__badge is-pending">Starts when order is completed</span>;
    }

    return (
        <span className={'site-account__badge ' + (rental.overdue ? 'is-cancelled' : 'is-delivered')}>
            {rental.overdue ? `Overdue, due ${rental.timeLeft}` : `Due ${rental.timeLeft}`}
        </span>
    );
}

function Rentals({ rentals, csrfToken }) {
    const confirmReturn = (event, title) => {
        if (!window.confirm(`Mark "${title}" as returned? The copy goes back up for rent.`)) {
            event.preventDefault();
        }
    };

    return (
        <div className="site-cart__panel site-account__panel">
            <h2>Rented books <span className="site-cart__muted">({rentals.length})</span></h2>
            {rentals.length === 0 ? (
                <p className="site-cart__muted">No books are rented out right now.</p>
            ) : (
                <div className="site-account__table-wrap">
                    <table className="site-account__table site-admin__table">
                        <thead>
                            <tr>
                                <th scope="col">Book</th>
                                <th scope="col">Customer</th>
                                <th scope="col">Order</th>
                                <th scope="col">Length</th>
                                <th scope="col">Return by</th>
                                <th scope="col">Status</th>
                                <th scope="col"><span className="site-sr-only">Return</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            {rentals.map((rental) => (
                                <tr key={rental.id}>
                                    <td>{rental.url ? <a href={rental.url}><strong>{rental.title}</strong></a> : <strong>{rental.title}</strong>}</td>
                                    <td>
                                        {rental.customer}
                                        {rental.email && <div className="site-cart__muted site-cart__small">{rental.email}</div>}
                                    </td>
                                    <td>
                                        {rental.order ? (
                                            <>
                                                #{rental.order}
                                                <div className="site-cart__muted site-cart__small">{rental.orderStatus}</div>
                                            </>
                                        ) : '—'}
                                    </td>
                                    <td>{rental.months ? monthsLabel(rental.months) : '—'}</td>
                                    <td>{rental.due || '—'}</td>
                                    <td><RentalStatus rental={rental} /></td>
                                    <td className="site-account__actions">
                                        <form method="post" action={rental.returnUrl} onSubmit={(event) => confirmReturn(event, rental.title)}>
                                            <Hidden csrfToken={csrfToken} />
                                            <button type="submit" className="site-account__link">Mark returned</button>
                                        </form>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            )}
        </div>
    );
}

export default function Admin({
    tab: initialTab = 'books',
    tabUrl,
    csrfToken,
    status = null,
    genres = [],
    statuses = {},
    books = [],
    bookForm,
    orders = [],
    rentals = [],
}) {
    const [tab, setTab] = useState(initialTab);
    // Editing lives on its own URL; picking any tab leaves it.
    const [editing, setEditing] = useState(Boolean(bookForm.editing));
    const urlFor = (id) => `${tabUrl}?tab=${id}`;

    // Switch tabs without a reload, but keep the URL in step so refreshes and form redirects land on the same tab.
    const pick = (event, id) => {
        event.preventDefault();
        setTab(id);
        setEditing(false);
        window.history.replaceState(null, '', urlFor(id));
    };

    // The add form starts empty unless it is coming back with errors.
    const addForm = bookForm.editing ? { ...bookForm, editing: null, values: { forRent: 0, rentPrice: 0 }, errors: {} } : bookForm;

    return (
        <div className="site-cart">
            <div className="site-account__intro">
                <h1 className="site-cart__heading">Admin panel</h1>
                <p className="site-cart__muted">Manage the books in the shop, the orders customers place and the books they rent.</p>
            </div>

            {status && <p className="site-cart__notice" role="status">{status}</p>}

            <div className="site-cart__layout">
                <nav aria-label="Admin" className="site-cart__panel site-account__nav">
                    {TABS.map((item) => (
                        <a
                            key={item.id}
                            href={urlFor(item.id)}
                            aria-current={item.id === tab ? 'page' : undefined}
                            onClick={(event) => pick(event, item.id)}
                        >
                            {item.label}
                        </a>
                    ))}
                </nav>

                <section className="site-account__content">
                    {tab === 'books' && (editing
                        ? <BookForm form={bookForm} genres={genres} csrfToken={csrfToken} booksUrl={urlFor('books')} />
                        : <Books books={books} csrfToken={csrfToken} onAdd={(event) => pick(event, 'add')} />)}
                    {tab === 'add' && <BookForm form={addForm} genres={genres} csrfToken={csrfToken} />}
                    {tab === 'orders' && <Orders orders={orders} statuses={statuses} csrfToken={csrfToken} />}
                    {tab === 'rentals' && <Rentals rentals={rentals} csrfToken={csrfToken} />}
                </section>
            </div>
        </div>
    );
}
