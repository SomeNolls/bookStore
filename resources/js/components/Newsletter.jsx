import React from 'react';

export default function Newsletter({ title, text, action = '#', csrfToken }) {
    return (
        <section className="site-newsletter">
            <div className="site-newsletter__inner">
                <div className="site-newsletter__copy">
                    <h2 className="site-section__title">{title}</h2>
                    {text && <p className="site-newsletter__text">{text}</p>}
                </div>
                <form className="site-newsletter__form" action={action} method="POST">
                    <input type="hidden" name="_token" value={csrfToken} />
                    <label htmlFor="newsletter-email" className="site-sr-only">Email address</label>
                    <input id="newsletter-email" type="email" name="email" placeholder="you@example.com" required />
                    <button type="submit">Subscribe</button>
                </form>
            </div>
        </section>
    );
}
