import React from 'react';
import AuthShell from './AuthShell';
import Field from './Field';

export default function Register({ homeUrl, action, csrfToken, loginUrl, values = {}, errors = {} }) {
    const fieldProps = { values, errors, idPrefix: 'register' };

    return (
        <AuthShell homeUrl={homeUrl}>
            <form method="post" action={action} className="site-auth__card site-auth__card--wide" noValidate>
                <input type="hidden" name="_token" value={csrfToken} />

                <h1>Create an account</h1>

                <Field name="name" label="Name" autoComplete="name" {...fieldProps} />
                <Field name="email" label="Email" type="email" autoComplete="email" {...fieldProps} />
                <Field
                    name="password"
                    label="Password"
                    type="password"
                    autoComplete="new-password"
                    hint="At least 8 characters, with a number."
                    {...fieldProps}
                />
                <Field name="password_confirmation" label="Confirm password" type="password" autoComplete="new-password" {...fieldProps} />

                <div>
                    <label className="site-auth__check">
                        <input
                            type="checkbox"
                            name="terms"
                            value="1"
                            defaultChecked={!!values.terms}
                            aria-invalid={errors.terms ? true : undefined}
                            aria-describedby={errors.terms ? 'register-terms-error' : undefined}
                        />
                        <span>I agree to the <a href="#">terms</a> and <a href="#">privacy policy</a></span>
                    </label>
                    {errors.terms && <span id="register-terms-error" className="site-field__error">{errors.terms}</span>}
                </div>

                <button type="submit" className="site-auth__submit">Create account</button>
                <p className="site-auth__switch">
                    Already have an account? <a href={loginUrl}>Sign in</a>
                </p>
            </form>
        </AuthShell>
    );
}
