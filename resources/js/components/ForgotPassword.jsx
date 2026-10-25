import React from 'react';
import AuthShell from './AuthShell';
import Field from './Field';

export default function ForgotPassword({ homeUrl, action, csrfToken, loginUrl, values = {}, errors = {}, status = null }) {
    const fieldProps = { values, errors, idPrefix: 'forgot' };

    return (
        <AuthShell homeUrl={homeUrl}>
            <form method="post" action={action} className="site-auth__card" noValidate>
                <input type="hidden" name="_token" value={csrfToken} />

                <h1>Reset password</h1>
                <p className="site-auth__intro">Enter your account's email and we'll send you a link to choose a new password.</p>

                {status && <p className="site-auth__status" role="status">{status}</p>}

                <Field name="email" label="Email" type="email" autoComplete="email" {...fieldProps} />

                <button type="submit" className="site-auth__submit">Send reset link</button>
                <p className="site-auth__switch">
                    Remembered it? <a href={loginUrl}>Sign in</a>
                </p>
            </form>
        </AuthShell>
    );
}
