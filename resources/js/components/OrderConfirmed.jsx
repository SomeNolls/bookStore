import React from 'react';

const money = (amount) => amount.toFixed(2) + '$';
const monthsLabel = (months) => `${months} ${months === 1 ? 'month' : 'months'}`;

export default function OrderConfirmed({ order, catalogueUrl }) {
    return (
        <div className="site-cart">
            <ol aria-label="Checkout steps" className="site-checkout__steps">
                <li>1 Cart</li>
                <li aria-hidden="true">—</li>
                <li>2 Details &amp; delivery</li>
                <li aria-hidden="true">—</li>
                <li aria-current="step">3 Confirmation</li>
            </ol>

            <div className="site-cart__panel site-confirmed">
                <h1 className="site-cart__heading">Thank you for your order</h1>
                <p>
                    Order <strong>#{order.number}</strong> is placed. We'll write to <strong>{order.email}</strong>
                    {order.deliveryMethod === 'pickup'
                        ? ' when it is ready to pick up.'
                        : ' when it ships.'}
                </p>

                {order.items.some((item) => item.months) && (
                    <p className="site-cart__muted">Your rental period starts on the day the order is completed; you'll see the return date under My account.</p>
                )}

                <div className="site-confirmed__lines">
                    {order.items.map((item, index) => (
                        <div key={index} className="site-cart__row">
                            <span>{item.title}{item.months ? ` · rent for ${monthsLabel(item.months)}` : ` × ${item.quantity}`}</span>
                            <strong>{money(item.price * (item.months || item.quantity))}</strong>
                        </div>
                    ))}
                    <div className="site-cart__row">
                        <span className="site-cart__muted">Delivery</span>
                        <strong>{order.delivery === 0 ? 'Free' : money(order.delivery)}</strong>
                    </div>
                    <div className="site-cart__row site-cart__total">
                        <span>Total</span>
                        <span>{money(order.total)}</span>
                    </div>
                </div>

                <a href={catalogueUrl} className="site-cart__btn">Keep browsing</a>
            </div>
        </div>
    );
}
