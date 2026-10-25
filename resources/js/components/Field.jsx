import React from 'react';

// A labelled input that shows its Laravel validation error; values and errors are keyed by field name.
export default function Field({
    name,
    label,
    values = {},
    errors = {},
    idPrefix = 'field',
    type = 'text',
    autoComplete,
    wide = false,
    labelAside = null,
    hint = null,
    onChange,
    inputMode,
    placeholder,
    maxLength,
    children,
}) {
    const id = `${idPrefix}-${name}`;
    const error = errors[name];
    const describedBy = [hint && `${id}-hint`, error && `${id}-error`].filter(Boolean).join(' ');

    return (
        <div className={'site-field' + (wide ? ' is-wide' : '')}>
            <div className="site-field__label-row">
                <label htmlFor={id}>{label}</label>
                {labelAside}
            </div>
            <input
                id={id}
                name={name}
                type={type}
                autoComplete={autoComplete}
                defaultValue={type === 'password' ? undefined : values[name] || ''}
                onChange={onChange}
                inputMode={inputMode}
                placeholder={placeholder}
                maxLength={maxLength}
                aria-invalid={error ? true : undefined}
                aria-describedby={describedBy || undefined}
            />
            {children}
            {hint && <span id={`${id}-hint`} className="site-field__hint">{hint}</span>}
            {error && <span id={`${id}-error`} className="site-field__error">{error}</span>}
        </div>
    );
}
