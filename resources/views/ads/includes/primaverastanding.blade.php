@php
    use GuzzleHttp\Client;
    use Symfony\Component\DomCrawler\Crawler;
    use Illuminate\Http\Request;
@endphp

@php
    // Create an HTTP client instance.
    $client = new Client([
        'headers' => [
            'User-Agent' =>
                'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/112.0.0.0 Safari/537.36',
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'Accept-Language' => 'it-IT,it;q=0.9,en-US;q=0.8,en;q=0.7',
        ],
    ]);
    // Fetch the remote page.
    $response = $client->get('https://www.tuttocampo.it/Italia/Primavera/GironeA/Classifica');
    $html = $response->getBody()->getContents();

    // Use DomCrawler to parse the HTML.
    $crawler = new Crawler($html);

    // Try to extract the element with the class .table_ranking.
    try {
        $tableRankingHtml = $crawler->filter('.table_ranking')->html();
    } catch (\Exception $e) {
        $tableRankingHtml = '<p>Ranking table not found.</p>';
    }
@endphp

{!! $tableRankingHtml !!}
