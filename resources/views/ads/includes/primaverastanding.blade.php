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

    // Set the RapidAPI endpoint and payload
    $rapidApiUrl = 'https://scrapeninja.p.rapidapi.com/scrape-js';
    $payload = [
        'url' => 'https://www.diretta.it/calcio/italia/primavera-1/classifiche/#/6NcAZJet/table/overall',
        'geo' => 'us',
        'retryNum' => 1,
    ];

    // Send a POST request to ScrapeNinja with the necessary headers
    $response = $client->request('POST', $rapidApiUrl, [
        'headers' => [
            'Content-Type' => 'application/json',
            'x-rapidapi-host' => 'scrapeninja.p.rapidapi.com',
            'x-rapidapi-key' => '1e9b76550emshc710802be81e3fcp1a0226jsn069e6c35a2bb',
        ],
        'json' => $payload,
    ]);

    // Get the rendered HTML response
    $html = $response->getBody()->getContents();

    // Parse the HTML using DomCrawler
    $crawler = new Crawler($html);
    try {
        $tableRankingHtml = $crawler->filter('.tournament-table-standings')->html();
    } catch (\Exception $e) {
        $tableRankingHtml = '<p>Ranking table not found.</p>';
    }
@endphp

{!! $tableRankingHtml !!}
