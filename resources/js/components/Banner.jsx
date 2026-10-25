import React from 'react';

export default function Banner({
    title,
    text,
    image,
    imageAlt = '',
    primaryAction,
    secondaryAction,
}) {
    return (
        <section className="site-banner">
            <div className="site-banner__inner">
                <div className="site-banner__copy">
                    <h1 className="site-banner__title">{title}</h1>
                    {text && <p className="site-banner__text">{text}</p>}
                    <div className="site-banner__actions">
                        {primaryAction && (
                            <a href={primaryAction.url} className="site-banner__btn site-banner__btn--primary">
                                {primaryAction.label}
                            </a>
                        )}
                        {secondaryAction && (
                            <a href={secondaryAction.url} className="site-banner__btn site-banner__btn--outline">
                                {secondaryAction.label}
                            </a>
                        )}
                    </div>
                </div>

                {image && (
                    <div className="site-banner__media">
                        <img src={image} alt={imageAlt} className="site-banner__image" />
                    </div>
                )}
            </div>
        </section>
    );
}
