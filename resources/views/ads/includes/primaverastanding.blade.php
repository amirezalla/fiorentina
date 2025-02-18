@php
    use GuzzleHttp\Client;
    use Symfony\Component\DomCrawler\Crawler;
    use Illuminate\Http\Request;
@endphp

@php
    use GuzzleHttp\Client;
    use Symfony\Component\DomCrawler\Crawler;

    // Create a new Guzzle client
    $client = new Client();

    // The URL you want to render
    $targetUrl = 'https://www.diretta.it/calcio/italia/primavera-1/classifiche/#/6NcAZJet/table/overall';

    // Rendertron endpoint: append the target URL (properly URL encoded)
    $renderUrl = 'https://render-tron.appspot.com/render/' . urlencode($targetUrl);

    // Make the GET request to Rendertron
    $response = $client->get($renderUrl, [
        'headers' => [
            // Optional: set a User-Agent header if needed
            'User-Agent' =>
                'Mozilla/5.0 (Windows NT 10.0; Win64; x64) ' .
                'AppleWebKit/537.36 (KHTML, like Gecko) ' .
                'Chrome/112.0.0.0 Safari/537.36',
        ],
    ]);

    // Get the fully rendered HTML
    $html = $response->getBody()->getContents();

    // Use DomCrawler to extract the element with class .tournament-table-standings
    $crawler = new Crawler($html);

    try {
        $tableRankingHtml = $crawler->filter('.tournament-table-standings')->html();
    } catch (\Exception $e) {
        $tableRankingHtml = '<p>Ranking table not found.</p>';
    }
@endphp

@dd($tableRankingHtml)
