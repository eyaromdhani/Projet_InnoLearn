<?php

namespace App\Service;

use App\Entity\InscritEvent;
use Nucleos\DompdfBundle\Factory\DompdfFactoryInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Twig\Environment;

class PdfService
{
    public function __construct(
        private DompdfFactoryInterface $dompdfFactory,
        private Environment $twig,
        private HttpClientInterface $httpClient
    ) {}

    public function generateEventTicket(InscritEvent $inscription): string
    {
        $location = $inscription->getEvent()->getLieu() ?: 'En ligne';
        $mapsUrl = "https://www.google.com/maps/search/?api=1&query=" . urlencode($location);
        
        $qrCodeData = '';
        try {
            // Using HttpClient with a timeout to prevent blocking indefinitely
            // format=svg is better as it scales and usually doesn't need GD in the PDF renderer
            $qrUrl = "http://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($mapsUrl) . "&format=svg";
            
            $response = $this->httpClient->request('GET', $qrUrl, [
                'timeout' => 5,
                'verify_peer' => false, // Some environments have issues with SSL certs
            ]);

            if ($response->getStatusCode() === 200) {
                $qrCodeData = 'data:image/svg+xml;base64,' . base64_encode($response->getContent());
            }
        } catch (\Exception $e) {
            // Fallback: the ticket will still be generated but without QR code
        }

        $html = $this->twig->render('event/ticket.html.twig', [
            'inscription' => $inscription,
            'qrCodeSvg' => $qrCodeData,
            'mapsUrl' => $mapsUrl
        ]);

        $dompdf = $this->dompdfFactory->create([
            'isRemoteEnabled' => true,
            'defaultPaperSize' => 'A4',
            'isHtml5ParserEnabled' => true,
        ]);
        
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->output();
    }
}
