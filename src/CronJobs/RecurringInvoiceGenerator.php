<?php

namespace Kalakotra\Baudigit\CronJobs;

use Kalakotra\Baudigit\Models\RecurringInvoice;
use SilverStripe\Control\Director;
use SilverStripe\Core\Environment;
use SilverStripe\Control\Controller;
use SilverStripe\Security\Permission;
use SilverStripe\Security\PermissionProvider;

class RecurringInvoiceGenerator extends Controller implements PermissionProvider
{

    private static $allowed_actions = [
		'index' => '->canAccess'
	];

	public function index(){
		$this->generateRecurringInvoices();

		return 'GENERATOR PROCESS COMPLETED';
	}
    public function canAccess()
	{
		$token = $this->getRequest()->getVar('token');
		$validToken = Environment::getEnv('INVOICE_CRONJOB_TOKEN');
		if ($token && $validToken && hash_equals($validToken, $token)) {
			return true;
		}
		
		if(Director::is_cli() || Permission::check('GENERATE_RECURRING_INVOICES')){
			return true;
		} else {
			return false;
		}
	}

    public function generateRecurringInvoices()
    {
        // Logic to generate recurring invoices
        // get all active recurring invoices
        $recurringInvoices = RecurringInvoice::get()->filterByCallback(function($item) {
            return $item->checkIfInvoiceDue() == 1; // only active
        });

        if (!$recurringInvoices || $recurringInvoices->count() == 0) {
            echo "No active recurring invoices found.\n";
            return;
        } else {
            echo "Found " . $recurringInvoices->count() . " active recurring invoices.\n";
        }

        // loop through each recurring invoice and check if an invoice needs to be generated
        foreach ($recurringInvoices as $recurringInvoice) {
                // generate invoice
            if ($newInvoice = $recurringInvoice->generateInvoiceFromRecurring()) {
                echo "Generated invoice ID: " . $newInvoice->ID . " from Recurring Invoice ID: " . $recurringInvoice->ID . "\n";
                $recurringInvoice->Invoices()->add($newInvoice);
                $recurringInvoice->write();
            }
        }
    }
    
    public function providePermissions()
    {
        return [
            'GENERATE_RECURRING_INVOICES' => 'Generate Recurring Invoices',
        ];
    }
}