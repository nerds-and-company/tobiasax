<?php

namespace Craft;

use Exception;

/**
 * Tobias AX Income service
 */
class TobiasAx_IncomeService extends BaseApplicationComponent
{
    /**
     * @var string
     */
    const ERROR_UPDATE_UNKNOWN = 'Unknown error occurred while updating an income: ';

    /**
     * @param TobiasAx_IncomeModel[] $incomes
     * @return TobiasAx_IncomeModel[]
     * @throws TobiasAx_SoapException $e
     */
    public function updateIncomes($incomes)
    {
        $responseIncomes = array();
        foreach ($incomes as $income) {
            if (!empty($income->Id)) {
                $responseIncome = $this->updateIncome($income);
                $responseIncomes[] = $responseIncome;
            }
        }

        return $responseIncomes;
    }

    /**
     * @param TobiasAx_IncomeModel $income
     * @return TobiasAx_IncomeModel
     * @throws TobiasAx_SoapException
     */
    public function updateIncome($income)
    {
        $service = craft()->tobiasAx_incomeConnector;

        try {
            $envelope = $service->sendRequest($service->updateIncome($income));
            $result = $service->extractSingle($envelope, 'Body/xmlns:UpdateIncomeResponse/xmlns:UpdateIncomeResult');
        } catch (Exception $e) {
            throw new TobiasAx_SoapException(static::ERROR_UPDATE_UNKNOWN . $e->getMessage(), null, $e);
        }

        return new TobiasAx_IncomeModel($result);
    }
}
