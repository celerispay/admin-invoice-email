<?php

namespace Boostsales\AdminInvoiceEmail\Controller\Adminhtml\Orders;
use Magento\Framework\Controller\ResultFactory;

class SendEmail extends \Magento\Backend\App\Action
{

    protected $resultForwardFactory;

    public function __construct(
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Backend\App\Action\Context $context
    )

    {
        $this->resultForwardFactory = $resultForwardFactory;
        parent::__construct($context);
    }


    public function execute()
    {
        $invoiceIds = $this->getRequest()->getPost('selected', array());
        $countInvoiceEmail = 0;
        $countNonInvoiceEmail = 0;

        foreach($invoiceIds as $invoiceId){
            if(!$invoiceId){
                $countNonInvoiceEmail++;
            }
            if($invoiceId){
                $invoice = $this->_objectManager->create(\Magento\Sales\Api\InvoiceRepositoryInterface::class)->get($invoiceId);
                if (!$invoice) {
                    $countNonInvoiceEmail++;
                }else{
                    $this->_objectManager->create(
                        \Magento\Sales\Api\InvoiceManagementInterface::class
                    )->notify($invoice->getEntityId());
                    $countInvoiceEmail++;
                }
            }
        }

        if($countInvoiceEmail){
            $this->messageManager->addSuccessMessage(__('You sent %1 Emails ',$countInvoiceEmail));
        }
        if($countNonInvoiceEmail){
            $this->messageManager->addError(__('%1 emails were not sent', $countNonInvoiceEmail));
        }

        $resultRedirect = $this->resultRedirectFactory->create([ResultFactory::TYPE_REDIRECT]);
        return $resultRedirect->setPath('sales/invoice/index');
    }
}
