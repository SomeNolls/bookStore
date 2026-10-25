import React from 'react';

const ICONS = {
    book: (
        <>
            <path d="M2 5h7a3 3 0 0 1 3 3v12a2 2 0 0 0-2-2H2z" />
            <path d="M22 5h-7a3 3 0 0 0-3 3v12a2 2 0 0 1 2-2h8z" />
        </>
    ),
    tag: (
        <>
            <path d="M20.6 13.4l-7.2 7.2a2 2 0 0 1-2.8 0L2 12V2h10l8.6 8.6a2 2 0 0 1 0 2.8z" />
            <circle cx="7" cy="7" r="1.5" />
        </>
    ),
    truck: (
        <>
            <path d="M1 4h13v11H1z" />
            <path d="M14 8h4l4 4v3h-8z" />
            <circle cx="5.5" cy="18" r="2" />
            <circle cx="17.5" cy="18" r="2" />
        </>
    ),
    calendar: (
        <>
            <rect x="3" y="4" width="18" height="17" rx="2" />
            <path d="M3 9h18" />
            <path d="M8 2v4" />
            <path d="M16 2v4" />
            <path d="M12 12v3l2 1.5" />
        </>
    ),
};

// A full-width strip of short reasons to shop here, each with an icon.
export default function Features({ label = 'Why shop with us', items = [] }) {
    return (
        <section aria-label={label} className="site-features">
            <div className="site-features__inner">
                {items.map((item) => (
                    <div key={item.title} className="site-features__item">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             strokeWidth="1.4" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                            {ICONS[item.icon] || ICONS.book}
                        </svg>
                        <div className="site-features__copy">
                            <strong className="site-features__title">{item.title}</strong>
                            <span className="site-features__text">{item.text}</span>
                        </div>
                    </div>
                ))}
            </div>
        </section>
    );
}
