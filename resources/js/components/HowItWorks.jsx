import React from 'react';

const ICONS = {
    search: (
        <>
            <circle cx="11" cy="11" r="7" />
            <path d="M21 21l-4.3-4.3" />
        </>
    ),
    book: (
        <>
            <path d="M2 5h7a3 3 0 0 1 3 3v12a2 2 0 0 0-2-2H2z" />
            <path d="M22 5h-7a3 3 0 0 0-3 3v12a2 2 0 0 1 2-2h8z" />
        </>
    ),
    calendar: (
        <>
            <rect x="3" y="4" width="18" height="17" rx="2" />
            <path d="M3 9h18" />
            <path d="M8 2v4" />
            <path d="M16 2v4" />
            <path d="M9 15l2 2 4-4" />
        </>
    ),
};

// The steps of renting a book; the "How it works" button in the banner links here.
export default function HowItWorks({ id = 'how', title, intro, steps = [] }) {
    return (
        <section id={id} className="site-how">
            <div className="site-how__inner">
                <div className="site-how__head">
                    <div className="site-how__heading">
                        <h2 className="site-how__title">{title}</h2>
                        <span className="site-how__rule" aria-hidden="true" />
                    </div>
                    {intro && <p className="site-how__intro">{intro}</p>}
                </div>

                <div className="site-how__steps">
                    {steps.map((step, index) => (
                        <div key={step.title} className="site-how__step">
                            <div className={`site-how__icon site-how__icon--${index + 1}`}>
                                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                     strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                                    {ICONS[step.icon] || ICONS.book}
                                </svg>
                            </div>
                            <h3 className="site-how__step-title">{step.title}</h3>
                            <p className="site-how__step-text">{step.text}</p>
                        </div>
                    ))}
                </div>
            </div>
        </section>
    );
}
