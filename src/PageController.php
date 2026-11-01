<?php

declare(strict_types=1);

use Mpdf\Mpdf;
use Kalakotra\Baudigit\Pages\ProjectPage;
use SilverStripe\Core\ClassInfo;
use SilverStripe\Core\TempFolder;
use SilverStripe\Security\Member;
use SilverStripe\Security\Security;
use SilverStripe\Control\Director;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\CMS\Controllers\ContentController;

class PageController extends ContentController
{
    private static array $allowed_actions = [
        'getByClassJson',
    ];

    private static array $url_handlers = [
        '-/$className!/$searchTerm!' => 'getByClassJson',
    ];

    protected function init(): void
    {
        parent::init();
    }

    // ─── Auth ────────────────────────────────────────────────────────────────────

    public function currentUser(): ?Member
    {
        return Security::getCurrentUser();
    }

    // ─── Resource ID helpers ─────────────────────────────────────────────────────

    /**
     * Primarni ID resursa iz URL segmenta (url_handlers param 'ID').
     * Zamjenjuje stari ?e= GET pattern.
     */
    protected function resourceID(): ?int
    {
        $id = $this->getRequest()->param('ID');
        return ($id && is_numeric($id)) ? (int) $id : null;
    }

    /**
     * Sekundarni ID iz URL segmenta (url_handlers param 'SubID').
     * Zamjenjuje stare ?em=, ?ed=, ?et=, ?el= GET patterne.
     */
    protected function subResourceID(): ?int
    {
        $id = $this->getRequest()->param('SubID');
        return ($id && is_numeric($id)) ? (int) $id : null;
    }

    /**
     * Backwards-compat helper — čita iz URL param ili GET var.
     * Koristi se samo u mjestima koja još nisu migrirana.
     * @deprecated Koristiti resourceID() ili subResourceID()
     */
    public function checkEdit()
    {
        $id = $this->getRequest()->param('ID')
            ?: $this->getRequest()->getVar('e')
            ?: $this->getRequest()->postVar('e');
        return $id ?: false;
    }

    /**
     * Template helper: true when the current action is 'add'.
     * Replaces the old $checkAdd / ?a=1 GET-var pattern.
     */
    public function isAddAction(): bool
    {
        return $this->getAction() === 'add';
    }

    // ─── System checks ───────────────────────────────────────────────────────────

    public function hasProjectModule(): bool
    {
        return ProjectPage::get()->count() > 0;
    }

    public function checkActiveTab(): ?string
    {
        return $this->getRequest()->getVar('activetab') ?: null;
    }

    // ─── JSON autocomplete endpoint ───────────────────────────────────────────────

    public function getByClassJson()
    {
        $className  = $this->getRequest()->param('className');
        $searchTerm = $this->getRequest()->param('searchTerm');

        if (!$className) {
            return $this->jsonResponse([]);
        }

        try {
            $class = ClassInfo::class_name(urldecode($className));
        } catch (\Exception $e) {
            return $this->jsonResponse([]);
        }

        if (!$class) {
            return $this->jsonResponse([]);
        }

        $list = $class::get()->limit(20);
        if ($searchTerm) {
            $list = $list->filterAny(['Title:PartialMatch' => urldecode($searchTerm)]);
        }

        return $this->jsonResponse($list->map('ID', 'Title')->toArray());
    }

    protected function jsonResponse(array $data)
    {
        $response = $this->getResponse();
        $response->addHeader('Content-Type', 'application/json');
        $response->setBody(json_encode($data));
        return $response;
    }

    // ─── PDF ─────────────────────────────────────────────────────────────────────

    public function generatePDF(string $html, string $pdfName = 'document'): string
    {
        $config = SiteConfig::current_site_config();

        $header = (string) $this->customise([
            'CompanyName'         => $config->CompanyName,
            'CompanyAddressLine1' => $config->CompanyAddressLine1,
            'CompanyContactPhone' => $config->CompanyContactPhone,
        ])->renderWith('app/Pages/Layout/PDF/FirstHeader');

        $footer = (string) $this->customise([
            'CompanyName'         => $config->CompanyName,
            'CompanyAddressLine1' => $config->CompanyAddressLine1,
            'CompanyContactPhone' => $config->CompanyContactPhone,
        ])->renderWith('app/Pages/Layout/PDF/Footer');

        $mpdf = new Mpdf([
            'mode'          => 'utf-8',
            'format'        => [210, 297],
            'margin_top'    => 42,
            'margin_bottom' => 26,
        ]);

        $mpdf->SetHTMLHeader($header);
        $mpdf->WriteHTML(' ');
        $mpdf->SetHTMLFooter($footer);
        $mpdf->WriteHTML($html);

        $path = TempFolder::getTempFolder(Director::baseFolder()) . '/' . $pdfName;
        $mpdf->Output($path, 'F');

        return $path;
    }

    // ─── Session notifications ───────────────────────────────────────────────────

    public function getNotifySessionData()
    {
        $session    = $this->getRequest()->getSession();
        $notifyData = $session->get('NotifyData');
        $session->clear('NotifyData');
        return $notifyData ? $this->customise($notifyData) : [];
    }

    public function setNotifySessionData(string $type, string $icon, string $message): void
    {
        $this->getRequest()->getSession()->set('NotifyData', [
            'type'    => $type,
            'icon'    => $icon,
            'message' => $message,
        ]);
    }
}
