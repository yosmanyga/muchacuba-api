export default class ResolveElement
{
    resolve(url, items) {
        let item = items.find((item) => {
            return url.startsWith(item.url);
        });

        if (typeof item === 'undefined') {
            item = items.find((item) => {
                return typeof item.def !== 'undefined'
            });
        }

        return item.element;
    }
}