export function useFormat () {
    const formatMoney = (amount, currency = '₽') => {
        const formattedAmount = amount.toLocaleString('ru-RU', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        });
        return `${formattedAmount} ${currency}`;
    };

    const formatDateTime = (isoString) => {
        if (!isoString) {
            return '';
        }
        const date    = new Date(isoString);
        const day     = String(date.getDate()).padStart(2, '0');
        const month   = String(date.getMonth() + 1).padStart(2, '0');
        const year    = date.getFullYear();
        const hours   = String(date.getHours()).padStart(2, '0');
        const minutes = String(date.getMinutes()).padStart(2, '0');
        return `${day}.${month}.${year} ${hours}:${minutes}`;
    };

    const formatDate = (isoString) => {
        if (!isoString) {
            return '';
        }
        const date  = new Date(isoString);
        const day   = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year  = date.getFullYear();
        return `${day}.${month}.${year}`;
    };

    return {
        formatMoney,
        formatDateTime,
        formatDate,
    };
}