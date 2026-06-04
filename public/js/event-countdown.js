/**
 * Countdown H-Minus — mirror logika App\Support\EventCountdown (client UI).
 * eventDate: string 'YYYY-MM-DD' dari pesanans.tanggal_acara
 */
export function getDaysRemaining(eventDate, today = new Date()) {
    if (!eventDate) return null;

    const event = new Date(eventDate + 'T00:00:00');
    const now = new Date(today);
    now.setHours(0, 0, 0, 0);
    event.setHours(0, 0, 0, 0);

    const msPerDay = 86400000;
    return Math.round((event - now) / msPerDay);
}

export function getCountdownBadge(daysRemaining) {
    if (daysRemaining === null || daysRemaining === undefined) return null;

    let label;
    if (daysRemaining < 0) {
        label = `H+${Math.abs(daysRemaining)}`;
    } else {
        label = `H-${daysRemaining}`;
    }

    let tier = 'early';
    if (daysRemaining < 0) tier = 'past';
    else if (daysRemaining === 0) tier = 'event_day';
    else if (daysRemaining <= 7) tier = 'urgent';
    else if (daysRemaining <= 30) tier = 'preparation';

    const classes = {
        event_day: 'bg-lime text-bottle border border-bottleBright',
        urgent: 'bg-red-600 text-white border border-red-700',
        preparation: 'bg-orange-500 text-white border border-orange-600',
        past: 'bg-gray-200 text-gray-600 border border-gray-300',
        early: 'bg-leafSoft text-bottle border border-leaf',
    };

    return { label, tier, className: classes[tier] || classes.early };
}
