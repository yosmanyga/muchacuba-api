export default class ResolveElement
{
    resolve(url, items) {
        /* Analyze by hostname */

        let item = items.find((item) => {
            const type = typeof item.hostname;

            if (type === 'undefined') {
                return false;
            }

            if (type === 'string') {
                item.hostname = [item.hostname];
            }

            return item.hostname.find((hostname) => {
                return (
                    hostname === window.location.hostname
                    || 'www.' + hostname === window.location.hostname
                );
            })
        });

        if (typeof item !== 'undefined') {
            return item.element;
        }

        /* Analyze by url */

        item = items.find((item) => {
            return (
                typeof item.url !== 'undefined'
                && url.startsWith(item.url)
            );
        });

        if (typeof item !== 'undefined') {
            return item.element;
        }

        /* Default */

        if (typeof item === 'undefined') {
            item = items.find((item) => {
                return (
                    typeof item.def !== 'undefined'
                    && item.def === true
                );
            });
        }

        if (typeof item !== 'undefined') {
            return item.element;
        }

        throw new Error();
    }
}