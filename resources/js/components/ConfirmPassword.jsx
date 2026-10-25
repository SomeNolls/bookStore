import React from 'react';
import AuthShell from './AuthShell';
import Field from './Field';

export default function ConfirmPassword({ homeUrl, action, csrfToken, forgotUrl = null, errors = {} }) {
    const fieldProps = { errors, idPrefix: 'confirm' };

    return (
        <AuthShell homeUrl={homeUrl}>
            <form method="post" action={action} className="site-auth__card" noValidate>
                <input type="hidden" name="_token" value={csrfToken} />

                <h1>Confirm password</h1>
                <p className="site-auth__intro">Please confirm your password before continuing.</p>

                <Field
                    name="password"
                    label="Password"
                    type="password"
                    autoComplete="current-password"
                    labelAside={forgotUrl && <a href={forgotUrl}>Forgot password?</a>}
                    {...fieldProps}
                />

                <button type="submit" className="site-auth__submit">Confirm password</button>
            </form>
        </AuthShell>
    );
}
