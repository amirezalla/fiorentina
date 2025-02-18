@php
    use GuzzleHttp\Client;
    use Symfony\Component\DomCrawler\Crawler;
    use Illuminate\Http\Request;
@endphp

@php
    // Create an HTTP client instance.
    $client = new Client();

    // Fetch the remote page.
    $response = $client->get('https://www.diretta.it/calcio/italia/primavera-1/classifiche/#/6NcAZJet/table/overall');
    $html = $response->getBody()->getContents();

    // Use DomCrawler to parse the HTML.
    $crawler = new Crawler($html);

    // Try to extract the element with the class .table_ranking.
    try {
        $tableRankingHtml = $crawler->filter('.tournament-table-standings')->html();
    } catch (\Exception $e) {
        $tableRankingHtml = '<p>Ranking table not found.</p>';
    }
@endphp

{!! $tableRankingHtml !!}
