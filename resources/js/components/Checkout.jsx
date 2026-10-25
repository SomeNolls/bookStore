import React, { useState } from 'react';
import { Cover } from './Cart';
import Field from './Field';
import { fieldsFor, formatCardNumber, formatExpiry, formatPhone, formatPostcode, lettersOnly, validateField } from './checkoutValidation';

const money = (amount) => amount.toFixed(2) + '$';
const monthsLabel = (months) => `${months} ${months === 1 ? 'month' : 'months'}`;

const DELIVERY_OPTIONS = [
    { id: 'courier', name: 'Courier to address', note: '1–2 working days' },
    { id: 'office', name: 'Courier office', note: 'Collect from a courier office' },
    { id: 'pickup', name: 'Pick up in store', note: 'Free · ready within 24 h' },
];

const valueOf = (input) => (input.type === 'checkbox' ? input.checked : input.value);

// Fields whose input is cleaned up or masked as you type.
const FORMATTERS = {
    first_name: lettersOnly,
    last_name: lettersOnly,
    city: lettersOnly,
    card_name: lettersOnly,
    phone: formatPhone,
    postcode: formatPostcode,
    card_number: formatCardNumber,
    card_expiry: formatExpiry,
};

// Rewrites the input's value, keeping the cursor after the same character it was after.
function applyFormat(input, format) {
    const before = input.value;
    const after = format(before);
    if (after === before) return;

    const caret = format(before.slice(0, input.selectionStart ?? before.length)).length;
    input.value = after;
    input.setSelectionRange(caret, caret);
}

export default function Checkout({ items = [], rentals = [], subtotal = 0, delivery = 0, action, csrfToken, cartUrl, values = {}, errors = {}, store }) {
    const [method, setMethod] = useState(values.delivery_method || 'courier');
    // Fields are checked once you leave them, then again on every change.
    const [touched, setTouched] = useState({});
    const [clientErrors, setClientErrors] = useState({});
    const deliveryFee = method === 'pickup' ? 0 : delivery;

    // A field's own check replaces the server's error once it has been touched.
    const shownErrors = { ...errors };
    Object.keys(touched).forEach((field) => {
        if (clientErrors[field]) {
            shownErrors[field] = clientErrors[field];
        } else {
            delete shownErrors[field];
        }
    });
    const fieldProps = { values, errors: shownErrors, idPrefix: 'checkout' };

    function check(input, markTouched) {
        const field = input.name;
        if (!fieldsFor(method).includes(field) || (!markTouched && !touched[field])) {
            return;
        }

        setTouched((previous) => ({ ...previous, [field]: true }));
        setClientErrors((previous) => ({ ...previous, [field]: validateField(field, valueOf(input)) }));
    }

    function handleChange(event) {
        const input = event.target;
        if (FORMATTERS[input.name]) applyFormat(input, FORMATTERS[input.name]);

        check(input, input.type === 'checkbox');
    }

    // Checks every field before sending, and stays on the page at the first problem.
    function handleSubmit(event) {
        const form = event.currentTarget;
        const found = {};
        fieldsFor(method).forEach((field) => {
            if (form.elements[field]) {
                found[field] = validateField(field, valueOf(form.elements[field]));
            }
        });

        setTouched(Object.fromEntries(Object.keys(found).map((field) => [field, true])));
        setClientErrors(found);

        const first = Object.keys(found).find((field) => found[field]);
        if (first) {
            event.preventDefault();
            form.elements[first].focus();
        }
    }

    return (
        <div className="site-cart">
            <div className="site-checkout__top">
                <ol aria-label="Checkout steps" className="site-checkout__steps">
                    <li><a href={cartUrl}>1 Cart</a></li>
                    <li aria-hidden="true">—</li>
                    <li aria-current="step">2 Details &amp; delivery</li>
                    <li aria-hidden="true">—</li>
                    <li className="is-upcoming">3 Confirmation</li>
                </ol>
                <a href={cartUrl} className="site-checkout__back">← Back to cart</a>
            </div>

            <h1 className="site-cart__heading">Checkout</h1>

            {errors.cart && <p className="site-cart__notice is-error" role="alert">{errors.cart}</p>}

            <div className="site-cart__layout">
                <form
                    id="checkout-form"
                    method="post"
                    action={action}
                    className="site-checkout__form"
                    noValidate
                    onChange={handleChange}
                    onBlur={(event) => check(event.target, true)}
                    onSubmit={handleSubmit}
                >
                    <input type="hidden" name="_token" value={csrfToken} />

                    <section className="site-cart__panel site-checkout__section">
                        <h2>Contact details</h2>
                        <div className="site-checkout__grid">
                            <Field name="first_name" label="First name" autoComplete="given-name" {...fieldProps} />
                            <Field name="last_name" label="Last name" autoComplete="family-name" {...fieldProps} />
                            <Field name="email" label="Email" type="email" autoComplete="email" {...fieldProps} />
                            <Field name="phone" label="Phone" type="tel" autoComplete="tel" inputMode="numeric" maxLength={12} placeholder="0888 123 456" {...fieldProps} />
                        </div>
                    </section>

                    <section className="site-cart__panel site-checkout__section">
                        <h2>Delivery</h2>
                        <fieldset className="site-checkout__options">
                            <legend className="site-sr-only">Delivery method</legend>
                            {DELIVERY_OPTIONS.map((option) => (
                                <label key={option.id} className={'site-checkout__option' + (option.id === method ? ' is-selected' : '')}>
                                    <input
                                        type="radio"
                                        name="delivery_method"
                                        value={option.id}
                                        checked={option.id === method}
                                        onChange={() => setMethod(option.id)}
                                    />
                                    <strong>{option.name}</strong>
                                    <span className="site-cart__muted">{option.note}</span>
                                </label>
                            ))}
                        </fieldset>

                        {method === 'pickup' ? (
                            <div className="site-checkout__pickup">
                                <strong>Pick up from:</strong> {store.address} · {store.hours}
                                <br />
                                We'll email you when your order is ready (usually within 24 hours).
                            </div>
                        ) : (
                            <div className="site-checkout__grid">
                                <Field
                                    name="address"
                                    label={method === 'office' ? 'Courier office address' : 'Street address'}
                                    autoComplete={method === 'office' ? undefined : 'street-address'}
                                    wide
                                    {...fieldProps}
                                />
                                <Field name="city" label="City" autoComplete="address-level2" {...fieldProps} />
                                <Field name="postcode" label="Postcode" autoComplete="postal-code" inputMode="numeric" maxLength={4} placeholder="1000" {...fieldProps} />
                            </div>
                        )}
                    </section>

                    <section className="site-cart__panel site-checkout__section">
                        <h2>Payment</h2>
                        <p className="site-cart__muted site-cart__small">
                            Pay by card now. Test card: 7111 1111 1111 1111 with any CVC.
                        </p>
                        <div className="site-checkout__grid">
                            <Field name="card_name" label="Name on card" autoComplete="cc-name" wide {...fieldProps} />
                            <Field
                                name="card_number"
                                label="Card number"
                                autoComplete="cc-number"
                                inputMode="numeric"
                                maxLength={19}
                                placeholder="1234 5678 9012 3456"
                                wide
                                {...fieldProps}
                            />
                            <Field name="card_expiry" label="Expiry date" autoComplete="cc-exp" inputMode="numeric" maxLength={5} placeholder="MM/YY" {...fieldProps} />
                            <Field name="card_cvc" label="CVC" autoComplete="cc-csc" inputMode="numeric" maxLength={4} placeholder="123" {...fieldProps} />
                        </div>

                        <div className="site-field">
                            <label htmlFor="checkout-notes">Order notes (optional)</label>
                            <textarea id="checkout-notes" name="notes" rows="3" defaultValue={values.notes || ''} />
                        </div>

                        <div>
                            <label className="site-checkout__terms">
                                <input
                                    type="checkbox"
                                    name="terms"
                                    value="1"
                                    defaultChecked={!!values.terms}
                                    aria-invalid={shownErrors.terms ? true : undefined}
                                    aria-describedby={shownErrors.terms ? 'checkout-terms-error' : undefined}
                                />
                                <span>I agree to the <a href="#">terms and conditions</a></span>
                            </label>
                            {shownErrors.terms && <span id="checkout-terms-error" className="site-field__error">{shownErrors.terms}</span>}
                        </div>
                    </section>
                </form>

                <aside aria-label="Order summary" className="site-cart__panel site-cart__summary">
                    <h2>Your order</h2>
                    {items.map((item) => (
                        <div key={item.url} className="site-checkout__line">
                            <Cover book={item} className="site-cart__cover is-small" />
                            <div className="site-checkout__line-info">
                                <strong>{item.title}</strong>
                                <span className="site-cart__muted site-cart__small">Qty {item.quantity}</span>
                            </div>
                            <strong>{money(item.price * item.quantity)}</strong>
                        </div>
                    ))}
                    {rentals.map((item) => (
                        <div key={`rent-${item.url}`} className="site-checkout__line">
                            <Cover book={item} className="site-cart__cover is-small" />
                            <div className="site-checkout__line-info">
                                <strong>{item.title}</strong>
                                <span className="site-cart__muted site-cart__small">Rent for {monthsLabel(item.months)} · {money(item.rentPrice)} / month</span>
                            </div>
                            <strong>{money(item.rentPrice * item.months)}</strong>
                        </div>
                    ))}
                    <div className="site-checkout__totals">
                        <div className="site-cart__row">
                            <span className="site-cart__muted">Subtotal</span>
                            <strong>{money(subtotal)}</strong>
                        </div>
                        <div className="site-cart__row">
                            <span className="site-cart__muted">Delivery</span>
                            <strong>{deliveryFee === 0 ? 'Free' : money(deliveryFee)}</strong>
                        </div>
                    </div>
                    <div className="site-cart__row site-cart__total">
                        <span>Total</span>
                        <span>{money(subtotal + deliveryFee)}</span>
                    </div>
                    <button type="submit" form="checkout-form" className="site-cart__btn">Place order</button>
                </aside>
            </div>
        </div>
    );
}
