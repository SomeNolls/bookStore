import React from 'react';

const money = (amount) => amount.toFixed(2) + '$';
const monthsLabel = (months) => `${months} ${months === 1 ? 'month' : 'months'}`;

export function Cover({ book, className }) {
    return (
        <div className={className} aria-hidden="true">
            {book.img ? <img src={book.img} alt="" /> : <span>{book.title}</span>}
        </div>
    );
}

// Forms can only send GET/POST, so PATCH and DELETE go through Laravel's _method field.
function MethodForm({ action, method, csrfToken, className, children }) {
    return (
        <form method="post" action={action} className={className}>
            <input type="hidden" name="_token" value={csrfToken} />
            <input type="hidden" name="_method" value={method} />
            {children}
        </form>
    );
}

function CartItem({ item, csrfToken }) {
    return (
        <div className="site-cart__item">
            <Cover book={item} className="site-cart__cover" />

            <div className="site-cart__item-info">
                <a href={item.url} className="site-cart__item-title">{item.title}</a>
                {item.author && <span className="site-cart__muted">{item.author}</span>}
                <span className="site-cart__muted">{money(item.price)} each</span>
                <MethodForm action={item.removeUrl} method="DELETE" csrfToken={csrfToken}>
                    <button type="submit" className="site-cart__remove">Remove</button>
                </MethodForm>
            </div>

            <MethodForm action={item.updateUrl} method="PATCH" csrfToken={csrfToken} className="site-cart__qty">
                <button
                    type="submit"
                    name="quantity"
                    value={item.quantity - 1}
                    aria-label={`Decrease quantity of ${item.title}`}
                    disabled={item.quantity <= 1}
                >
                    −
                </button>
                <span aria-label={`Quantity ${item.quantity}`}>{item.quantity}</span>
                <button
                    type="submit"
                    name="quantity"
                    value={item.quantity + 1}
                    aria-label={`Increase quantity of ${item.title}`}
                    disabled={item.quantity >= item.available}
                >
                    +
                </button>
            </MethodForm>

            <span className="site-cart__line-total">{money(item.price * item.quantity)}</span>
        </div>
    );
}

function RentalItem({ item, csrfToken }) {
    return (
        <div className="site-cart__item">
            <Cover book={item} className="site-cart__cover" />

            <div className="site-cart__item-info">
                <a href={item.url} className="site-cart__item-title">{item.title}</a>
                {item.author && <span className="site-cart__muted">{item.author}</span>}
                <span className="site-cart__muted">{money(item.rentPrice)} / month · rent for {monthsLabel(item.months)}</span>
                <span className="site-cart__muted">Starts when your order is completed</span>
                <MethodForm action={item.removeUrl} method="DELETE" csrfToken={csrfToken}>
                    <button type="submit" className="site-cart__remove">Remove</button>
                </MethodForm>
            </div>

            <span className="site-cart__line-total">{money(item.rentPrice * item.months)}</span>
        </div>
    );
}

export default function Cart({
    items = [],
    rentals = [],
    subtotal = 0,
    delivery = 0,
    freeDeliveryFrom,
    notice = null,
    csrfToken,
    catalogueUrl,
    checkoutUrl,
}) {
    const count = items.reduce((sum, item) => sum + item.quantity, 0) + rentals.length;

    return (
        <div className="site-cart">
            <h1 className="site-cart__heading">Your cart</h1>

            {notice && <p className="site-cart__notice" role="status">{notice}</p>}

            {items.length === 0 && rentals.length === 0 ? (
                <div className="site-cart__panel site-cart__empty">
                    <h2>Your cart is empty</h2>
                    <p className="site-cart__muted">Find something good to read in the catalogue.</p>
                    <a href={catalogueUrl} className="site-cart__btn">Browse books</a>
                </div>
            ) : (
                <div className="site-cart__layout">
                    <section aria-label="Cart items" className="site-cart__panel site-cart__items">
                        {items.map((item) => (
                            <CartItem key={item.id} item={item} csrfToken={csrfToken} />
                        ))}
                        {rentals.map((item) => (
                            <RentalItem key={`rent-${item.id}`} item={item} csrfToken={csrfToken} />
                        ))}
                        <div className="site-cart__continue">
                            <a href={catalogueUrl}>← Continue shopping</a>
                        </div>
                    </section>

                    <aside aria-label="Order summary" className="site-cart__panel site-cart__summary">
                        <h2>Order summary</h2>
                        <div className="site-cart__row">
                            <span className="site-cart__muted">Subtotal ({count} {count === 1 ? 'item' : 'items'})</span>
                            <strong>{money(subtotal)}</strong>
                        </div>
                        <div className="site-cart__row">
                            <span className="site-cart__muted">Delivery</span>
                            <strong>{delivery === 0 ? 'Free' : money(delivery)}</strong>
                        </div>
                        <p className="site-cart__hint">
                            {subtotal >= freeDeliveryFrom
                                ? 'You qualify for free delivery.'
                                : `Add ${money(freeDeliveryFrom - subtotal)} more for free delivery.`}
                        </p>
                        <div className="site-cart__row site-cart__total">
                            <span>Total</span>
                            <span>{money(subtotal + delivery)}</span>
                        </div>
                        <a href={checkoutUrl} className="site-cart__btn">Proceed to checkout</a>
                        <span className="site-cart__muted site-cart__small">Or reserve and pick up in store at checkout.</span>
                    </aside>
                </div>
            )}
        </div>
    );
}
