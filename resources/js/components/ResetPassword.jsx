import React from 'react';
import AuthShell from './AuthShell';
import Field from './Field';

export default function ResetPassword({ homeUrl, action, csrfToken, token, loginUrl, values = {}, errors = {} }) {
    const fieldProps = { values, errors, idPrefix: 'reset' };

    return (
        <AuthShell homeUrl={homeUrl}>
            <form method="post" action={action} className="site-auth__card" noValidate>
                <input type="hidden" name="_token" value={csrfToken} />
                <input type="hidden" name="token" value={token} />

                <h1>Choose a new password</h1>

                <Field name="email" label="Email" type="email" autoComplete="email" {...fieldProps} />
                <Field name="password" label="New password" type="password" autoComplete="new-password" {...fieldProps} />
                <Field name="password_confirmation" label="Confirm new password" type="password" autoComplete="new-password" {...fieldProps} />

                <button type="submit" className="site-auth__submit">Reset password</button>
                <p className="site-auth__switch">
                    <a href={loginUrl}>Back to sign in</a>
                </p>
            </form>
        </AuthShell>
    );
}
