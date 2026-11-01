<?php

declare(strict_types=1);

namespace Kalakotra\Baudigit\Controllers;

use Kalakotra\Baudigit\Models\Article;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\HiddenField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\Validation\RequiredFieldsValidator;

class ArticlePageController extends \PageController
{
    use \Kalakotra\Baudigit\Traits\FeatureToggleTrait;

    protected function init(): void
    {
        parent::init();
        if ($err = $this->checkEnabled()) {
            return;
        }
    }
    private static array $allowed_actions = [
        'add',
        'doAdd',
        'edit',
        'doEdit',
    ];

    private static array $url_handlers = [
        'add'       => 'add',
        'edit/$ID!' => 'edit',
    ];

    // ─── Template helpers ─────────────────────────────────────────────────────────

    public function getSummaryFields(): array
    {
        return Article::singleton()->summaryFields();
    }

    public function readAllInputs()
    {
        return Article::get();
    }

    // ─── Forms ───────────────────────────────────────────────────────────────────

    private function buildFormFields(): FieldList
    {
        return FieldList::create(
            TextField::create('InternalID', 'Interne ID')->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control'),
            TextField::create('Title', 'Titel')->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control'),
            DropdownField::create('Unit', 'Einheit', Article::singleton()->getUnitOptions())->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-select'),
            TextField::create('BuyPriceNetto', 'Einkaufspreis netto')->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control'),
            TextField::create('SellPriceNetto', 'Verkaufspreis netto')->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control'),
            TextField::create('Description', 'Beschreibung')->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control'),
        );
    }

    public function add()
    {
        $form = Form::create(
            $this, 'add',
            $this->buildFormFields(),
            FieldList::create(FormAction::create('doAdd', 'Hinzufügen')->addExtraClass('btn btn-sm btn-success px-4')),
            RequiredFieldsValidator::create(['Title', 'Unit', 'SellPriceNetto'])
        );
        $form->setFormAction($this->Link('add'));
        return $form;
    }

    public function doAdd(array $data, Form $form)
    {
        $article = Article::create();
        $form->saveInto($article);
        $article->write();

        $this->setNotifySessionData('success', 'bi bi-check2-circle', 'Artikel erfolgreich gespeichert.');
        return $this->redirect($this->Link('edit/' . $article->ID));
    }

    public function edit()
    {
        $id = $this->resourceID();
        if (!$id) {
            return $this->httpError(400, 'Ungültige ID.');
        }

        $article = Article::get()->byID($id);
        if (!$article || !$article->exists()) {
            return $this->httpError(404, 'Artikel nicht gefunden.');
        }

        $fields = $this->buildFormFields();
        $fields->push(HiddenField::create('ArticleID', 'ID')->setValue($article->ID));

        $form = Form::create(
            $this, 'edit', $fields,
            FieldList::create(FormAction::create('doEdit', 'Speichern')->addExtraClass('btn btn-sm btn-success px-4')),
            RequiredFieldsValidator::create(['Title', 'Unit', 'SellPriceNetto'])
        );
        $form->loadDataFrom($article);
        $form->setFormAction($this->Link('edit/' . $article->ID));

        return $form;
    }

    public function doEdit(array $data, Form $form)
    {
        $id      = isset($data['ArticleID']) && is_numeric($data['ArticleID']) ? (int) $data['ArticleID'] : null;
        $article = $id ? Article::get()->byID($id) : null;

        if ($article && $article->exists()) {
            $form->saveInto($article);
            $article->write();

            $this->setNotifySessionData('success', 'bi bi-check2-circle', 'Artikel erfolgreich gespeichert.');
            return $this->redirectBack();
        }

        $this->setNotifySessionData('danger', 'bi bi-exclamation-triangle', 'Artikel nicht gefunden.');
        return $this->redirectBack();
    }
}
