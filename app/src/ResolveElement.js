export default class ResolveElement
{
    resolve(url, items) {
        let item = items.find((item) => {
            if (
                typeof item.hostname !== 'undefined'
                && (
                    item.hostname === window.location.hostname
                    || 'www.' + item.hostname === window.location.hostname
                )
            ) {
                return true;
            }

            return item.url !== '' && url.startsWith(item.url);
        });

        if (typeof item === 'undefined') {
            item = items.find((item) => {
                return typeof item.def !== 'undefined' && item.def === true
            });
        }

        return item.element;
    }
}