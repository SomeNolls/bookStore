import React, { useState } from 'react';
import Field from './Field';

const money = (amount) => amount.toFixed(2) + '$';
const monthsLabel = (months) => `${months} ${months === 1 ? 'month' : 'months'}`;

const TABS = [
    { id: 'orders', label: 'My orders' },
    { id: 'rentals', label: 'Rented books' },
    { id: 'profile', label: 'Profile' },
];

function Empty({ title, text, catalogueUrl }) {
    return (
        <div className="site-account__empty">
            <h3>{title}</h3>
            <p className="site-cart__muted">{text}</p>
            <a href={catalogueUrl} className="site-cart__btn">Browse books</a>
        </div>
    );
}

function OrderRow({ order }) {
    const [open, setOpen] = useState(false);
    const detailsId = `order-${order.number}-details`;

    return (
        <>
            <tr>
                <td><strong>#{order.number}</strong></td>
                <td>{order.date}</td>
                <td>{order.count}</td>
                <td><strong>{money(order.total)}</strong></td>
                <td><span className={`site-account__badge is-${order.status}`}>{order.statusLabel}</span></td>
                <td className="site-account__actions">
                    <button type="button" className="site-account__link" aria-expanded={open} aria-controls={detailsId} onClick={() => setOpen(!open)}>
                        {open ? 'Hide details' : 'Details'}
                    </button>
                </td>
            </tr>
            {open && (
                <tr id={detailsId} className="site-account__details">
                    <td colSpan="6">
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
                                <dt>Delivery</dt>
                                <dd>{order.deliveryMethod}{order.address && <><br />{order.address}</>}</dd>
                                <dt>Payment</dt>
                                <dd>{order.payment}</dd>
                            </dl>
                        </div>
                    </td>
                </tr>
            )}
        </>
    );
}

function Orders({ orders, catalogueUrl }) {
    return (
        <div className="site-cart__panel site-account__panel">
            <h2>My orders</h2>
            {orders.length === 0 ? (
                <Empty title="No orders yet" text="Your orders and their delivery status will show up here." catalogueUrl={catalogueUrl} />
            ) : (
                <div className="site-account__table-wrap">
                    <table className="site-account__table">
                        <thead>
                            <tr>
                                <th scope="col">Order</th>
                                <th scope="col">Date</th>
                                <th scope="col">Items</th>
                                <th scope="col">Total</th>
                                <th scope="col">Status</th>
                                <th scope="col"><span className="site-sr-only">Details</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            {orders.map((order) => <OrderRow key={order.number} order={order} />)}
                        </tbody>
                    </table>
                </div>
            )}
        </div>
    );
}

function Rentals({ rentals, csrfToken, catalogueUrl }) {
    return (
        <div className="site-cart__panel site-account__panel">
            <h2>Rented books</h2>
            {rentals.length === 0 ? (
                <Empty title="No rented books" text="Books you rent show up here with their return date." catalogueUrl={catalogueUrl} />
            ) : (
                <div className="site-account__table-wrap">
                    <table className="site-account__table">
                        <thead>
                            <tr>
                                <th scope="col">Book</th>
                                <th scope="col">Rented</th>
                                <th scope="col">Return by</th>
                                <th scope="col">Status</th>
                                <th scope="col"><span className="site-sr-only">Return</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            {rentals.map((rental) => (
                                <tr key={rental.id}>
                                    <td><a href={rental.url}><strong>{rental.title}</strong></a></td>
                                    <td>{rental.rented}</td>
                                    <td>{rental.due || '—'}</td>
                                    <td>
                                        {rental.started ? (
                                            <span className={'site-account__badge ' + (rental.overdue ? 'is-cancelled' : 'is-pending')}>
                                                {rental.overdue ? `Overdue, due ${rental.timeLeft}` : `Due ${rental.timeLeft}`}
                                            </span>
                                        ) : (
                                            <span className="site-account__badge is-shipped">
                                                {rental.months ? `${monthsLabel(rental.months)} from` : 'Starts'} when order #{rental.order} is completed
                                            </span>
                                        )}
                                    </td>
                                    <td className="site-account__actions">
                                        {rental.started && (
                                            <form method="post" action={rental.returnUrl}>
                                                <input type="hidden" name="_token" value={csrfToken} />
                                                <button type="submit" className="site-account__link">Return</button>
                                            </form>
                                        )}
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

function Profile({ profile, csrfToken }) {
    const fieldProps = { values: profile.values, errors: profile.errors, idPrefix: 'profile' };

    return (
        <>
            <form method="post" action={profile.action} className="site-cart__panel site-account__panel" noValidate>
                <input type="hidden" name="_token" value={csrfToken} />
                <input type="hidden" name="_method" value="PUT" />
                <h2>Profile</h2>
                <div className="site-account__fields">
                    <Field name="email" label="Email" type="email" autoComplete="email" {...fieldProps} />
                </div>
                <div>
                    <button type="submit" className="site-cart__btn site-account__save">Save changes</button>
                </div>
            </form>

            <form method="post" action={profile.passwordAction} className="site-cart__panel site-account__panel" noValidate>
                <input type="hidden" name="_token" value={csrfToken} />
                <input type="hidden" name="_method" value="PUT" />
                <h2>Change password</h2>
                <div className="site-account__fields">
                    <Field name="current_password" label="Current password" type="password" autoComplete="current-password" {...fieldProps} />
                    <Field
                        name="password"
                        label="New password"
                        type="password"
                        autoComplete="new-password"
                        hint="At least 8 characters, with a number."
                        {...fieldProps}
                    />
                    <Field name="password_confirmation" label="Confirm new password" type="password" autoComplete="new-password" {...fieldProps} />
                </div>
                <div>
                    <button type="submit" className="site-account__secondary">Change password</button>
                </div>
            </form>
        </>
    );
}

export default function Account({
    firstName,
    tab: initialTab = 'orders',
    tabUrl,
    csrfToken,
    logoutUrl,
    catalogueUrl,
    status = null,
    orders = [],
    rentals = [],
    profile,
}) {
    const [tab, setTab] = useState(initialTab);
    const urlFor = (id) => `${tabUrl}?tab=${id}`;

    // Switch tabs without a reload, but keep the URL in step so refreshes and form redirects land on the same tab.
    const pick = (event, id) => {
        event.preventDefault();
        setTab(id);
        window.history.replaceState(null, '', urlFor(id));
    };

    return (
        <div className="site-cart">
            <div className="site-account__intro">
                <h1 className="site-cart__heading">Hello, {firstName}</h1>
                <p className="site-cart__muted">Manage your orders, rented books and profile.</p>
            </div>

            {status && <p className="site-cart__notice" role="status">{status}</p>}

            <div className="site-cart__layout">
                <nav aria-label="Account" className="site-cart__panel site-account__nav">
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
                    <form method="post" action={logoutUrl}>
                        <input type="hidden" name="_token" value={csrfToken} />
                        <button type="submit">Sign out</button>
                    </form>
                </nav>

                <section className="site-account__content">
                    {tab === 'orders' && <Orders orders={orders} catalogueUrl={catalogueUrl} />}
                    {tab === 'rentals' && <Rentals rentals={rentals} csrfToken={csrfToken} catalogueUrl={catalogueUrl} />}
                    {tab === 'profile' && <Profile profile={profile} csrfToken={csrfToken} />}
                </section>
            </div>
        </div>
    );
}
