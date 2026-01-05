window.formatAbbreviatedNumber = function (num) {
    if (num === null || num === undefined) return 0;

    const abs = Math.abs(num);
    const truncate = value => Math.floor(value * 100) / 100;

    if (abs >= 1_000_000_000_000) return truncate(num / 1_000_000_000_000) + 'T';
    if (abs >= 1_000_000_000)     return truncate(num / 1_000_000_000) + 'B';
    if (abs >= 1_000_000)         return truncate(num / 1_000_000) + 'M';
    if (abs >= 1_000)             return truncate(num / 1_000) + 'K';

    return truncate(num).toString();
};