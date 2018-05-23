<?php

namespace Yosmy\Navigation;

use Goutte\Client;

/**
 * @author Yosmany Garcia <yosmanyga@gmail.com>
 *
 * @di\service({
 *     private: true
 * })
 */
class ResolveProxies
{
    /**
     * @param int $amount
     *
     * @return array
     *
     * @cli\resolution({command: "yosmy.navigation.resolve-proxies"})
     */
    public function resolve($amount = 100)
    {
        $agent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/40.0.2214.111 Safari/537.36';

        $client = new Client(['HTTP_USER_AGENT' => $agent]);
        $client->setHeader('User-Agent', $agent);
        $client->followRedirects(true);

        return array_merge(
            $this->resolveFromTekbreak($client, $amount),
            $this->resolveFromSourceProxyhttp($client, $amount),
            $this->resolveFromHidemyname($client, $amount),
            $this->resolveFromFreeproxylist($client, $amount)
        );
    }

//    /**
//     * @param Client $client
//     * @param int    $amount
//     *
//     * @return array
//     */
//    private function resolveFromSpinproxies(Client $client, $amount)
//    {
//        $data = json_decode(
//            file_get_contents(sprintf(
//                'https://spinproxies.com/api/v1/proxylist?%s&%s&%s&%s&%s&%s',
//                'key=kzu9kk633b0veas1msrkymh1z355fg',
//                'format=json',
//                'type=elite,anonymous',
//                'protocols=ALL',
//                //'country_code=ALL', empty if set this parameter
//                'new=false',
//                'limit=100'
//            )),
//            true
//        );
//
//        $proxies = [];
//        foreach ($data['data']['proxies'] as $i => $proxy) {
//            $proxies[$i] = [
//                'ip' => $proxy['ip'],
//                'port' => $proxy['port'],
//                'protocol' => $proxy['protocol'],
//            ];
//        }
//
//        return $proxies;
//    }

    /**
     * @param Client $client
     * @param int    $amount
     *
     * @return array
     */
    private function resolveFromTekbreak(Client $client, $amount)
    {
        $proxies = json_decode(
            file_get_contents('http://proxy.tekbreak.com/100/json'),
            true
        );

        foreach ($proxies as $i => $proxy) {
            $proxies[$i] = [
                'ip' => $proxy['ip'],
                'port' => $proxy['port'],
                'protocol' => $proxy['type'],
            ];
        }

        return $proxies;
    }

    /**
     * @param Client $client
     * @param int    $amount
     *
     * @return array
     */
    private function resolveFromSourceProxyhttp(Client $client, $amount)
    {
        $crawler = $client->request('GET', 'https://proxyhttp.net/free-list/proxy-anonymous-hide-ip-address/');
        $trCrawler = $crawler->filter('.proxytbl tbody tr');

        $proxies = [];
        $i = 1; // Ignore row 0, because it contains the headers
        while($i < $amount) {
            $tdCrawler = $trCrawler->eq($i);

            if ($tdCrawler->count() === 0) {
                break;
            }

            $ip = $tdCrawler->filter('td')->eq(0)->text();
            $port = $tdCrawler->filter('td')->eq(1)->text();
            $protocol = 'http';

            $proxies[] = [
                'ip' => $ip,
                'port' => $port,
                'protocol' => $protocol
            ];

            $i++;
        }

        return $proxies;
    }

    /**
     * @param Client $client
     * @param int    $amount
     *
     * @return array
     */
    private function resolveFromHidemyname(Client $client, $amount)
    {
        $crawler = $client->request('GET', 'https://hidemy.name/en/proxy-list/?type=h');
        $trCrawler = $crawler->filter('.proxy__t tbody tr');

        $proxies = [];
        $i = 0;
        while($i < $amount) {
            $tdCrawler = $trCrawler->eq($i);

            if ($tdCrawler->count() === 0) {
                break;
            }

            $ip = $tdCrawler->filter('td')->eq(0)->text();
            $port = $tdCrawler->filter('td')->eq(1)->text();
            $protocol = $tdCrawler->filter('td')->eq(4)->text();

            $proxies[] = [
                'ip' => $ip,
                'port' => $port,
                'protocol' => $protocol
            ];

            $i++;
        }

        return $proxies;
    }

    /**
     * @param Client $client
     * @param int    $amount
     *
     * @return array
     */
    private function resolveFromFreeproxylist(Client $client, $amount)
    {
        $crawler = $client->request('GET', 'https://free-proxy-list.net/');
        $trCrawler = $crawler->filter('table#proxylisttable tbody tr');

        $proxies = [];
        $i = 0;
        while($i < $amount) {
            $tdCrawler = $trCrawler->eq($i);

            if ($tdCrawler->count() === 0) {
                break;
            }

            $ip = $tdCrawler->filter('td')->eq(0)->text();
            $port = $tdCrawler->filter('td')->eq(1)->text();
            $protocol = $tdCrawler->filter('td')->eq(6)->text() === 'yes' ? 'https' : 'http';

            $proxies[] = [
                'ip' => $ip,
                'port' => $port,
                'protocol' => $protocol
            ];

            $i++;
        }

        return $proxies;
    }
}
