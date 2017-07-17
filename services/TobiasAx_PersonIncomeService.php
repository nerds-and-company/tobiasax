<?php

namespace Craft;

use Exception;

/**
 * Tobias AX Income service
 */
class TobiasAx_PersonIncomeService extends BaseApplicationComponent
{
    /**
     * @var string
     */
    const EXCEPTION_ALREADY_EXISTS = "De record bestaat al";

    /**
     * @var string
     */
    const ERROR_ALREADY_EXISTS = "Income already exists";

    /**
     * @var string
     */
    const ERROR_CREATE_UNKNOWN = "Unknown error creating income: ";

    /**
     * @param TobiasAx_IncomeModel[] $incomes The incomes to add to the person
     * @param string $personId The id of the person to add the address to
     * @return TobiasAx_incomeModel[]
     * @throws TobiasAx_SoapException $e Thrown when a person income can't be created
     */
    public function createPersonIncomes($incomes, $personId)
    {
        $responseIncomes = array();
        foreach ($incomes as $income) {
            $responseIncome = $this->createPersonIncome($income, $personId);
            $responseIncomes[] = $responseIncome;
        }

        return $responseIncomes;
    }

    /**
     * @param TobiasAx_IncomeModel $income The income to add to the person
     * @param string $personId The id of the person to add the address to
     * @return TobiasAx_incomeModel
     * @throws TobiasAx_SoapException $e Thrown when a person income can't be created
     */
    public function createPersonIncome($income, $personId)
    {
        $service = craft()->tobiasAx_personConnector;

        try {
            $envelope = $service->sendRequest($service->createPersonIncome($income, $personId));
            $result = $service->extractSingle($envelope, 'Body/xmlns:CreatePersonIncomeResponse/xmlns:CreatePersonIncomeResult');
        } catch (TobiasAx_SoapException $e) {
            if (stristr($e->getMessage(), static::EXCEPTION_ALREADY_EXISTS)) {
                throw new TobiasAx_SoapException(static::ERROR_ALREADY_EXISTS, null, $e);
            }
            throw new TobiasAx_SoapException(static::ERROR_CREATE_UNKNOWN . $e->getMessage(), null, $e);
        } catch (Exception $e) {
            throw new TobiasAx_SoapException(static::ERROR_CREATE_UNKNOWN . $e->getMessage(), null, $e);
        }

        return new TobiasAx_IncomeModel($result);
    }
}
