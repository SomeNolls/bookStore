import React from 'react';

export default function Genres({ title = 'Browse by genre', genres = [], currentGenre = null }) {
    return (
        <section className="site-section site-genres">
            <h2 className="site-section__title">{title}</h2>
            <div className="site-genres__list">
                {genres.map((genre) => (
                    <a
                        key={genre.name}
                        href={genre.url}
                        className={'site-genres__chip' + (genre.name === currentGenre ? ' is-active' : '')}
                    >
                        {genre.name}
                    </a>
                ))}
            </div>
        </section>
    );
}
