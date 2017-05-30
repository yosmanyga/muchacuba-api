export default class ResolveElement
{
    resolve(url, items) {
        let item = items.find((item) => {
            const type = typeof item.hostname;
            if (type !== 'undefined') {
                if (type === 'string') {
                    item.hostname = [item.hostname];
                }

                return item.hostname.filter((hostname) => {
                    return (
                        hostname === window.location.hostname
                        || 'www.' + hostname === window.location.hostname
                    );
                })
            }

            return (
                typeof item.url !== 'undefined'
                && url.startsWith(item.url)
            );
        });

        if (typeof item === 'undefined') {
            item = items.find((item) => {
                return (
                    typeof item.def !== 'undefined'
                    && item.def === true
                );
            });
        }

        return item.element;
    }
}