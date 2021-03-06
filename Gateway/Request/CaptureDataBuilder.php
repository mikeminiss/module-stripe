<?php
/**
 * Pmclain_Stripe extension
 * NOTICE OF LICENSE
 *
 * This source file is subject to the OSL 3.0 License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 *
 * @category  Pmclain
 * @package   Pmclain_Stripe
 * @copyright Copyright (c) 2017-2018
 * @license   Open Software License (OSL 3.0)
 */

namespace Pmclain\Stripe\Gateway\Request;

use Pmclain\Stripe\Gateway\Request\PaymentDataBuilder;
use Magento\Framework\Exception\LocalizedException;
use Pmclain\Stripe\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Pmclain\Stripe\Helper\Payment\Formatter;

class CaptureDataBuilder implements BuilderInterface
{
    use Formatter;

    const TRANSACTION_ID = 'transaction_id';

    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * CaptureDataBuilder constructor.
     * @param SubjectReader $subjectReader
     */
    public function __construct(
        SubjectReader $subjectReader
    ) {
        $this->subjectReader = $subjectReader;
    }

    /**
     * @param array $subject
     * @return array
     * @throws LocalizedException
     */
    public function build(array $subject)
    {
        $paymentDataObject = $this->subjectReader->readPayment($subject);
        $payment = $paymentDataObject->getPayment();
        $transactionId = $payment->getCcTransId();

        if (!$transactionId) {
            throw new LocalizedException(__('No Authorization Transaction to capture'));
        }

        return [
            self::TRANSACTION_ID => $transactionId,
            PaymentDataBuilder::AMOUNT => $this->formatPrice($this->subjectReader->readAmount($subject))
        ];
    }
}
