<?php
// app/Libraries/GeminiService.php
namespace App\Libraries;
use Gemini;
use GuzzleHttp\Client as GuzzleClient;
class GeminiService
{
    protected $apiKey;
    protected $client;
	protected $guzzle;
    protected $baseUrl = 'generativelanguage.googleapis.com';

    public function __construct(){
		$this->apiKey = env('gemini.api_key');
		 if(!$this->apiKey){
            throw new \Exception('Gemini API key not set in .env');
        }
		
		$this->guzzle = new GuzzleClient([
            'verify' => false,  // Disables cert check (use ONLY locally!)
            'timeout' => 60,
        ]);
		
		$this->client = Gemini::factory()
            ->withApiKey($this->apiKey)
            ->withHttpClient($this->guzzle)  // Inject the custom client
            ->make(); 
	}
	
	 public function generateText(string $prompt,string $model = 'gemini-2.5-flash-lite'){
        $response = $this->client
            ->generativeModel($model) // Note: Use string model names (no enum in v2.0+)
            ->generateContent($prompt);
        return $response->text();
    }
}