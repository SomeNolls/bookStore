// Instant checkout validation for Bulgarian addresses, matching the rules in CheckoutController.
// Each rule returns an error message, or null when the value is fine.

const NAME = /^\p{L}[\p{L}\s.'-]*$/u;
const POSTCODE = /^[1-9]\d{3}$/;
const EMAIL = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
const EXPIRY = /^(0[1-9]|1[0-2])\/(\d{2})$/;

const digits = (value) => value.replace(/\D/g, '');

const name = (missing) => (value) => {
    if (!value.trim()) return missing;
    return NAME.test(value.trim()) ? null : 'Use letters only.';
};

function expiry(value) {
    const match = EXPIRY.exec(value.trim());
    if (!value.trim()) return 'Enter the expiry date.';
    if (!match) return 'Use MM/YY, e.g. 08/28.';

    // A card works until the end of its expiry month.
    const now = new Date();
    const expires = new Date(2000 + Number(match[2]), Number(match[1]), 1);
    return expires > now ? null : 'This card has expired.';
}

const RULES = {
    first_name: name('Enter your first name.'),
    last_name: name('Enter your last name.'),
    email: (value) => {
        if (!value.trim()) return 'Enter your email.';
        return EMAIL.test(value.trim()) ? null : 'Enter a valid email, e.g. ana@example.com.';
    },
    phone: (value) => {
        if (!value.trim()) return 'Enter your phone number.';
        return digits(value).length === 10 ? null : 'The phone number has 10 digits, e.g. 0888 123 456.';
    },
    address: (value) => {
        if (!value.trim()) return 'Enter the address to deliver to.';
        return value.trim().length >= 3 ? null : 'Enter the full address.';
    },
    city: name('Enter the city.'),
    postcode: (value) => {
        if (!value.trim()) return 'Enter the postcode.';
        return POSTCODE.test(value.trim()) ? null : 'Bulgarian postcodes have 4 digits, e.g. 1000.';
    },
    card_name: name('Enter the name on the card.'),
    card_number: (value) => {
        if (!digits(value)) return 'Enter the card number.';
        return digits(value).length === 16 ? null : 'The card number has 16 digits.';
    },
    card_expiry: expiry,
    card_cvc: (value) => {
        if (!value.trim()) return 'Enter the CVC.';
        return /^\d{3,4}$/.test(value.trim()) ? null : 'The CVC is the 3 or 4 digits on the back of the card.';
    },
    terms: (checked) => (checked ? null : 'Please agree to the terms and conditions.'),
};

const ADDRESS_FIELDS = ['address', 'city', 'postcode'];

/**
 * The fields that need checking: pick-up orders have no address.
 */
export function fieldsFor(deliveryMethod) {
    return Object.keys(RULES).filter((field) => deliveryMethod !== 'pickup' || !ADDRESS_FIELDS.includes(field));
}

export function validateField(field, value) {
    return RULES[field] ? RULES[field](value) : null;
}

// Drops anything that isn't a letter (Cyrillic or Latin), a space, a dot, an apostrophe or a hyphen.
export const lettersOnly = (value) => value.replace(/[^\p{L}\s.'-]/gu, '');

// "0888123456" → "0888 123 456"; digits only, at most 10.
export function formatPhone(value) {
    const numbers = digits(value).slice(0, 10);
    return [numbers.slice(0, 4), numbers.slice(4, 7), numbers.slice(7)].filter(Boolean).join(' ');
}

// Postcodes are 4 digits.
export const formatPostcode = (value) => digits(value).slice(0, 4);

// "7111111111111111" → "7111 1111 1111 1111"
export const formatCardNumber = (value) => digits(value).slice(0, 16).replace(/(\d{4})(?=\d)/g, '$1 ');

// "0828" → "08/28"
export function formatExpiry(value) {
    const numbers = digits(value).slice(0, 4);
    return numbers.length > 2 ? `${numbers.slice(0, 2)}/${numbers.slice(2)}` : numbers;
}
