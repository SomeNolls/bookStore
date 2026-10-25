import React from 'react';
import AuthShell from './AuthShell';
import Field from './Field';

export default function Login({ homeUrl, action, csrfToken, registerUrl, forgotUrl = null, values = {}, errors = {}, status = null }) {
    const fieldProps = { values, errors, idPrefix: 'login' };

    return (
        <AuthShell homeUrl={homeUrl}>
            <form method="post" action={action} className="site-auth__card" noValidate>
                <input type="hidden" name="_token" value={csrfToken} />

                <h1>Sign in</h1>

                {status && <p className="site-auth__status" role="status">{status}</p>}

                <Field name="email" label="Email" type="email" autoComplete="email" {...fieldProps} />
                <Field
                    name="password"
                    label="Password"
                    type="password"
                    autoComplete="current-password"
                    labelAside={forgotUrl && <a href={forgotUrl}>Forgot password?</a>}
                    {...fieldProps}
                />

                <label className="site-auth__check">
                    <input type="checkbox" name="remember" value="1" defaultChecked={values.remember} />
                    Remember me
                </label>

                <button type="submit" className="site-auth__submit">Sign in</button>
                <p className="site-auth__switch">
                    Don't have an account? <a href={registerUrl}>Register</a>
                </p>
            </form>
        </AuthShell>
    );
}
