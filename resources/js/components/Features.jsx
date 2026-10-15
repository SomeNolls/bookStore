import React from 'react';

const ICONS = {
    search: (
        <>
            <circle cx="11" cy="11" r="7" />
            <path d="M21 21l-4.3-4.3" />
        </>
    ),
    star: <path d="M12 2l3 6.5 7 .8-5.2 4.8 1.5 7L12 17.6 5.7 21l1.5-7L2 9.3l7-.8z" />,
    bag: (
        <>
            <path d="M3 7h18v13H3z" />
            <path d="M8 7V4h8v3" />
        </>
    ),
};

export default function Features({ items = [] }) {
    return (
        <section className="site-section site-features">
            <div className="site-features__grid">
                {items.map((item) => (
                    <div key={item.title} className="site-features__card">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                            {ICONS[item.icon] || ICONS.search}
                        </svg>
                        <h3 className="site-features__title">{item.title}</h3>
                        <p className="site-features__text">{item.text}</p>
                    </div>
                ))}
            </div>
        </section>
    );
}
